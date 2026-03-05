import Vue from 'vue';
import Vuex from 'vuex';
import tours from './modules/tours';
import bookings from './modules/bookings';
import passengers from './modules/passengers';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        tours,
        bookings,
        passengers,
    },
    strict: process.env.NODE_ENV !== 'production',
});
