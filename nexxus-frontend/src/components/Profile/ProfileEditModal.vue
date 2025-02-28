<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white text-black p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Edit Profile</h2>

      <div class="relative w-full h-24 bg-gray-200 flex justify-center items-center border border-gray-300 rounded-lg mb-4">
        <span class="text-gray-500">Upload a profile banner</span>
        <input type="file" @change="uploadBanner" class="absolute inset-0 opacity-0 cursor-pointer" />
      </div>

      <div class="relative flex justify-center -mt-12">
        <img class="w-24 h-24 rounded-full border-4 border-gray-300 shadow-md" :src="editedUser.profile_photo_url" alt="Profile Pic" />
        <input type="file" @change="uploadProfilePic" class="absolute w-full h-full opacity-0 cursor-pointer" />
      </div>

      <div class="mt-6">
        <label class="block text-sm font-medium text-gray-600">Username</label>
        <input v-model="editedUser.name" type="text" class="w-full p-2 border border-gray-300 rounded mt-1" />
      </div>

      <div class="mt-4 flex gap-4 text-sm text-gray-600">
        <span>{{ editedUser.posts?.length || 0 }} posts</span>
        <span>{{ editedUser.followers || 0 }} followers</span>
        <span>{{ editedUser.following || 0 }} following</span>
      </div>

      <div class="flex justify-between items-center mt-5">
        <button @click="saveProfile" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-600">Save</button>
        <button @click="closeModal" class="border border-gray-400 text-gray-600 px-4 py-2 rounded hover:bg-gray-100">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import apiClient from '@/axios';

const props = defineProps(['user', 'isOpen']);
const emit = defineEmits(['close', 'save']);
const editedUser = ref({ ...props.user });
const profilePhoto = ref(null);
const bannerPhoto = ref(null);

const uploadBanner = (event) => {
  bannerPhoto.value = event.target.files[0];
};

const uploadProfilePic = (event) => {
  profilePhoto.value = event.target.files[0];
};

const saveProfile = async () => {
  const formData = new FormData();
  formData.append('name', editedUser.value.name);
  if (profilePhoto.value) {
    formData.append('profile_photo', profilePhoto.value);
  }
  if (bannerPhoto.value) {
    formData.append('banner_photo', bannerPhoto.value);
  }

  try {
    const response = await apiClient.post('/api/user/profile/update', formData);
    emit('save', response.data.user);
    closeModal();
  } catch (error) {
    console.error('Error updating profile:', error.response?.data || error.message);
  }
};

const closeModal = () => {
  emit('close');
};
</script>
