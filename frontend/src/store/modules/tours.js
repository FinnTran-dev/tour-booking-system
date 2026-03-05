import tourService from '@/services/tourService';

const state = () => ({
    tours: [],
    currentTour: null,
    pagination: null,
    loading: false,
    error: null,
});

const getters = {
    tours: (state) => state.tours,
    currentTour: (state) => state.currentTour,
    pagination: (state) => state.pagination,
    isLoading: (state) => state.loading,
    error: (state) => state.error,
};

const mutations = {
    SET_TOURS(state, tours) {
        state.tours = tours;
    },
    SET_CURRENT_TOUR(state, tour) {
        state.currentTour = tour;
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
};

const actions = {
    async fetchTours({ commit }, params = {}) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await tourService.list(params);
            commit('SET_TOURS', response.data.data);
            commit('SET_PAGINATION', response.data.meta);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to fetch tours');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchTour({ commit }, id) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await tourService.get(id);
            commit('SET_CURRENT_TOUR', response.data.data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to fetch tour');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async createTour({ commit }, data) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await tourService.create(data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to create tour');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async updateTour({ commit }, { id, data }) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await tourService.update(id, data);
            commit('SET_CURRENT_TOUR', response.data.data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to update tour');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    clearCurrentTour({ commit }) {
        commit('SET_CURRENT_TOUR', null);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
