import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
});

// Intercept responses to normalize errors
apiClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Standardize error payload
        let normalizedError = {
            status: error.response ? error.response.status : null,
            apiMessage: 'An unexpected error occurred. Please try again later.',
            apiErrors: null,
            raw: error,
        };

        if (error.response && error.response.data) {
            if (error.response.data.message) {
                normalizedError.apiMessage = error.response.data.message;
            }
            if (error.response.data.errors) {
                // Validation errors usually come in nested arrays, standardizing
                normalizedError.apiErrors = error.response.data.errors;
            }
        }

        return Promise.reject(normalizedError);
    }
);

export default apiClient;
