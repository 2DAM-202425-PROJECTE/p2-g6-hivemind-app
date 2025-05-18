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
    followers_count: 0,
    following_count: 0,
  });

  const currentUser = ref(null);
  const userPosts = ref([]);
  const isCurrentUser = ref(false);
  const isFollowing = ref(false);
  const isLoadingFollow = ref(false);
  const followers = ref([]);
  const following = ref([]);
  const followersPage = ref(1);
  const followingPage = ref(1);
  const followersHasMore = ref(true);
  const followingHasMore = ref(true);
  const searchQuery = ref('');

  const fetchUser = async () => {
    try {
      const response = await axios.get('/api/user');
      console.log('fetchUser response:', response.data);
      currentUser.value = response.data;
      checkIsCurrentUser();
    } catch (error) {
      console.error('Error en fetchUser:', error);
    }
  };

  const fetchUserProfileByUsername = async (username) => {
    try {
      const response = await axios.get(`/api/user/${username}`);
      console.log('fetchUserProfileByUsername response:', response.data);
      user.value = response.data.user;
      userPosts.value = response.data.posts || [];
      checkIsCurrentUser();
      await checkIsFollowing();
    } catch (error) {
      console.error('Error en fetchUserProfileByUsername:', error);
    }
  };

  const checkIsCurrentUser = () => {
    if (currentUser.value && user.value.username) {
      console.log('Comparando:', currentUser.value.username, 'vs', user.value.username);
      isCurrentUser.value = currentUser.value.username === user.value.username;
    } else {
      console.log('Falta currentUser o user.username:', currentUser.value, user.value.username);
      isCurrentUser.value = false;
    }
  };

  const checkIsFollowing = async () => {
    if (!currentUser.value || !user.value.id || isCurrentUser.value) {
      isFollowing.value = false;
      return;
    }
    try {
      const response = await axios.get(`/api/is-following/${user.value.id}`);
      isFollowing.value = response.data.is_following;
      console.log('isFollowing:', isFollowing.value);
    } catch (error) {
      console.error('Error checking follow status:', error);
      isFollowing.value = false;
    }
  };

  const toggleFollow = async () => {
    if (!currentUser.value || isCurrentUser.value) return { success: false, message: 'Invalid action' };

    isLoadingFollow.value = true;
    try {
      const endpoint = isFollowing.value ? `/unfollow/${user.value.id}` : `/follow/${user.value.id}`;
      const response = await axios.post(`/api${endpoint}`);
      isFollowing.value = !isFollowing.value;
      user.value.followers_count = response.data.user.followers_count;
      console.log('Toggle follow response:', response.data);
      return { success: true, message: response.data.message };
    } catch (error) {
      console.error('Error toggling follow:', error.response?.data);
      return { success: false, message: error.response?.data?.message || 'Error processing follow action' };
    } finally {
      isLoadingFollow.value = false;
    }
  };

  const fetchFollowers = async (reset = false) => {
    if (reset) {
      followersPage.value = 1;
      followers.value = [];
      followersHasMore.value = true;
    }
    if (!followersHasMore.value) return;

    try {
      const response = await axios.get(`/api/users/${user.value.username}/followers`, {
        params: {
          page: followersPage.value,
          query: searchQuery.value,
        },
      });
      followers.value = reset
        ? response.data.followers.data
        : [...followers.value, ...response.data.followers.data];
      followersHasMore.value = !!response.data.followers.next_page_url;
      followersPage.value += 1;
    } catch (error) {
      console.error('Error fetching followers:', error.response?.data || error.message);
    }
  };

  const fetchFollowing = async (reset = false) => {
    if (reset) {
      followingPage.value = 1;
      following.value = [];
      followingHasMore.value = true;
    }
    if (!followingHasMore.value) return;

    try {
      const response = await axios.get(`/api/users/${user.value.username}/following`, {
        params: {
          page: followingPage.value,
          query: searchQuery.value,
        },
      });
      following.value = reset
        ? response.data.following.data
        : [...following.value, ...response.data.following.data];
      followingHasMore.value = !!response.data.following.next_page_url;
      followingPage.value += 1;
    } catch (error) {
      console.error('Error fetching following:', error.response?.data || error.message);
    }
  };

  const resetSearch = () => {
    searchQuery.value = '';
    followers.value = [];
    following.value = [];
    followersPage.value = 1;
    followingPage.value = 1;
    followersHasMore.value = true;
    followingHasMore.value = true;
  };

  return {
    user,
    userPosts,
    isCurrentUser,
    isFollowing,
    isLoadingFollow,
    followers,
    following,
    followersHasMore,
    followingHasMore,
    searchQuery,
    fetchUser,
    fetchUserProfileByUsername,
    checkIsCurrentUser,
    checkIsFollowing,
    toggleFollow,
    fetchFollowers,
    fetchFollowing,
    resetSearch,
  };
}
