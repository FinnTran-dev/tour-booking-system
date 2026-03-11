import apiClient from './apiClient';

export default {
    list(params = {}) {
        return apiClient.get('/passengers', { params });
    },
    get(id) {
        return apiClient.get(`/passengers/${id}`);
    },
    create(data) {
        return apiClient.post('/passengers', data);
    },
    update(id, data) {
        return apiClient.put(`/passengers/${id}`, data);
    },
    delete(id) {
        return apiClient.delete(`/passengers/${id}`);
    },
};
