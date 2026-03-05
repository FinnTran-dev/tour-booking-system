import apiClient from './apiClient';

export default {
    list(params = {}) {
        return apiClient.get('/bookings', { params });
    },
    get(id) {
        return apiClient.get(`/bookings/${id}`);
    },
    create(data) {
        return apiClient.post('/bookings', data);
    },
    update(id, data) {
        return apiClient.put(`/bookings/${id}`, data);
    },
};
