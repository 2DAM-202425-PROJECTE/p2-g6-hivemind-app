<!-- src/components/Profile/ProfileHeader.vue -->
<template>
  <div :class="['rounded-lg shadow-lg overflow-hidden max-w-5xl mx-auto mb-5', profileThemeClass, equippedBackgroundClass]">
    <!-- Banner -->
    <div class="relative w-full h-48 bg-gray-200 dark:bg-gray-700 z-0">
      <img
        v-if="user.equipped_banner_photo_path"
        :src="user.equipped_banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
        @error="handleImageError('equipped_banner_photo_path')"
      />
      <img
        v-else-if="user.banner_photo_path"
        :src="user.banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
        @error="handleImageError('banner_photo_path')"
      />
      <div
        v-else
        class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400"
      >
        No banner set
      </div>
    </div>

    <!-- Content -->
    <div class="relative px-6 pb-6">
      <div class="absolute -top-16 left-6 flex flex-col items-center">
        <div class="relative">
          <img
            :src="user.profile_photo_url"
            alt="Profile Pic"
            class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover z-10"
            @error="handleImageError('profile_photo_url')"
          />
          <img
            v-if="user.equipped_profile_icon_path"
            :src="user.equipped_profile_icon_path"
            alt="Equipped Profile Icon"
            class="equipped-profile-icon"
            @error="handleImageError('equipped_profile_icon_path')"
          />
        </div>
      </div>

      <div class="pt-20 flex flex-col md:flex-row md:items-start">
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <h3
              :class="[
                'text-2xl font-bold text-gray-900 dark:text-white',
                getNameEffectClass(user.equipped_name_effect_path),
                profileFontClass
              ]"
            >
              {{ user.name }}
            </h3>
            <div v-if="user.equipped_badge_path" class="equipped-badge">
              <img
                :src="user.equipped_badge_path"
                alt="Equipped Badge"
                :class="['equipped-badge-img', getNameEffectClass(user.equipped_name_effect_path)]"
                @error="handleImageError('equipped_badge_path')"
              />
            </div>
          </div>
          <div class="text-sm text-black dark:text-black">@{{ user.username }}</div>
          <div v-if="user.description" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            {{ user.description }}
          </div>
          <div v-else-if="isCurrentUser" class="mt-2 text-sm text-gray-400 dark:text-gray-500 italic">
            What would you like others to know about you? Add a description to your profile
          </div>
          <div class="flex gap-6 mt-3 text-sm text-gray-600 dark:text-gray-400">
            <span>{{ user.posts?.length || 0 }} posts</span>
            <button @click="openFollowModal(true)" class="hover:underline">
              {{ user.followers_count || 0 }} followers
            </button>
            <button @click="openFollowModal(false)" class="hover:underline">
              {{ user.following_count || 0 }} following
            </button>
          </div>
        </div>

        <div class="absolute right-6 top-4 flex flex-col space-y-2">
          <button
            v-if="!isCurrentUser"
            @click="toggleFollow"
            :disabled="isLoadingFollow"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition duration-200 transform hover:scale-105',
              isFollowing ? 'bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500' : 'bg-amber-500 text-black hover:bg-amber-600',
              isLoadingFollow ? 'opacity-50 cursor-not-allowed' : ''
            ]"
          >
            {{ isLoadingFollow ? 'Processing...' : isFollowing ? 'Unfollow' : 'Follow' }}
          </button>
          <button
            v-if="!isCurrentUser"
            @click="startChat"
            :disabled="isLoadingChat"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition duration-200 transform hover:scale-105 flex items-center gap-2',
              'bg-amber-500 text-black hover:bg-amber-600',
              isLoadingChat ? 'opacity-50 cursor-not-allowed' : ''
            ]"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
              />
            </svg>
            {{ isLoadingChat ? 'Starting...' : 'Start Chat' }}
          </button>
          <button
            v-if="isCurrentUser"
            @click="editProfile"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition duration-200 transform hover:scale-110"
            title="Edit Profile"
          >
            <i class="fas fa-edit"></i>
          </button>
          <button
            v-if="isCurrentUser"
            @click="showInventory = true"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-purple-500 dark:hover:text-purple-400 transition duration-200 transform hover:scale-110"
            title="Inventory"
          >
            <i class="fas fa-box"></i>
          </button>
          <button
            v-if="isCurrentUser"
            @click="connectSocial"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition duration-200 transform hover:scale-110"
            title="Connect Social Accounts"
          >
            <i class="fas fa-link"></i>
          </button>
          <button
            @click="shareProfile"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-green-500 dark:hover:text-green-400 transition duration-200 transform hover:scale-110"
            title="Share Profile"
          >
            <i class="fas fa-share-alt"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <InventoryModal
    v-if="showInventory"
    :user="user"
    @close="showInventory = false"
    @update-user="updateUserAndRefresh"
  />
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/axios.js';
import axios from 'axios';
import InventoryModal from './InventoryModal.vue';

const props = defineProps({
  user: { type: Object, required: true },
  isCurrentUser: { type: Boolean, required: true },
  isFollowing: { type: Boolean, required: true },
  isLoadingFollow: { type: Boolean, required: true },
  editProfile: { type: Function, required: true },
  connectSocial: { type: Function, default: () => console.log('Connect Social clicked') },
  shareProfile: { type: Function, required: true },
  toggleFollow: { type: Function, required: true },
  openFollowModal: { type: Function, required: true },
});

const showInventory = ref(false);
const isLoadingChat = ref(false);
const router = useRouter();

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
};

const defaultFallback = 'https://api.iconify.design/lucide/image-off.svg';

const handleImageError = (type) => {
  console.warn(`Image failed to load for ${type}`);
  if (type === 'equipped_banner_photo_path' || type === 'banner_photo_path') {
    props.user[type] = defaultFallback;
  } else if (type === 'profile_photo_url') {
    props.user.profile_photo_url = defaultFallback;
  } else if (type === 'equipped_profile_icon_path') {
    props.user.equipped_profile_icon_path = null;
  } else if (type === 'equipped_badge_path') {
    props.user.equipped_badge_path = null;
  }
};

const equippedBackgroundClass = computed(() => {
  return props.user.equipped_background_path ? `bg-[url(${props.user.equipped_background_path})] bg-cover bg-center` : '';
});

const profileThemeClass = computed(() => {
  return props.user.equipped_profile_theme || 'bg-white dark:bg-gray-800';
});

const profileFontClass = computed(() => {
  console.log('Equipped Font:', props.user.equipped_profile_font_path);
  if (!props.user.equipped_profile_font_path) return '';
  switch (props.user.equipped_profile_font_path) {
    case 'Pixel Art': return 'font-pixel-art';
    case 'Comic Sans': return 'font-comic-sans';
    case 'Gothic': return 'font-gothic';
    case 'Cursive': return 'font-cursive';
    case 'Typewriter': return 'font-typewriter';
    case 'Bubble': return 'font-bubble';
    case 'Neon': return 'font-neon';
    case 'Graffiti': return 'font-graffiti';
    case 'Retro': return 'font-retro';
    case 'Cyberpunk': return 'font-cyberpunk';
    case 'Western': return 'font-western';
    case 'Chalkboard': return 'font-chalkboard';
    case 'Horror': return 'font-horror';
    case 'Futuristic': return 'font-futuristic';
    case 'Handwritten': return 'font-handwritten';
    case 'Bold Script': return 'font-bold-script';
    default: return '';
  }
});

const getNameEffectClass = (nameEffectPath) => {
  if (!nameEffectPath) return '';
  const effectMap = {
    'Gradient Fade': 'gradient-fade',
    'Golden Outline': 'gradient-fade',
    'Dark Pulse': 'dark-pulse',
    'Cosmic Shine': 'cosmic-shine',
    'Neon Edge': 'neon-edge',
    'Frost Glow': 'frost-glow',
    'Fire Flicker': 'fire-ficker',
    'Emerald Sheen': 'emerald-sheen',
    'Phantom Haze': 'phantom-haze',
    'Electric Glow': 'electric-glow',
    'Solar Flare': 'solar-flare',
    'Wave Shimmer': 'wave-shimmer',
    'Crystal Pulse': 'crystal-pulse',
    'Mystic Aura': 'mystic-aura',
    'Shadow Veil': 'shadow-veil',
    'Digital Pulse': 'digital-pulse',
  };
  return effectMap[nameEffectPath] || '';
};

const fetchCsrfToken = async () => {
  try {
    await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
      withCredentials: true,
    });
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken || csrfToken.includes('{{')) {
      console.warn('CSRF token invalid, retrying...');
      await axios.get('http://localhost:8000/sanctum/csrf-cookie', { withCredentials: true });
      return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
    return csrfToken;
  } catch (error) {
    console.error('Error fetching CSRF token:', error);
    throw error;
  }
};

const startChat = async () => {
  isLoadingChat.value = true;
  try {
    await fetchCsrfToken();
    const response = await apiClient.get('/api/chats/private', {
      params: { recipient_id: props.user.id },
      withCredentials: true,
    });
    console.log('Chat created:', response.data);
    const chatId = response.data.chat.id;
    router.push({ path: '/chat', query: { chatId } });
  } catch (error) {
    console.error('Error starting chat:', error.response?.data || error.message);
    alert('Failed to start chat: ' + (error.response?.data?.error || 'Unknown error'));
  } finally {
    isLoadingChat.value = false;
  }
};

const loadEquippedState = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/equipped-items`);
    console.log('Equipped items response:', response.data);
    Object.assign(props.user, {
      equipped_profile_icon_path: response.data.equipped_profile_icon_path,
      equipped_profile_theme: response.data.equipped_profile_theme || 'bg-white dark:bg-gray-800',
      equipped_background_path: response.data.equipped_background_path,
      equipped_name_effect_path: response.data.equipped_name_effect_path,
      equipped_profile_font_path: response.data.equipped_profile_font_path,
      equipped_badge_path: response.data.equipped_badge_path,
    });
    console.log('Loaded equipped state (excluding banner):', props.user);
  } catch (error) {
    console.error('Failed to load equipped state:', error.response?.data || error.message);
  }
};

const updateUser = (updatedFields) => {
  console.log('Updating user with fields:', updatedFields);
  Object.assign(props.user, updatedFields);
  console.log('Updated user:', props.user);
};

const updateUserAndRefresh = async (updatedFields) => {
  updateUser(updatedFields);
  await loadEquippedState();
};

watch(() => props.user, (newUser) => {
  console.log('User prop updated in ProfileHeader:', newUser);
  console.log('Banner state - equipped_banner_photo_path:', newUser.equipped_banner_photo_path);
  console.log('Banner state - banner_photo_path:', newUser.banner_photo_path);
}, { deep: true });

onMounted(() => {
  loadEquippedState();
});
</script>

<style scoped>
@import '../../styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

.equipped-profile-icon {
  width: 2rem;
  height: 2rem;
  position: absolute;
  top: -2rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 20;
  filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.8)) drop-shadow(0 0 16px rgba(255, 255, 255, 0.6));
  animation: whiteGlowPulse 2s ease-in-out infinite;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.equipped-profile-icon:hover,
.equipped-badge-img:hover,
h3:hover {
  transform: scale(1.1);
  opacity: 0.9;
}

.equipped-badge-img {
  width: 2rem;
  height: 2rem;
  margin-left: 0.5rem;
  transition: filter 0.3s ease, transform 0.3s ease, opacity 0.3s ease;
}

.font-pixel-art { font-family: 'Press Start 2P', cursive; }
.font-comic-sans { font-family: 'Comic Neue', cursive; }
.font-gothic { font-family: 'Black Ops One', cursive; }
.font-cursive { font-family: 'Dancing Script', cursive; }
.font-typewriter { font-family: 'Courier Prime', monospace; }
.font-bubble { font-family: 'Bungee', cursive; }
.font-neon { font-family: 'Orbitron', sans-serif; }
.font-graffiti { font-family: 'Wallpoet', cursive; }
.font-retro { font-family: 'VT323', monospace; }
.font-cyberpunk { font-family: 'Monoton', cursive; }
.font-western { font-family: 'Special Elite', cursive; }
.font-chalkboard { font-family: 'Creepster', cursive; }
.font-horror { font-family: 'Creepster', cursive; }
.font-futuristic { font-family: 'Audiowide', cursive; }
.font-handwritten { font-family: 'Caveat', cursive; }
.font-bold-script { font-family: 'Permanent Marker', cursive; }

.w-full.h-full.object-cover {
  animation: banner-animation 10s infinite linear;
}

@keyframes whiteGlowPulse {
  0% { filter: drop-shadow(0 0 12px rgba(255, 255, 255, 1)) drop-shadow(0 0 24px rgba(255, 255, 255, 0.9)); }
  50% { filter: drop-shadow(0 0 16px rgba(255, 255, 255, 1)) drop-shadow(0 0 32px rgba(255, 255, 255, 1)); }
  100% { filter: drop-shadow(0 0 12px rgba(255, 255, 255, 1)) drop-shadow(0 0 24px rgba(255, 255, 255, 0.9)); }
}

@keyframes banner-animation {
  0% { transform: scale(1); }
  50% { transform: scale(1.02); }
  100% { transform: scale(1); }
}
</style>
