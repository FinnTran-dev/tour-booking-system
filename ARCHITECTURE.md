# Part 4 - Architectural Explanation

Here's a breakdown of how I structured the application and the logic behind it.

## Folder Structure & "Why"

I'm a big believer in the Single Responsibility Principle, so you won't find any bulky controllers here.

**Backend (`backend/app/`):**
- **`Services/`**: This is the heart of the app. `BookingService.php` and `TourService.php` contain all the actual business rules (like ensuring a tour is public before booking, or tying an invoice to a booking). The controllers just pass data here.
- **`Http/Requests/`**: I hate putting validation in controllers. Using FormRequests (like `StoreBookingRequest`) means the data is clean and format-perfect before my Service classes even see it.
- **`Http/Resources/`**: These transformers ensure API consumers always get exactly the shape of JSON they expect, decoupling our DB schema from the frontend payload.
- **`Exceptions/`**: I created a custom `BookingException`. Instead of dropping random 400 or 500 errors in the middle of a service, I throw this exception, and the controller elegantly returns a nice error message to the user.

**Frontend (`frontend/src/`):**
- **`services/`**: My Axios instances live here (`apiClient.js`). It allows me to intercept all responses and normalize errors globally.
- **`store/modules/`**: I split Vuex into namespaced modules (`tours`, `bookings`). Components should just dispatch actions and read getters, not worry about HTTP requests.

---

## Killing N+1 Queries

N+1 issues are the silent killers of API performance. Here is how I dealt with them:
1. **Eager Loading in Services**: If you look at `TourService.php::getPublicTours()`, I don't just fetch tours. I use `->with(['tourDates' => ...])` to load the enabled dates in exactly two queries.
2. **Resource Protection**: In `TourResource.php` and `BookingResource.php`, I strictly use `$this->whenLoaded('relation')`. If a dev forgets to eager load a relation in the backend, the API resource just skips rendering it rather than lazy-loading and triggering 50 queries by accident.

---

## Transaction Boundaries (Atomic Bookings)

Creating a booking isn't just one insert. It involves the booking record, multiple dynamic passengers, the pivot table, and generating an invoice. If any step fails, we cannot have orphan records.

Look at `BookingService.php::createBooking()`. Everything happens inside a `DB::transaction()` block. 
If inserting the invoice fails at the very end, Laravel rolls back the entire transaction. The database stays perfectly clean, avoiding the nightmare of a user having a saved booking but no invoice.

---

## Scaling to 10,000 Bookings / Day

10,000 bookings a day is a fun challenge. The current architecture holds up, but here's how I'd evolve it for that scale:
1. **DB Split**: I'd set up read/write replicas. The heavy read traffic (the public facing `/tours` catalog) hits a Read-Replica. The transactions and writes go to the Master.
2. **Queues**: Right now, the Invoice is created synchronously. In the real world, generating a PDF invoice or sending confirmation emails should be pushed to a Redis Queue.
3. **Container Orchestration**: The Dockerized setup means we could throw this into AWS EKS (Kubernetes) and auto-scale the API pods during peak traffic hours.

---

## Production Readiness Checklist

Before pushing this to production, I'd want to add:
- **Caching**: The public tours list doesn't change every second. I'd slap a Redis cache on `TourService::getPublicTours()`, invalidating the cache tags only when an admin edits a tour.
- **Auth & Rate Limiting**: Implementing Laravel Sanctum and strict API throttling to stop bot scraping.
- **Observability**: Plugging in something like Datadog or Sentry so we know exactly when and why things break.
