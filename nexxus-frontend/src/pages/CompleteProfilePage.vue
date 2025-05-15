<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 flex flex-col">
    <Navbar />
    <div class="flex-1 flex items-center justify-center py-16 px-4">
      <div class="profile-box bg-white rounded-xl shadow-lg p-8 max-w-2xl w-full animate-fade-in">
        <img class="logo mx-auto mb-6" src="/logo.png" alt="Hivemind Logo" />
        <h1 class="text-4xl font-extrabold text-center mb-6">
          <span class="animate-letter gradient-text" style="--order: 1">C</span>
          <span class="animate-letter gradient-text" style="--order: 2">o</span>
          <span class="animate-letter gradient-text" style="--order: 3">m</span>
          <span class="animate-letter gradient-text" style="--order: 4">p</span>
          <span class="animate-letter gradient-text" style="--order: 5">l</span>
          <span class="animate-letter gradient-text" style="--order: 6">e</span>
          <span class="animate-letter gradient-text" style="--order: 7">t</span>
          <span class="animate-letter gradient-text" style="--order: 8">e</span>
          <span class="animate-letter gradient-text" style="--order: 9"> </span>
          <span class="animate-letter gradient-text" style="--order: 10">Y</span>
          <span class="animate-letter gradient-text" style="--order: 11">o</span>
          <span class="animate-letter gradient-text" style="--order: 12">u</span>
          <span class="animate-letter gradient-text" style="--order: 13">r</span>
          <span class="animate-letter gradient-text" style="--order: 14"> </span>
          <span class="animate-letter gradient-text" style="--order: 15">P</span>
          <span class="animate-letter gradient-text" style="--order: 16">r</span>
          <span class="animate-letter gradient-text" style="--order: 17">o</span>
          <span class="animate-letter gradient-text" style="--order: 18">f</span>
          <span class="animate-letter gradient-text" style="--order: 19">i</span>
          <span class="animate-letter gradient-text" style="--order: 20">l</span>
          <span class="animate-letter gradient-text" style="--order: 21">e</span>
        </h1>

        <!-- Banner -->
        <div class="relative w-full h-48 bg-amber-50 rounded-xl mb-8 overflow-hidden group">
          <img
            v-if="bannerPreview || user.banner_photo_path"
            :src="bannerPreview || getImageUrl(user.banner_photo_path)"
            alt="Profile Banner"
            class="w-full h-full object-cover transition duration-300 group-hover:brightness-75"
          />
          <div v-else class="absolute inset-0 flex items-center justify-center text-amber-700 text-lg">
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
              class="absolute bottom-1 right-1 bg-amber-500 text-black p-3 rounded-full cursor-pointer hover:bg-amber-600 transition duration-300"
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

        <!-- Form -->
        <form @submit.prevent="saveProfile" class="space-y-6">
          <div>
            <label class="block text-sm font-semibold text-amber-900">Name</label>
            <input
              v-model="user.name"
              type="text"
              class="mt-2 w-full p-3 bg-amber-50 text-gray-800 border border-amber-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 hover:border-amber-400 transition duration-300"
              placeholder="Enter your name"
            />
          </div>
          <div>
            <label class="block text-sm font-semibold text-amber-900">Description</label>
            <textarea
              v-model="user.description"
              class="mt-2 w-full p-3 bg-amber-50 text-gray-800 border border-amber-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 hover:border-amber-400 transition duration-300"
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
                class="h-5 w-5 text-amber-500 border-amber-200 rounded focus:ring-amber-500 cursor-not-allowed"
                disabled
              />
              <label for="terms-checkbox" class="ml-2 text-sm text-gray-800">
                I agree to the
                <router-link
                  to="/terms-of-service"
                  @click.prevent="acceptTerms"
                  class="text-amber-600 hover:underline font-semibold"
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
                class="h-5 w-5 text-amber-500 border-amber-200 rounded focus:ring-amber-500 cursor-not-allowed"
                disabled
              />
              <label for="policy-checkbox" class="ml-2 text-sm text-gray-800">
                I agree to the
                <router-link
                  to="/privacy-policy"
                  @click.prevent="acceptPrivacy"
                  class="text-amber-600 hover:underline font-semibold"
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
              class="px-6 py-2 text-gray-800 border border-amber-300 rounded-lg hover:bg-amber-100 transition duration-300 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
            >
              Skip
            </button>
            <button
              type="submit"
              :disabled="!acceptedTerms || !acceptedPrivacy"
              class="btn-primary"
            >
              Save
            </button>
          </div>
        </form>
      </div>
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

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/axios';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';

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

const fetchUser = async () => {
  try {
    const response = await apiClient.get('/api/user');
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
  window.open('/terms-of-service', '_blank');
};

const acceptPrivacy = () => {
  acceptedPrivacy.value = true;
  window.open('/privacy-policy', '_blank');
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
    const response = await apiClient.post('/api/user/profile/update', formData);
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
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes letter {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-letter {
  display: inline-block;
  opacity: 0;
  animation: letter 0.5s ease-out forwards;
  animation-delay: calc(var(--order) * 0.1s);
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.profile-box {
  text-align: center;
}

.logo {
  width: 60px;
  height: 60px;
}

.btn-primary {
  @apply px-6 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center;
  @apply bg-gradient-to-r from-amber-500 to-amber-400 text-black;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover:not(:disabled) {
  @apply bg-gradient-to-r from-amber-600 to-amber-500;
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.btn-primary:disabled {
  @apply bg-gray-400 text-gray-600 cursor-not-allowed;
  box-shadow: none;
}

.custom-snackbar {
  z-index: 10000;
  margin-bottom: 64px; /* Adjusted to avoid overlap with footer */
  margin-right: 16px;
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

@media (max-width: 768px) {
  .min-h-screen {
    padding: 1rem;
  }

  .profile-box {
    padding: 1.5rem;
  }

  .logo {
    width: 50px;
    height: 50px;
  }

  h1 {
    font-size: 2rem;
  }

  .relative.w-full.h-48 {
    height: 32px;
  }

  .relative.flex.justify-center.-mt-24 {
    margin-top: -6rem;
  }

  .w-36.h-36 {
    width: 5rem;
    height: 5rem;
  }
}
</style>
