<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Profile</h2>

      <!-- Banner -->
      <div class="relative w-full h-40 bg-gray-200 rounded-lg mb-6 overflow-hidden group">
        <img
          v-if="bannerPreview || editedUser.banner_photo_path"
          :src="bannerPreview || getImageUrl(editedUser.banner_photo_path)"
          alt="Profile Banner"
          class="w-full h-full object-cover transition duration-300 group-hover:brightness-50"
        />
        <div v-else class="absolute inset-0 flex items-center justify-center text-gray-500">
          Upload a profile banner
        </div>
        <input
          type="file"
          @change="uploadBanner"
          accept=".png, .jpg, .jpeg"
          class="absolute inset-0 opacity-0 cursor-pointer"
        />
        <button
          v-if="bannerPreview || editedUser.banner_photo_path"
          @click="removeBanner"
          class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <i class="fas fa-trash"></i>
        </button>
      </div>

      <!-- Foto de perfil -->
      <div class="relative flex justify-center -mt-20 mb-6">
        <div class="relative group">
          <img
            :src="profilePreview || editedUser.profile_photo_url"
            alt="Profile Pic"
            class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover"
          />
          <label
            for="profile-upload"
            class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
          >
            <i class="fas fa-camera"></i>
          </label>
          <input
            id="profile-upload"
            type="file"
            @change="uploadProfilePic"
            accept=".png, .jpg, .jpeg"
            class="hidden"
          />
          <button
            v-if="profilePreview || editedUser.profile_photo_url"
            @click="removeProfilePic"
            class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
          >
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>

      <!-- Campos -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
          <input
            v-model="editedUser.name"
            type="text"
            class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
          <textarea
            v-model="editedUser.description"
            class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
            rows="3"
            style="max-height: 120px;"
          ></textarea>
        </div>
      </div>

      <!-- Botones -->
      <div class="flex justify-end gap-4 mt-6">
        <button
          @click="closeModal"
          class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          Cancel
        </button>
        <button
          @click="saveProfile"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Save
        </button>
      </div>

      <!-- Mensaje de error o éxito -->
      <p v-if="errorMessage" class="mt-4 text-red-500 text-sm">{{ errorMessage }}</p>
      <p v-if="successMessage" class="mt-4 text-green-500 text-sm">{{ successMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import apiClient from '@/axios';

const props = defineProps(['user', 'isOpen']);
const emit = defineEmits(['close', 'save']);

const editedUser = ref({ ...props.user });
const profilePhoto = ref(null);
const bannerPhoto = ref(null);
const profilePreview = ref(null);
const bannerPreview = ref(null);
const errorMessage = ref('');
const successMessage = ref('');

watch(() => props.user, (newUser) => {
  editedUser.value = { ...newUser };
}, { deep: true });

const getImageUrl = (filePath) => {
  const baseUrl = 'http://localhost:8000';
  if (filePath && filePath.startsWith('/')) return `${baseUrl}${filePath}`;
  return filePath ? `${baseUrl}/storage/${filePath}` : '/default-profile.jpg';
};

const uploadBanner = (event) => {
  const file = event.target.files[0];
  if (file) {
    bannerPhoto.value = file;
    bannerPreview.value = URL.createObjectURL(file);
  }
};

const uploadProfilePic = (event) => {
  const file = event.target.files[0];
  if (file) {
    profilePhoto.value = file;
    profilePreview.value = URL.createObjectURL(file);
  }
};

const removeBanner = () => {
  bannerPhoto.value = null;
  bannerPreview.value = null;
  editedUser.value.banner_photo_path = null;
};

const removeProfilePic = () => {
  profilePhoto.value = null;
  profilePreview.value = null;
  editedUser.value.profile_photo_url = null;
};

const saveProfile = async () => {
  const formData = new FormData();
  formData.append('name', editedUser.value.name || '');
  formData.append('description', editedUser.value.description || '');
  if (profilePhoto.value) {
    formData.append('profile_photo', profilePhoto.value);
  } else if (!profilePreview.value) {
    formData.append('remove_profile_photo', '1');
  }
  if (bannerPhoto.value) {
    formData.append('banner_photo', bannerPhoto.value);
  } else if (!bannerPreview.value) {
    formData.append('remove_banner_photo', '1');
  }

  try {
    const response = await apiClient.post('/api/user/profile/update', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    emit('save', response.data.user);
    successMessage.value = 'Profile updated successfully!';
    errorMessage.value = '';
    setTimeout(closeModal, 1500);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Error updating profile';
    successMessage.value = '';
    console.error('Error updating profile:', error);
  }
};

const closeModal = () => {
  emit('close');
  profilePhoto.value = null;
  bannerPhoto.value = null;
  profilePreview.value = null;
  bannerPreview.value = null;
  errorMessage.value = '';
  successMessage.value = '';
};
</script>
