import apiClient from './apiClient';

export default {
    list(params = {}) {
        return apiClient.get('/tours', { params });
    },
    get(id) {
        return apiClient.get(`/tours/${id}`);
    },
    create(data) {
        return apiClient.post('/tours', data);
    },
    update(id, data) {
        return apiClient.put(`/tours/${id}`, data);
    },
};
