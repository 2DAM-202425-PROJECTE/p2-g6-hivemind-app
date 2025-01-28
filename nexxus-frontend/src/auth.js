import apiClient from "./axios";

export const setAuthToken = (token) => {
    apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
};

export const clearAuthToken = () => {
    delete apiClient.defaults.headers.common['Authorization'];
};

