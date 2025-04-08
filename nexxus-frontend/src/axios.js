import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
  }
});

export default apiClient;
