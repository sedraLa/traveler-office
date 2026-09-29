# Travel Booking API

A Laravel 12 API for managing trips and traveler bookings. The system handles seat availability, booking creation, booking status transitions, and pricing for normal and VIP trips.

This is an API-only project — there are no Blade views or frontend pages. All responses are JSON.

---

## Features

- List trips and check seat availability
- View seats for a specific trip, including which are currently available
- Create bookings with automatic price calculation
- Confirm or cancel bookings
- Prevent duplicate active bookings for the same seat
- Reject seat/trip mismatches at the time of booking
- VIP trips apply a 20% markup over the base price
- Booking price is saved as a snapshot at the time of creation
- State-based booking transitions with proper error handling
- Consistent JSON error responses across the API

---


## Database Design

### Tables

**customers**
Stores traveler information. Each customer can have many bookings.
Fields: `id`, `name`, `phone`, `timestamps`

**trips**
Represents an intercity bus trip. Each trip has many seats and many bookings.
Fields: `id`, `from_city`, `to_city`, `departure_at`, `trip_type` (normal/vip), `total_seats`, `base_price`, `timestamps`

**seats**
Each seat belongs to one trip. A seat has a unique `seat_number` within its trip, enforced by a composite unique constraint on `(trip_id, seat_number)`.
Fields: `id`, `trip_id`, `seat_number`, `timestamps`

**bookings**
Records each booking attempt. A seat can have multiple booking records over time because a cancelled booking does not permanently block the seat — only `pending` and `confirmed` bookings are considered active.
Fields: `id`, `customer_id`, `trip_id`, `seat_id`, `status`, `price`, `timestamps`

### Relationships

```
Customer  →  many Bookings
Trip      →  many Seats  →  many Bookings
Seat      →  many Bookings
Booking   →  belongs to Customer, Trip, Seat
```

### Foreign Keys

All foreign keys use `restrictOnDelete`, meaning you cannot delete a customer, trip, or seat that has existing bookings. This protects booking history.

---

## API Endpoints

| Method  | Endpoint                              | Description                                     |
|---------|---------------------------------------|-------------------------------------------------|
| GET     | /api/trips                            | List all trips with available seat count        |
| GET     | /api/trips/{trip}                     | Get trip details with per-seat availability     |
| GET     | /api/trips/{trip}/available-seats     | List only the currently available seats         |
| POST    | /api/bookings                         | Create a new booking                            |
| GET     | /api/bookings/{booking}               | Get booking details with customer, trip, seat   |
| PATCH   | /api/bookings/{booking}/confirm       | Confirm a pending booking                       |
| PATCH   | /api/bookings/{booking}/cancel        | Cancel a pending or confirmed booking           |

---

## Booking Lifecycle

Bookings start in `pending` status and can transition as follows:

```
pending  →  confirmed
pending  →  cancelled
confirmed  →  cancelled
```

The following transitions are invalid:

```
confirmed  →  confirmed
cancelled  →  confirmed
cancelled  →  cancelled
```

---

## Business Rules

- **Seat/trip validation**: The selected seat must belong to the selected trip. If not, the request is rejected.
- **Active booking check**: A seat cannot have more than one active (pending or confirmed) booking for the same trip at a time.
- **Cancelled bookings**: A cancelled booking does not block the seat. A new booking can be created on the same seat after cancellation.
- **Pricing**:
  - Normal trip → booking price = `base_price`
  - VIP trip → booking price = `base_price × 1.20`
- **Price snapshot**: The calculated price is stored directly on the booking at creation time. It does not change if the trip's base price changes later.
- **Concurrency protection**: Booking creation runs inside a database transaction with `lockForUpdate()` on the seat row. This helps prevent two simultaneous requests from booking the same seat.

---

## Design Patterns

### Strategy Pattern — Pricing

Pricing behavior is handled through the `TicketPricingStrategy` interface, with two implementations:

- `NormalPricingStrategy` — returns the base price unchanged
- `VipPricingStrategy` — returns `base_price × 1.20`

`NormalBookingService` and `VipBookingService` each receive a `TicketPricingStrategy` through constructor injection. `BookingService` then calls the appropriate one based on the trip's `trip_type`.

### State Pattern — Booking Status

Booking state transitions are managed through:

- `BookingState` (interface) — defines `confirm()`, `cancel()`, and `status()`
- `PendingState` — allows confirm and cancel
- `ConfirmedState` — allows cancel only; confirm throws `InvalidBookingStateException`
- `CancelledState` — no transitions allowed; both throw `InvalidBookingStateException`

`BookingService` resolves the current state from `BookingStatus`, calls the transition method, and saves the new status.

### Service Layer

`BookingService` holds all booking business logic:
- `createBooking()` — validates, locks, checks duplicates, calculates price, creates the record
- `confirm()` — delegates to the State Pattern
- `cancel()` — delegates to the State Pattern

### Service Container and Dependency Injection

Bindings are registered in `AppServiceProvider`:

```php
// Default binding — resolves TicketPricingStrategy to NormalPricingStrategy globally
$this->app->bind(TicketPricingStrategy::class, NormalPricingStrategy::class);

// Contextual bindings — each service gets the correct strategy
$this->app->when(NormalBookingService::class)
    ->needs(TicketPricingStrategy::class)
    ->give(NormalPricingStrategy::class);

$this->app->when(VipBookingService::class)
    ->needs(TicketPricingStrategy::class)
    ->give(VipPricingStrategy::class);
```

This is useful because `NormalBookingService` and `VipBookingService` both depend on `TicketPricingStrategy`, but each needs a different implementation injected automatically.

---

## Error Handling

All API errors return JSON. The following responses are configured in `bootstrap/app.php`:

| Status | Trigger                                         | Message                                  |
|--------|-------------------------------------------------|------------------------------------------|
| 404    | Resource not found (model binding)              | `Not Found.`                             |
| 409    | Seat already has an active booking              | `Seat is already booked.`                |
| 409    | Invalid booking state transition                | `Invalid booking state transition.`      |
| 422    | Seat does not belong to the selected trip       | `Seat does not belong to this trip.`     |
| 422    | Validation errors (missing or invalid fields)   | Laravel default validation error format  |
| 500    | Unexpected server errors                        | `Server Error`                           |
