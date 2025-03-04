// nexxus-frontend/src/composables/profile/useProfile.js
import { ref } from 'vue';
import apiClient from '@/axios';

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

  const userPosts = ref([]);

  const fetchUserProfileByUsername = async (username) => {
    try {
      const response = await apiClient.get(`/api/user/${username}`);
      user.value = response.data.user;
      userPosts.value = response.data.user.posts;
    } catch (error) {
      console.error('Error fetching user profile by username:', error);
    }
  };

  return {
    user,
    userPosts,
    fetchUserProfileByUsername,
  };
}
