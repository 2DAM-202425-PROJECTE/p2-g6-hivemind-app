<!-- src/pages/ProfilePage.vue -->
<template>
  <div class="profile-wrapper">
    <div
      class="min-h-screen bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white p-5 pb-24 relative z-10"
      :style="backgroundStyle"
    >
      <Navbar />
      <h1 class="text-2xl mb-5">Profile</h1>

      <div class="dark:bg-gray-700 rounded-lg p-5 max-w-5xl mx-auto">
        <ProfileHeader
          :user="user"
          :is-current-user="isCurrentUser"
          :is-following="isFollowing"
          :is-loading-follow="isLoadingFollow"
          :edit-profile="editProfile"
          :share-profile="shareProfile"
          :toggle-follow="wrappedToggleFollow"
          :open-follow-modal="openFollowModal"
        />
        <ProfileStories
          :stories="stories"
          :profile-user-id="user?.id"
          :role="user?.role"
          :is-current-user="isCurrentUser"
        />
        <ProfilePosts :user-posts="userPosts" :is-current-user="isCurrentUser" />
      </div>

      <ProfileEditModal
        :is-open="isEditModalVisible"
        :user="user"
        @close="isEditModalVisible = false"
        @save="updateUserProfile"
      />
      <ShareModal v-if="isModalVisible" :share-url="shareUrl" @close="isModalVisible = false" />
      <InventoryModal
        v-if="isInventoryModalVisible"
        :user="user"
        @close="isInventoryModalVisible = false"
        @update-user="updateUserProfile"
      />
      <FollowListModal
        :is-open="isFollowModalOpen"
        :is-followers="isFollowersModal"
        :list="isFollowersModal ? followers : following"
        :has-more="isFollowersModal ? followersHasMore : followingHasMore"
        :total-count="isFollowersModal ? user.followers_count : user.following_count"
        :search-query="searchQuery"
        :fetch-list="isFollowersModal ? fetchFollowers : fetchFollowing"
        @update:isOpen="isFollowModalOpen = $event"
        @update:searchQuery="searchQuery = $event; resetSearch()"
      />
      <Footer />

      <v-snackbar
        v-model="snackbar"
        :timeout="2000"
        color="green darken-2"
        bottom
        class="flex justify-end"
      >
        Profile URL copied to clipboard!
      </v-snackbar>
      <v-snackbar
        v-model="followSnackbar.show"
        :timeout="3000"
        :color="followSnackbar.color"
        bottom
        class="flex justify-end"
      >
        {{ followSnackbar.message }}
      </v-snackbar>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useProfile } from '@/composables/profile/useProfile';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import ShareModal from '@/components/ShareModal.vue';
import ProfileHeader from '@/components/Profile/ProfileHeader.vue';
import ProfileStories from '@/components/Profile/ProfileStories.vue';
import ProfilePosts from '@/components/Profile/ProfilePosts.vue';
import ProfileEditModal from '@/components/Profile/ProfileEditModal.vue';
import InventoryModal from '@/components/Profile/InventoryModal.vue';
import FollowListModal from '@/components/Profile/FollowListModal.vue';
import apiClient from '@/axios.js';

const route = useRoute();
const {
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
  toggleFollow,
  fetchFollowers,
  fetchFollowing,
  resetSearch,
} = useProfile();

const isEditModalVisible = ref(false);
const isModalVisible = ref(false);
const isInventoryModalVisible = ref(false);
const isFollowModalOpen = ref(false);
const isFollowersModal = ref(true);
const shareUrl = ref('');
const snackbar = ref(false);
const followSnackbar = ref({ show: false, message: '', color: 'green darken-2' });
const stories = ref([]);

const backgroundStyle = computed(() => {
  return user.value?.equipped_background_path
    ? {
      backgroundImage: `url(${user.value.equipped_background_path})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundAttachment: 'fixed',
    }
    : {
      background: 'linear-gradient(to bottom right, #FEFCE8, #FDE68A)',
    };
});

const editProfile = () => {
  isEditModalVisible.value = true;
};

const updateUserProfile = (updatedUser) => {
  user.value = { ...user.value, ...updatedUser };
};

const shareProfile = () => {
  const url = `${window.location.origin}/profile/${user.value.username}`;
  navigator.clipboard.writeText(url).then(() => {
    snackbar.value = true;
  }).catch(err => {
    console.error('Failed to copy: ', err);
    snackbar.value = true;
  });
};

const openFollowModal = (isFollowers) => {
  console.log('Opening modal for', isFollowers ? 'followers' : 'following');
  isFollowersModal.value = isFollowers;
  isFollowModalOpen.value = true;
  resetSearch();
};

const wrappedToggleFollow = async () => {
  const result = await toggleFollow();
  followSnackbar.value = {
    show: true,
    message: result.message,
    color: result.success ? 'green darken-2' : 'red darken-2',
  };
};

const fetchStories = async () => {
  try {
    const response = await apiClient.get('/api/stories', {
      params: {
        profile_user_id: user.value?.id,
        role: user.value?.role,
      },
    });
    stories.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching stories:', error.response?.data || error.message);
  }
};

onMounted(async () => {
  console.log('Iniciando carga de datos en Profile.vue');
  await fetchUser();
  await fetchUserProfileByUsername(route.params.username);
  await fetchStories();
  console.log('Profile loaded, isCurrentUser:', isCurrentUser.value);
  console.log('userPosts en Profile.vue:', userPosts.value);
  console.log('Equipped items:', user.value);
  console.log('Stories:', stories.value);
});

watch(() => route.params.username, (newUsername, oldUsername) => {
  if (newUsername !== oldUsername) {
    fetchUserProfileByUsername(newUsername);
    fetchStories();
  }
});
</script>

<style scoped>
.profile-wrapper {
  position: relative;
  min-height: 100vh;
}

.min-h-screen {
  position: relative;
  z-index: 10;
}

.dark:bg-gray-700 {
  background-color: rgba(55, 65, 81, 0.9);
}

@keyframes bannerPulse {
  0%, 100% { opacity: 0.9; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.02); }
}
</style>
