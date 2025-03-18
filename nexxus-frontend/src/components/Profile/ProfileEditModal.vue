<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Profile</h2>

      <!-- Banner -->
      <div class="relative w-full h-40 bg-gray-200 rounded-lg mb-6 overflow-hidden banner-container">
        <img
          v-if="bannerPreview || editedUser.banner_photo_path"
          :src="bannerPreview || getImageUrl(editedUser.banner_photo_path)"
          alt="Profile Banner"
          class="w-full h-full object-cover transition duration-300 banner-image"
        />
        <div v-else class="absolute inset-0 flex items-center justify-center text-gray-500 pointer-events-none">
          Upload a profile banner
        </div>
        <div class="banner-buttons absolute inset-0 flex items-center justify-center gap-4 transition-opacity duration-300">
          <label
            for="banner-upload"
            class="bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
          >
            <i class="fas fa-camera"></i>
          </label>
          <input
            id="banner-upload"
            type="file"
            @change="uploadBanner"
            accept=".png, .jpg, .jpeg"
            class="hidden"
          />
          <button
            v-if="bannerPreview || editedUser.banner_photo_path"
            @click="removeBanner"
            class="bg-red-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
          >
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>

      <!-- Foto de perfil -->
      <div class="relative flex justify-center -mt-20 mb-6">
        <div class="relative w-32 h-32 profile-container">
          <img
            :src="profileImageSrc"
            alt="Profile Pic"
            class="w-full h-full rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover transition duration-300 profile-image"
          />
          <div class="profile-buttons absolute inset-0 flex items-center justify-center gap-4 transition-opacity duration-300">
            <label
              for="profile-upload"
              class="bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
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
              v-if="hasRemovableProfilePic"
              @click="removeProfilePic"
              class="bg-red-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
            >
              <i class="fas fa-trash"></i>
            </button>
          </div>
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
import { ref, defineProps, defineEmits, watch, computed } from 'vue';
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

// Generar avatar con iniciales
const generateAvatar = (name) => {
  const canvas = document.createElement('canvas');
  canvas.width = 100;
  canvas.height = 100;
  const ctx = canvas.getContext('2d');
  ctx.fillStyle = '#FFDE21';
  ctx.fillRect(0, 0, 100, 100);

  const words = name.trim().split(' ');
  let initials = '';
  if (words.length === 1 && words[0].length >= 2) {
    initials = words[0].substring(0, 2);
  } else if (words.length >= 2) {
    initials = words[0][0] + words[words.length - 1][0];
  } else if (words.length === 1) {
    initials = words[0][0];
  }
  initials = initials.toUpperCase();
  ctx.fillStyle = '#000000';
  ctx.font = 'bold 48px Arial';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(initials, 50, 50);

  return canvas.toDataURL('image/png');
};

// Fuente de la imagen de perfil
const profileImageSrc = computed(() => {
  if (profilePreview.value) return profilePreview.value;
  if (editedUser.value.profile_photo_path) return getImageUrl(editedUser.value.profile_photo_path);
  return generateAvatar(editedUser.value.name || 'User');
});

// Determinar si la imagen de perfil es removible
const hasRemovableProfilePic = computed(() => {
  return profilePreview.value || (editedUser.value.profile_photo_path && true);
});

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
  editedUser.value.profile_photo_path = null;
};

const saveProfile = async () => {
  const formData = new FormData();
  formData.append('name', editedUser.value.name || '');
  formData.append('description', editedUser.value.description || '');

  if (profilePhoto.value) {
    formData.append('profile_photo', profilePhoto.value);
  } else if (!editedUser.value.profile_photo_path && props.user.profile_photo_path) {
    formData.append('profile_photo', '');
  }

  if (bannerPhoto.value) {
    formData.append('banner_photo', bannerPhoto.value);
  } else if (!editedUser.value.banner_photo_path && props.user.banner_photo_path) {
    formData.append('banner_photo', '');
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

<style scoped>
/* Estilos para el banner */
.banner-container {
  position: relative;
}

.banner-container:hover .banner-image {
  filter: brightness(50%);
}

.banner-container:hover .banner-buttons {
  opacity: 1;
}

.banner-buttons {
  opacity: 0;
}

/* Estilos para la foto de perfil */
.profile-container {
  position: relative;
}

.profile-container:hover .profile-image {
  filter: brightness(50%);
}

.profile-container:hover .profile-buttons {
  opacity: 1;
}

.profile-buttons {
  opacity: 0;
}
</style>
