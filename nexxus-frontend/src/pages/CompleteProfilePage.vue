<template>
  <div class="complete-profile-container">
    <div class="profile-box">
      <img class="logo" src="/logo.png" alt="Hivemind Logo" />
      <h1>Complete Your Profile</h1>

      <!-- Banner -->
      <div class="relative w-full h-48 bg-gray-200 rounded-xl mb-8 overflow-hidden group">
        <img
          v-if="bannerPreview || user.banner_photo_path"
          :src="bannerPreview || getImageUrl(user.banner_photo_path)"
          alt="Profile Banner"
          class="w-full h-full object-cover transition duration-300 group-hover:brightness-75"
        />
        <div v-else class="absolute inset-0 flex items-center justify-center text-gray-500 text-lg">
          Upload a Profile Banner
        </div>
        <input
          type="file"
          @change="uploadBanner"
          accept=".png, .jpg, .jpeg"
          class="absolute inset-0 opacity-0 cursor-pointer"
        />
      </div>

      <!-- Profile Picture -->
      <div class="relative flex justify-center -mt-24 mb-8">
        <div class="relative">
          <img
            :src="profilePreview || user.profile_photo_url || '/default-profile.jpg'"
            alt="Profile Pic"
            class="w-36 h-36 rounded-full border-4 border-white shadow-lg object-cover"
          />
          <label
            for="profile-upload"
            class="absolute bottom-1 right-1 bg-blue-600 text-white p-3 rounded-full cursor-pointer hover:bg-blue-700 transition duration-200"
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
        </div>
      </div>

      <!-- Fields -->
      <form @submit.prevent="saveProfile" class="space-y-6">
        <div>
          <label class="block text-sm font-semibold text-gray-800">Name</label>
          <input
            v-model="user.name"
            type="text"
            class="mt-2 w-full p-3 bg-[#f0f2f5] text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition duration-200"
            placeholder="Enter your name"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-800">Description</label>
          <textarea
            v-model="user.description"
            class="mt-2 w-full p-3 bg-[#f0f2f5] text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition duration-200"
            rows="4"
            placeholder="Tell us about yourself"
          ></textarea>
        </div>

        <!-- Terms and Privacy Checkboxes -->
        <div class="space-y-4">
          <div class="flex items-center">
            <input
              :checked="acceptedTerms"
              type="checkbox"
              id="terms-checkbox"
              class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-not-allowed"
              disabled
            />
            <label for="terms-checkbox" class="ml-2 text-sm text-gray-800">
              I agree to the
              <router-link
                to="/terms-of-service"
                @click.prevent="acceptTerms"
                class="text-blue-600 hover:underline font-semibold"
                target="_blank"
              >
                Terms of Service
              </router-link>
            </label>
          </div>
          <div class="flex items-center">
            <input
              :checked="acceptedPrivacy"
              type="checkbox"
              id="policy-checkbox"
              class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-not-allowed"
              disabled
            />
            <label for="policy-checkbox" class="ml-2 text-sm text-gray-800">
              I agree to the
              <router-link
                to="/privacy-policy"
                @click.prevent="acceptPrivacy"
                class="text-blue-600 hover:underline font-semibold"
                target="_blank"
              >
                Privacy Policy
              </router-link>
            </label>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 mt-8">
          <button
            type="button"
            @click="handleSkip"
            :disabled="!acceptedTerms || !acceptedPrivacy"
            class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition duration-200 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
          >
            Skip
          </button>
          <button
            type="submit"
            :disabled="!acceptedTerms || !acceptedPrivacy"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            Save
          </button>
        </div>
      </form>
    </div>

    <!-- Success Snackbar -->
    <v-snackbar
      v-model="showSuccessSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
      Profile updated successfully!
    </v-snackbar>

    <!-- Error Snackbar -->
    <v-snackbar
      v-model="showErrorSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
      {{ errorMessage }}
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/axios';

const router = useRouter();
const user = ref({
  name: '',
  username: '',
  description: '',
  profile_photo_url: '',
  banner_photo_path: '',
});
const profilePhoto = ref(null);
const bannerPhoto = ref(null);
const profilePreview = ref(null);
const bannerPreview = ref(null);
const errorMessage = ref('');
const showErrorSnackbar = ref(false);
const showSuccessSnackbar = ref(false);
const acceptedTerms = ref(false);
const acceptedPrivacy = ref(false);

const getImageUrl = (filePath) => {
  const baseUrl = 'http://localhost:8000'; // Adjust based on your server
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

const fetchUser = async () => {
  try {
    const response = await apiClient.get('/api/user', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });
    user.value = response.data;
  } catch (error) {
    console.error('Error fetching user:', error);
    errorMessage.value = 'Failed to load user data.';
    showErrorSnackbar.value = true;
    setTimeout(() => {
      showErrorSnackbar.value = false;
    }, 3000);
  }
};

const acceptTerms = () => {
  acceptedTerms.value = true;
  window.open('/terms-of-service', '_blank'); // Opens the link in a new tab
};

const acceptPrivacy = () => {
  acceptedPrivacy.value = true;
  window.open('/privacy-policy', '_blank'); // Opens the link in a new tab
};

const handleSkip = () => {
  if (!acceptedTerms.value || !acceptedPrivacy.value) {
    errorMessage.value = 'Please review and accept the Terms of Service and Privacy Policy to proceed.';
    showErrorSnackbar.value = true;
    setTimeout(() => {
      showErrorSnackbar.value = false;
    }, 3000);
    return;
  }
  router.push('/home');
};

const saveProfile = async () => {
  if (!acceptedTerms.value || !acceptedPrivacy.value) {
    errorMessage.value = 'Please review and accept the Terms of Service and Privacy Policy to proceed.';
    showErrorSnackbar.value = true;
    setTimeout(() => {
      showErrorSnackbar.value = false;
    }, 3000);
    return;
  }

  const formData = new FormData();
  formData.append('name', user.value.name || '');
  formData.append('description', user.value.description || '');
  if (profilePhoto.value) formData.append('profile_photo', profilePhoto.value);
  if (bannerPhoto.value) formData.append('banner_photo', bannerPhoto.value);

  try {
    const response = await apiClient.post('/api/user/profile/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    user.value = response.data.user;
    showSuccessSnackbar.value = true;
    errorMessage.value = '';

    setTimeout(() => {
      showSuccessSnackbar.value = false;
      router.push(`/profile/${user.value.username}`);
    }, 3000);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Error updating profile';
    showErrorSnackbar.value = true;
    showSuccessSnackbar.value = false;
    console.error('Error updating profile:', error);
    setTimeout(() => {
      showErrorSnackbar.value = false;
    }, 3000);
  }
};

onMounted(() => {
  fetchUser();
});
</script>

<style scoped>
.complete-profile-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-color: #f0f2f5;
  padding: 2rem;
}

.profile-box {
  text-align: center;
  background: #ffffff;
  padding: 3rem;
  border-radius: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  width: 100%;
  max-width: 800px;
}

.logo {
  width: 60px;
  height: 60px;
  margin-bottom: 1.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

h1 {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: #1a1a1a;
}

input,
textarea {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  color: #1a1a1a;
  background-color: #f0f2f5;
}

input::placeholder,
textarea::placeholder {
  color: #6b7280;
}

button {
  padding: 0.9rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.custom-snackbar {
  z-index: 10000;
  margin-bottom: 16px;
  margin-right: 200px;
  position: fixed;
  bottom: 0;
  right: 0;
  left: auto;
  transform: none;
  max-width: calc(100% - 32px);
}

.white--text {
  color: white !important;
}
</style>
