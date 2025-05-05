<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Profile</h2>

      <!-- Banner -->
      <div class="relative w-full h-40 bg-gray-200 rounded-lg mb-6 overflow-hidden banner-container">
        <img
          v-if="bannerImageSrc"
          :src="bannerImageSrc"
          alt="Profile Banner"
          class="w-full h-full object-cover transition duration-300 banner-image"
          @error="handleImageError('banner')"
        />
        <div v-else class="absolute inset-0 flex items-center justify-center text-gray-500 pointer-events-none">
          Upload a profile banner
        </div>
        <div class="banner-buttons absolute inset-0 flex items-center justify-center gap-4 transition-opacity duration-300">
          <label
            for="banner-upload"
            class="bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
            aria-label="Upload profile banner"
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
            v-if="bannerPreview || editedUser.banner_photo_path || localEquippedBanner"
            @click="removeBanner"
            class="bg-red-500 text-white p-2 rounded-full cursor-pointer hover:bg-red-600"
            aria-label="Remove profile banner"
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
            @error="handleImageError('profile')"
          />
          <div class="profile-buttons absolute inset-0 flex items-center justify-center gap-4 transition-opacity duration-300">
            <label
              for="profile-upload"
              class="bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600"
              aria-label="Upload profile picture"
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
              class="bg-red-500 text-white p-2 rounded-full cursor-pointer hover:bg-red-600"
              aria-label="Remove profile picture"
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
const emit = defineEmits(['close', 'save', 'update-user']);

const editedUser = ref({ ...props.user });
const profilePhoto = ref(null);
const bannerPhoto = ref(null);
const profilePreview = ref(null);
const bannerPreview = ref(null);
const errorMessage = ref('');
const successMessage = ref('');
const localEquippedBanner = ref(props.user.equipped_banner_photo_path);

// Fallback URLs for banners
const fallbackUrls = {
  'Cosmic Vortex': 'https://media.tenor.com/5o2qbr5P5mUAAAAC/space-vortex.gif',
  'Neon Cityscape': 'https://media.tenor.com/8vL1Z5j0Z7IAAAAC/neon-city.gif',
  'Firestorm Horizon': 'https://media.tenor.com/2vL5z5z5z5IAAAAC/firestorm.gif',
  'Mystic Nebula': 'https://media.tenor.com/1z5z5z5z5zIAAAAC/nebula-space.gif',
  'Cyber Grid': 'https://media.tenor.com/3z5z5z5z5zIAAAAC/cyber-grid.gif',
  'Ethereal Waves': 'https://media.tenor.com/4z5z5z5z5zIAAAAC/ethereal-waves.gif',
  'Ocean Surge': 'https://media.tenor.com/5z5z5z5z5zIAAAAC/ocean-waves.gif',
  'Pixel Storm': 'https://media.tenor.com/6z5z5z5z5zIAAAAC/pixel-storm.gif',
  'Lava Flow': 'https://media.tenor.com/7z5z5z5z5zIAAAAC/lava-flow.gif',
  'Frost Vortex': 'https://media.tenor.com/8z5z5z5z5zIAAAAC/frost-vortex.gif',
  'Steampunk Gears': 'https://media.tenor.com/9z5z5z5z5zIAAAAC/steampunk-gears.gif',
  'Lunar Eclipse': 'https://media.tenor.com/0z5z5z5z5zIAAAAC/lunar-eclipse.gif',
  'Glitch Matrix': 'https://media.tenor.com/1z5z5z5z5zIAAAAC/glitch-matrix.gif',
  'Aurora Dance': 'https://media.tenor.com/2z5z5z5z5zIAAAAC/aurora-borealis.gif',
  'Galactic Spin': 'https://media.tenor.com/3z5z5z5z5zIAAAAC/galaxy-spin.gif',
  'Rainbow Flux': 'https://media.tenor.com/4z5z5z5z5zIAAAAC/rainbow-flux.gif',
  'Digital Pulse': 'https://api.iconify.design/mdi/pulse.svg?color=%2300FFFF',
  'Misty Peaks': 'https://api.iconify.design/mdi/mountain.svg?color=%23666666',
  'Cascading Falls': 'https://api.iconify.design/mdi/waterfall.svg?color=%2300FFFF',
  'Stormy Waves': 'https://api.iconify.design/mdi/wave.svg?color=%230066CC',
  'Desert Sunset': 'https://api.iconify.design/mdi/weather-sunset.svg?color=%23FF4500',
  'Northern Lights': 'https://api.iconify.design/mdi/weather-night.svg?color=%2300FF00',
  'Gentle Waterfall': 'https://api.iconify.design/mdi/water.svg?color=%2300FFFF',
  'Autumn Drift': 'https://api.iconify.design/mdi/leaf.svg?color=%23FFA500',
  'Tech Grid': 'https://api.iconify.design/mdi/grid.svg?color=%2300FFFF',
  'Particle Flow': 'https://api.iconify.design/mdi/dots-horizontal.svg?color=%23FF00FF',
  'Circuit Pulse': 'https://api.iconify.design/mdi/circuit-board.svg?color=%2300FF00',
  'Matrix Rain': 'https://api.iconify.design/mdi/matrix.svg?color=%2300FF00',
  'Cyber Skyline': 'https://api.iconify.design/mdi/city.svg?color=%23FF4500',
  'Code Rainfall': 'https://api.iconify.design/mdi/code-braces.svg?color=%2300FF00',
  'Holo Waves': 'https://api.iconify.design/mdi/waveform.svg?color=%2300FFFF',
  'Neon Pulse': 'https://api.iconify.design/mdi/pulse.svg?color=%23FF00FF',
  'Star Warp': 'https://api.iconify.design/mdi/star-four-points.svg?color=%2300FFFF',
};

const defaultBanner = 'https://api.iconify.design/lucide/image-off.svg';

// Computed property for banner image source
const bannerImageSrc = computed(() => {
  console.log('bannerPreview:', bannerPreview.value);
  console.log('editedUser.banner_photo_path:', editedUser.value.banner_photo_path);
  console.log('localEquippedBanner:', localEquippedBanner.value);
  if (bannerPreview.value) return bannerPreview.value;
  if (editedUser.value.banner_photo_path) return getImageUrl(editedUser.value.banner_photo_path);
  if (localEquippedBanner.value) return getImageUrl(localEquippedBanner.value);
  return defaultBanner;
});

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
  localEquippedBanner.value = newUser.equipped_banner_photo_path;
}, { deep: true });

const getImageUrl = (filePath) => {
  if (!filePath) return defaultBanner;
  if (filePath.startsWith('http://') || filePath.startsWith('https://')) {
    return filePath;
  }
  const baseUrl = 'http://localhost:8000';
  if (filePath.startsWith('/')) return `${baseUrl}${filePath}`;
  return `${baseUrl}/storage/${filePath}`;
};

const handleImageError = (type) => {
  console.warn(`Image failed to load for ${type}`);
  const bannerNames = {
    'cosmic_vortex': 'Cosmic Vortex',
    'neon_cityscape': 'Neon Cityscape',
    'firestorm_horizon': 'Firestorm Horizon',
    'mystic_nebula': 'Mystic Nebula',
    'cyber_grid': 'Cyber Grid',
    'ethereal_waves': 'Ethereal Waves',
    'ocean_surge': 'Ocean Surge',
    'pixel_storm': 'Pixel Storm',
    'lava_flow': 'Lava Flow',
    'frost_vortex': 'Frost Vortex',
    'steampunk_gears': 'Steampunk Gears',
    'lunar_eclipse': 'Lunar Eclipse',
    'glitch_matrix': 'Glitch Matrix',
    'aurora_dance': 'Aurora Dance',
    'galactic_spin': 'Galactic Spin',
    'rainbow_flux': 'Rainbow Flux',
    'digital_pulse': 'Digital Pulse',
    'misty_peaks': 'Misty Peaks',
    'cascading_falls': 'Cascading Falls',
    'stormy_waves': 'Stormy Waves',
    'desert_sunset': 'Desert Sunset',
    'northern_lights': 'Northern Lights',
    'gentle_waterfall': 'Gentle Waterfall',
    'autumn_drift': 'Autumn Drift',
    'tech_grid': 'Tech Grid',
    'particle_flow': 'Particle Flow',
    'circuit_pulse': 'Circuit Pulse',
    'matrix_rain': 'Matrix Rain',
    'cyber_skyline': 'Cyber Skyline',
    'code_rainfall': 'Code Rainfall',
    'holo_waves': 'Holo Waves',
    'neon_pulse': 'Neon Pulse',
    'star_warp': 'Star Warp',
  };

  if (type === 'banner') {
    let bannerPath = bannerPreview.value || editedUser.value.banner_photo_path || localEquippedBanner.value;
    const themeValue = Object.keys(bannerNames).find(key =>
      bannerPath?.toLowerCase().includes(key)
    );
    const itemName = themeValue ? bannerNames[themeValue] : '';
    const fallbackUrl = itemName && fallbackUrls[itemName] ? fallbackUrls[itemName] : defaultBanner;

    if (bannerPreview.value) bannerPreview.value = fallbackUrl;
    else if (editedUser.value.banner_photo_path) editedUser.value.banner_photo_path = fallbackUrl;
    else if (localEquippedBanner.value) {
      localEquippedBanner.value = fallbackUrl;
      emit('update-user', { equipped_banner_photo_path: fallbackUrl });
    }
  } else if (type === 'profile') {
    if (profilePreview.value) profilePreview.value = defaultBanner;
    else editedUser.value.profile_photo_path = defaultBanner;
  }
};

const uploadBanner = (event) => {
  const file = event.target.files[0];
  if (file) {
    bannerPhoto.value = file;
    bannerPreview.value = URL.createObjectURL(file);
    localEquippedBanner.value = null;
    emit('update-user', { equipped_banner_photo_path: null });
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
  localEquippedBanner.value = null;
  emit('update-user', { equipped_banner_photo_path: null });
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
    formData.append('remove_profile_photo', '1');
  }

  if (bannerPhoto.value) {
    formData.append('banner_photo', bannerPhoto.value);
  } else if (!editedUser.value.banner_photo_path && props.user.banner_photo_path) {
    formData.append('remove_banner_photo', '1');
  }

  try {
    // Update profile (name, description, photos)
    const response = await apiClient.post('/api/user/profile/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    // Clear equipped banner if a new banner was uploaded or the banner was removed
    if (bannerPhoto.value || (!editedUser.value.banner_photo_path && (props.user.banner_photo_path || props.user.equipped_banner_photo_path))) {
      console.log('Sending request to clear equipped banner for user ID:', props.user.id);
      const equippedResponse = await apiClient.post('/api/user/update-equipped-banner', {
        userId: props.user.id,
        equipped_banner_photo_path: null,
      });
      console.log('Equipped banner response:', equippedResponse.data);
      localEquippedBanner.value = null;
    }

    // Update the user object to emit
    const updatedUser = {
      ...response.data.user,
      banner_photo_path: bannerPhoto.value ? response.data.user.banner_photo_path : null,
      equipped_banner_photo_path: null, // Explicitly set to null
    };

    emit('save', updatedUser);
    emit('update-user', { equipped_banner_photo_path: null });
    successMessage.value = 'Profile updated successfully!';
    errorMessage.value = '';
    setTimeout(closeModal, 1500);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Error updating profile';
    successMessage.value = '';
    console.error('Error updating profile:', error.response?.data || error);
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
  localEquippedBanner.value = props.user.equipped_banner_photo_path;
};
</script>

<style scoped>
/* Estilos para el banner */
.banner-container {
  position: relative;
}

.banner-image {
  animation: banner-animation 10s infinite linear;
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

@keyframes banner-animation {
  0% { transform: scale(1); }
  50% { transform: scale(1.02); }
  100% { transform: scale(1); }
}
</style>
