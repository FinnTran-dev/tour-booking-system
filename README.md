# Tour Booking System - Senior Assessment

Hey there! This is my submission for the Senior Full Stack Developer Technical Assessment. 

I've built this Single Page Application (SPA) to manage tours, specific dates, and bookings. The goal was to prove architectural maturity, safe state management, and solid database design, rather than just slapping together a quick CRUD app. 

## What's under the hood?

I stuck to the requested stack:
- **API:** Laravel 10 (PHP 8.1+) - I know the prompt mentioned Laravel 6+, but I opted for 10 to show modern practices while keeping the core principles backward compatible.
- **Database:** MySQL 8.
- **Frontend:** Vue 2 & Vuex & Vue Router (since the current production system uses it, I wanted to show I can write clean code within the legacy constraints).
- **Tooling:** Webpack, Axios, and full Docker orchestration for easy evaluation.

## How to run this locally

The easiest way to check this out is via Docker. I've set up a `docker-compose.yml` that handles everything (MySQL, Laravel, and Vue).

1. **Clone it down:**
   git clone git@github.com:FinnTran-dev/tour-booking-system.git
   cd tour-booking-system

2. **Spin it up:**
   docker-compose up --build -d

3. **Check it out:**
   - The Vue SPA is running at: [http://localhost:3000](http://localhost:3000)
   - The Laravel API is running at: [http://localhost:8000](http://localhost:8000)

## Design Choices

I've put a lot of thought into keeping the controllers thin and moving the heavy lifting to Service classes (`TourService`, `BookingService`). This makes the code highly testable and reusable. Validation is strictly handled via FormRequests, and I'm using API Resources to control exactly what JSON payload goes out the door.

For a deep dive into the architecture, N+1 prevention, and how I handled atomic database transactions, grab a coffee and check out `ARCHITECTURE.md`.

For my thoughts on performance, scaling, and how I'd tackle refactoring your legacy monolith, please see `PERFORMANCE_LEGACY.md`.

Cheers!
