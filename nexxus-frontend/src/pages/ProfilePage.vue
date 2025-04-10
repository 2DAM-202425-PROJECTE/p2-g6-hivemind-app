<!-- Profile.vue -->
<template>
  <div
    class="min-h-screen bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white p-5 pb-24"
    :style="backgroundStyle"
  >
    <Navbar />
    <h1 class="text-2xl mb-5">Profile</h1>

    <div class="dark:bg-gray-700 rounded-lg p-5 max-w-5xl mx-auto">
      <ProfileHeader
        :user="user"
        :is-current-user="isCurrentUser"
        :edit-profile="editProfile"
        :share-profile="shareProfile"
        :open-inventory="openInventory"
      />
      <ProfileStories :visible-stories="visibleStories" :prev-story="prevStory" :next-story="nextStory" />
      <ProfilePosts :user-posts="userPosts" :is-current-user="isCurrentUser" />
    </div>

    <ProfileEditModal :is-open="isEditModalVisible" :user="user" @close="isEditModalVisible = false" @save="updateUserProfile" />
    <ShareModal v-if="isModalVisible" :share-url="shareUrl" @close="isModalVisible = true" />
    <InventoryModal v-if="isInventoryModalVisible" :user="user" @close="isInventoryModalVisible = false" @update-user="updateUserProfile" />
    <Footer />

    <v-snackbar
      v-model="snackbar"
      :timeout="2000"
      color="green darken-2"
      bottom
      class="d-flex justify-end"
    >
      Profile URL copied to clipboard!
    </v-snackbar>
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

const route = useRoute();
const { user, userPosts, isCurrentUser, fetchUser, fetchUserProfileByUsername } = useProfile();

const isEditModalVisible = ref(false);
const isModalVisible = ref(false);
const isInventoryModalVisible = ref(false);
const shareUrl = ref('');
const snackbar = ref(false);

const backgroundStyle = computed(() => {
  return user.value?.equipped_background_path
    ? {
      backgroundImage: `url(${user.value.equipped_background_path})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
    }
    : {};
});

const editProfile = () => {
  isEditModalVisible.value = true;
};

const openInventory = () => {
  isInventoryModalVisible.value = true;
};

const updateUserProfile = (updatedUser) => {
  user.value = { ...user.value, ...updatedUser }; // Merge updated fields into user object
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

onMounted(async () => {
  console.log('Iniciando carga de datos en Profile.vue');
  await fetchUser(); // Loads current user with equipped items
  await fetchUserProfileByUsername(route.params.username);
  console.log('Profile loaded, isCurrentUser:', isCurrentUser.value);
  console.log('userPosts en Profile.vue:', userPosts.value);
  console.log('Equipped items:', user.value);
});

watch(() => route.params.username, (newUsername, oldUsername) => {
  if (newUsername !== oldUsername) {
    fetchUserProfileByUsername(newUsername);
  }
});
</script>

<style scoped>
.min-h-screen {
  position: relative;
  z-index: 1;
}

.dark:bg-gray-700 {
  background-color: rgba(55, 65, 81, 0.9); /* Slightly transparent for contrast */
}
</style>
