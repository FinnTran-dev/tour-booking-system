import bookingService from '@/services/bookingService';

const state = () => ({
    bookings: [],
    currentBooking: null,
    pagination: null,
    loading: false,
    error: null,
    validationErrors: {},
});

const getters = {
    bookings: (state) => state.bookings,
    currentBooking: (state) => state.currentBooking,
    pagination: (state) => state.pagination,
    isLoading: (state) => state.loading,
    error: (state) => state.error,
    validationErrors: (state) => state.validationErrors,
};

const mutations = {
    SET_BOOKINGS(state, bookings) {
        state.bookings = bookings;
    },
    SET_CURRENT_BOOKING(state, booking) {
        state.currentBooking = booking;
    },
    SET_PAGINATION(state, pagination) {
        state.pagination = pagination;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    SET_ERROR(state, error) {
        state.error = error;
    },
    SET_VALIDATION_ERRORS(state, errors) {
        state.validationErrors = errors;
    },
    CLEAR_ERRORS(state) {
        state.error = null;
        state.validationErrors = {};
    },
};

const actions = {
    async fetchBookings({ commit }, params = {}) {
        commit('SET_LOADING', true);
        commit('CLEAR_ERRORS');
        try {
            const response = await bookingService.list(params);
            commit('SET_BOOKINGS', response.data.data);
            commit('SET_PAGINATION', response.data.meta);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to fetch bookings');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchBooking({ commit }, id) {
        commit('SET_LOADING', true);
        commit('CLEAR_ERRORS');
        try {
            const response = await bookingService.get(id);
            commit('SET_CURRENT_BOOKING', response.data.data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to fetch booking');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async createBooking({ commit }, data) {
        commit('SET_LOADING', true);
        commit('CLEAR_ERRORS');
        try {
            const response = await bookingService.create(data);
            return response;
        } catch (error) {
            if (error.apiErrors) {
                commit('SET_VALIDATION_ERRORS', error.apiErrors);
            }
            commit('SET_ERROR', error.apiMessage || 'Failed to create booking');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async updateBooking({ commit }, { id, data }) {
        commit('SET_LOADING', true);
        commit('CLEAR_ERRORS');
        try {
            const response = await bookingService.update(id, data);
            commit('SET_CURRENT_BOOKING', response.data.data);
            return response;
        } catch (error) {
            if (error.apiErrors) {
                commit('SET_VALIDATION_ERRORS', error.apiErrors);
            }
            commit('SET_ERROR', error.apiMessage || 'Failed to update booking');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    clearCurrentBooking({ commit }) {
        commit('SET_CURRENT_BOOKING', null);
        commit('CLEAR_ERRORS');
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
