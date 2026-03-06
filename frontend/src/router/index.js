import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        redirect: '/tours',
    },
    {
        path: '/tours',
        name: 'tours.index',
        component: () => import(/* webpackChunkName: "tours" */ '@/views/tours/ToursListView.vue'),
        meta: { title: 'Tours' },
    },
    {
        path: '/tours/create',
        name: 'tours.create',
        component: () => import(/* webpackChunkName: "tours" */ '@/views/tours/TourFormView.vue'),
        meta: { title: 'Create Tour' },
    },
    {
        path: '/tours/:id/edit',
        name: 'tours.edit',
        component: () => import(/* webpackChunkName: "tours" */ '@/views/tours/TourFormView.vue'),
        meta: { title: 'Edit Tour' },
    },
    {
        path: '/passengers',
        name: 'passengers.index',
        component: () => import(/* webpackChunkName: "passengers" */ '@/views/passengers/PassengersListView.vue'),
        meta: { title: 'Passengers' },
    },
    {
        path: '/bookings',
        name: 'bookings.index',
        component: () => import(/* webpackChunkName: "bookings" */ '@/views/bookings/BookingsListView.vue'),
        meta: { title: 'Bookings' },
    },
    {
        path: '/bookings/new',
        name: 'bookings.create',
        component: () => import(/* webpackChunkName: "bookings" */ '@/views/bookings/BookingFormView.vue'),
        meta: { title: 'New Booking' },
    },
    {
        path: '/bookings/:id/edit',
        name: 'bookings.edit',
        component: () => import(/* webpackChunkName: "bookings" */ '@/views/bookings/BookingFormView.vue'),
        meta: { title: 'Edit Booking' },
    },
];

const router = new VueRouter({
    mode: 'history',
    routes,
});

// Update page title on navigation
router.afterEach((to) => {
    document.title = to.meta.title
        ? `${to.meta.title} — Tour Booking System`
        : 'Tour Booking System';
});

export default router;
