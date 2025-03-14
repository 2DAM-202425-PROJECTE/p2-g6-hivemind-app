import { ref } from 'vue';
import apiClient from '@/axios.js';

export function useUser() {
  const userId = ref(null);

  const fetchUserId = async () => {
    try {
      const response = await apiClient.get('/api/user');
      userId.value = response.data.id;
    } catch (error) {
      console.error("Error al obtener usuario:", error);
    }
  };

  return { userId, fetchUserId };
}
