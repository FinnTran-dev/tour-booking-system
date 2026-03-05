import passengerService from '@/services/passengerService';

const state = () => ({
    passengers: [],
    loading: false,
    error: null,
});

const getters = {
    passengers: (state) => state.passengers,
    isLoading: (state) => state.loading,
    error: (state) => state.error,
};

const mutations = {
    SET_PASSENGERS(state, passengers) {
        state.passengers = passengers;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    SET_ERROR(state, error) {
        state.error = error;
    },
};

const actions = {
    async fetchPassengers({ commit }, params = {}) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await passengerService.list(params);
            commit('SET_PASSENGERS', response.data.data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to fetch passengers');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async createPassenger({ commit }, data) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await passengerService.create(data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to create passenger');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async updatePassenger({ commit }, { id, data }) {
        commit('SET_LOADING', true);
        commit('SET_ERROR', null);
        try {
            const response = await passengerService.update(id, data);
            return response;
        } catch (error) {
            commit('SET_ERROR', error.apiMessage || 'Failed to update passenger');
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
