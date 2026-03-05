# Part 5 & 6 - Performance, Scalability & Legacy Refactoring

Here are my thoughts on handling performance bottlenecks and safely moving away from your legacy Laravel 6/Vue 2 monolith.

---

## Part 5: Performance & Scalability

### 1. Where do we need indexes?
Databases fall apart without good indexes. Here's where I'd enforce them:
- **Foreign Keys:** `tour_id`, `tour_date_id`. If we want to look up all bookings for a specific tour, this is mandatory.
- **Status Columns:** Enums like `tours.status` and `tour_dates.status`. Filtering thousands of drafts out to only show 'Public' tours would cause full table scans otherwise.
- **Unique Constraints:** I added a composite unique index on `[booking_id, passenger_id]` in the pivot table so the DB engine physically rejects duplicate attachments.

### 2. Preventing Double Bookings
If a tour date only has 20 spots, what happens if 25 people click "Book" at the exact same millisecond?
**My approach:** 
I'd use Pessimistic Locking inside my DB transaction. By calling `TourDate::where('id', $id)->lockForUpdate()->first()`, MySQL places a row-level lock. Person A gets the lock, decreases the available spots. Person B's query physically waits until Person A's transaction commits. If spots hit 0, Person B gets a standard "Sorry, fully booked" exception.

### 3. Caching Approach
I would use Redis to cache the main `/api/v1/tours` endpoint. 
Cache keys would look like `tours_page_1`. 
The catch is invalidation. I'd use Laravel Eloquent Observers. Whenever a `Tour` model is `saved`, `updated`, or `deleted` by an admin, I'd trigger a `Cache::tags(['tours'])->flush()` to wipe the catalog and repopulate it on the next user hit.

### 4. Handling Soft Deletes
We absolutely cannot hard-delete tours or bookings, because historical financial data (invoices) depends on them.
I'd add Laravel's `SoftDeletes` trait to the Models and `$table->softDeletes()` to the migrations. It transparently adds `deleted_at`, ensuring old bookings stay intact in the DB but vanish from normal application queries.

---

## Part 6: Legacy Refactoring & Migration Strategy

Tackling a production monolith running Laravel 6 and Vue 2 is tricky, but totally manageable if done iteratively. I would avoid a "big bang rewrite" at all costs.

### How I'd refactor safely and incrementally
I prefer the **Strangler Fig pattern**. We don't stop the world to rewrite. Instead, I'd pick one bounded context (e.g., Passenger Management). I'd write tests for the existing legacy behavior, build a new Service layer alongside the old code, and switch the route over. Once stable, move to the next piece.

### Introducing Service Layers without breaking prod
If `BookingController@store` is a 400-line monster:
1. Copy the logic verbatim into a new `LegacyBookingService`.
2. Inject that service into the controller. Everything still works.
3. Start breaking down `LegacyBookingService` into cleaner private methods and modern validation. This keeps the API contract identical for the production Vue frontend.

### Eliminating N+1 safely
You can't just blindly add `->with()` everywhere because some legacy directus endpoints might rely on lazy-loaded payloads.
Instead, I'd use Laravel Debugbar to audit the queries. If I find a loop causing N+1, I'll eager load the relation *just before the loop* using `$collection->load('relation')`. It fixes the DB load but changes absolutely nothing about the JSON output structure.

### Injecting DB Transactions live
Find the scattered `Booking::create` and `Invoice::create` methods, and wrap them in `DB::transaction()`. The main risk here is that legacy code might have hidden API calls (like sending an email) stuck between DB inserts. I would extract those side-effects out to be dispatched *after* the transaction commits (`DB::afterCommit()`).

### The Path to Laravel 10 and Vue 3
**Backend:** Upgrade iteratively. Laravel 6 -> 7 -> 8 -> 9 -> 10. Fix deprecations at each step. Don't skip numbers.
**Frontend:** I'd use Vue's Migration Build (`@vue/compat`). It lets Vue 2 and Vue 3 code live side-by-side. I'd rewrite heavy components into the Composition API one by one, eventually tossing Vuex for Pinia, and swapping Webpack for Vite once the syntax is fully modern.

### My Risk Mitigation Strategy
- **Feature Toggles:** Wrap the new decoupled architecture in configurations. If my new `BookingService` crashes in prod, an env variable flip instantly routes traffic back to the legacy controller block.
- **Shadow Deployments:** Route a copy of read-traffic into the new endpoints and diff the JSON payloads to guarantee 100% parity before the actual switch.
