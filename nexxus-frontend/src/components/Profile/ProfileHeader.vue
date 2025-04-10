<template>
  <div :class="['rounded-lg shadow-lg overflow-hidden max-w-5xl mx-auto mb-5', profileThemeClass, equippedBackgroundClass]">
    <!-- Banner -->
    <div class="relative w-full h-48 bg-gray-200 dark:bg-gray-700">
      <!-- Display equipped banner (GIF or image) -->
      <img
        v-if="user.equipped_banner_photo_path"
        :src="user.equipped_banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
        @error="handleImageError('equipped_banner_photo_path')"
      />
      <!-- Fallback to user-uploaded banner -->
      <img
        v-else-if="user.banner_photo_path"
        :src="user.banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
        @error="handleImageError('banner_photo_path')"
      />
      <!-- Show placeholder if no banner is set -->
      <div
        v-else
        class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400"
      >
        No banner set
      </div>
    </div>

    <!-- Content -->
    <div class="relative px-6 pb-6">
      <!-- Profile photo with equipped icon -->
      <div class="absolute -top-16 left-6 flex flex-col items-center">
        <div class="relative">
          <img
            :src="user.profile_photo_url"
            alt="Profile Pic"
            class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover"
            @error="handleImageError('profile_photo_url')"
          />
        </div>
        <img
          v-if="user.equipped_badge_path"
          :src="user.equipped_badge_path"
          alt="Equipped Badge"
          class="equipped-badge"
          @error="handleImageError('equipped_badge_path')"
        />
      </div>

      <!-- Info and buttons -->
      <div class="pt-20 flex flex-col md:flex-row md:items-start">
        <!-- User info -->
        <div class="flex-1">
          <h3
            :class="[
              'text-2xl font-bold text-gray-900 dark:text-white',
              getNameEffectClass(user.equipped_name_effect_path),
              profileFontClass
            ]"
          >
            {{ user.name }}
          </h3>
          <div class="text-sm text-gray-500 dark:text-gray-400">@{{ user.username }}</div>
          <div v-if="user.description" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            {{ user.description }}
          </div>
          <div v-else-if="isCurrentUser" class="mt-2 text-sm text-gray-400 dark:text-gray-500 italic">
            What would you like others to know about you? Add a description to your profile
          </div>
          <div class="flex gap-6 mt-3 text-sm text-gray-600 dark:text-gray-400">
            <span>{{ user.posts?.length || 0 }} posts</span>
            <span>{{ user.followers || 0 }} followers</span>
            <span>{{ user.following || 0 }} following</span>
          </div>
        </div>

        <!-- Buttons -->
        <div class="absolute right-6 top-4 flex flex-col space-y-2">
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
    @update-user="updateUser"
  />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import apiClient from '@/axios.js';
import InventoryModal from './InventoryModal.vue';

const props = defineProps({
  user: { type: Object, required: true },
  isCurrentUser: { type: Boolean, required: true },
  editProfile: { type: Function, required: true },
  connectSocial: { type: Function, default: () => console.log('Connect Social clicked') },
  shareProfile: { type: Function, required: true },
});

const showInventory = ref(false);

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
    'https://api.iconify.design/lucide/blend.svg': 'gradient-fade',
    'https://api.iconify.design/lucide/badge.svg': 'golden-outline',
    'https://api.iconify.design/lucide/vibrate.svg': 'dark-pulse',
    'https://api.iconify.design/lucide/stars.svg': 'cosmic-shine',
    'https://api.iconify.design/lucide/lightbulb.svg': 'neon-edge',
    'https://api.iconify.design/lucide/snowflake.svg': 'frost-glow',
    'https://api.iconify.design/lucide/flame.svg': 'fire-flicker',
    'https://api.iconify.design/lucide/gem.svg': 'emerald-sheen',
    'https://api.iconify.design/lucide/cloud-fog.svg': 'phantom-haze',
    'https://api.iconify.design/lucide/zap.svg': 'electric-glow',
    'https://api.iconify.design/lucide/sun.svg': 'solar-flare',
    'https://api.iconify.design/lucide/waves.svg': 'wave-shimmer',
    'https://api.iconify.design/lucide/diamond.svg': 'crystal-pulse',
    'https://api.iconify.design/lucide/sparkles.svg': 'mystic-aura',
    'https://api.iconify.design/lucide/shield.svg': 'shadow-veil',
    'https://api.iconify.design/mdi/pulse.svg?color=%2300FFFF': 'digital-pulse',
  };
  return effectMap[nameEffectPath] || '';
};

const loadEquippedState = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/equipped-items`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    Object.assign(props.user, {
      equipped_profile_icon_path: response.data.equipped_profile_icon_path,
      equipped_profile_theme: response.data.equipped_profile_theme,
      equipped_background_path: response.data.equipped_background_path,
      equipped_name_effect_path: response.data.equipped_name_effect_path,
      equipped_profile_font_path: response.data.equipped_profile_font_path,
      equipped_badge_path: response.data.equipped_badge_path,
      equipped_banner_photo_path: response.data.equipped_banner_photo_path,
    });
    console.log('Loaded equipped state:', props.user);
  } catch (error) {
    console.error('Failed to load equipped state:', error.response?.data || error.message);
  }
};

const updateUser = (updatedFields) => {
  Object.assign(props.user, updatedFields);
  console.log('Updated user:', props.user);
};

const handleImageError = (field) => {
  console.warn(`Image failed to load for ${field}: ${props.user[field]}`);
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
  };

  if (field === 'equipped_banner_photo_path') {
    const themeValue = Object.keys(bannerNames).find(key =>
      props.user[field]?.toLowerCase().includes(key)
    );
    const itemName = themeValue ? bannerNames[themeValue] : '';
    props.user[field] = itemName && fallbackUrls[itemName] ?
      fallbackUrls[itemName] : defaultFallback;
  } else if (field === 'equipped_name_effect_path') {
    const itemName = props.user[field] === 'https://api.iconify.design/mdi/pulse.svg?color=%2300FFFF' ? 'Digital Pulse' : '';
    props.user[field] = itemName && fallbackUrls[itemName] ?
      fallbackUrls[itemName] : defaultFallback;
  } else {
    props.user[field] = defaultFallback;
  }
};

onMounted(() => {
  loadEquippedState();
});
</script>

<style scoped>
@import '../../styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

.equipped-badge {
  width: 1.5rem;
  height: 1.5rem;
  position: absolute;
  bottom: -0.5rem;
  right: -0.5rem;
  z-index: 10;
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

@keyframes banner-animation {
  0% { transform: scale(1); }
  50% { transform: scale(1.02); }
  100% { transform: scale(1); }
}
</style>
