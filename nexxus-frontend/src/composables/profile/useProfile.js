import { ref } from 'vue';
import axios from "@/axios.js";

export function useProfile() {
  const user = ref({
    name: '',
    username: '',
    level: 0,
    description: '',
    profile_photo_url: '',
    banner_photo_url: '',
    stories: [],
    posts: 0,
    followers: 0,
    following: 0,
  });

  const currentUser = ref(null);
  const userPosts = ref([]);
  const isCurrentUser = ref(false);

  const fetchUser = async () => {
    try {
      const response = await axios.get('/api/user');
      console.log('fetchUser response:', response.data);
      currentUser.value = response.data;
      checkIsCurrentUser(); // Verifica después de actualizar currentUser
    } catch (error) {
      console.error('Error en fetchUser:', error);
    }
  };

  const fetchUserProfileByUsername = async (username) => {
    try {
      const response = await axios.get(`/api/user/${username}`);
      console.log('fetchUserProfileByUsername response:', response.data);
      user.value = response.data.user; // Actualiza user primero
      userPosts.value = response.data.posts || [];
      checkIsCurrentUser(); // Luego verifica
    } catch (error) {
      console.error('Error en fetchUserProfileByUsername:', error);
    }
  };

  const checkIsCurrentUser = () => {
    if (currentUser.value && user.value.username) { // Asegúrate de que ambos existan
      console.log('Comparando:', currentUser.value.username, 'vs', user.value.username);
      isCurrentUser.value = currentUser.value.username === user.value.username;
    } else {
      console.log('Falta currentUser o user.username:', currentUser.value, user.value.username);
      isCurrentUser.value = false;
    }
  };

  return {
    user,
    userPosts,
    isCurrentUser,
    fetchUser,
    fetchUserProfileByUsername,
    checkIsCurrentUser,
  };
}
