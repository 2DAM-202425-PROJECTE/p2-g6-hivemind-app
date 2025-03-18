<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white p-5 pb-24">
    <Navbar />
    <h1 class="text-2xl mb-5">Profile</h1>

    <div class="dark:bg-gray-700 rounded-lg p-5 max-w-5xl mx-auto">
      <ProfileHeader :user="user" :is-current-user="isCurrentUser" :edit-profile="editProfile" :share-profile="shareProfile" />
      <ProfileStories :visible-stories="visibleStories" :prev-story="prevStory" :next-story="nextStory" />
      <ProfilePosts :user-posts="userPosts" :is-current-user="isCurrentUser" />
    </div>

    <ProfileEditModal :is-open="isEditModalVisible" :user="user" @close="isEditModalVisible = false" @save="updateUserProfile" />
    <ShareModal v-if="isModalVisible" :share-url="shareUrl" @close="isModalVisible = false"/>
    <Footer/>

    <!-- Snackbar para el mensaje de copia -->
    <v-snackbar
      v-model="snackbar"
      :timeout="2000"
      color="green darken-2"
      bottom
      right
    >
      Profile URL copied to clipboard!
    </v-snackbar>
  </div>
</template>

<script setup>
import {ref, onMounted, watch} from 'vue';
import {useRoute} from 'vue-router';
import {useProfile} from '@/composables/profile/useProfile';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import ShareModal from '@/components/ShareModal.vue';
import ProfileHeader from '@/components/Profile/ProfileHeader.vue';
import ProfileStories from '@/components/Profile/ProfileStories.vue';
import ProfilePosts from '@/components/Profile/ProfilePosts.vue';
import ProfileEditModal from '@/components/Profile/ProfileEditModal.vue';

const route = useRoute();
const {user, userPosts, isCurrentUser, fetchUser, fetchUserProfileByUsername} = useProfile();

const isEditModalVisible = ref(false);
const isModalVisible = ref(false);
const shareUrl = ref('');
const snackbar = ref(false);

const editProfile = () => {
  isEditModalVisible.value = true;
};

const updateUserProfile = (updatedUser) => {
  user.value = updatedUser;
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
  await fetchUser();
  await fetchUserProfileByUsername(route.params.username);
  console.log('Profile loaded, isCurrentUser:', isCurrentUser.value);
  console.log('userPosts en Profile.vue:', userPosts.value);
});

watch(() => route.params.username, (newUsername, oldUsername) => {
  if (newUsername !== oldUsername) {
    fetchUserProfileByUsername(newUsername);
  }
});
</script>
