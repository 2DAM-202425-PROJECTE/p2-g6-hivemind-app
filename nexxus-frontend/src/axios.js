import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://localhost:8000', //url backend laravel
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

export default apiClient;