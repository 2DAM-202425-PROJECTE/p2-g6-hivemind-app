<template>
  <div :class="['bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden max-w-5xl mx-auto mb-5', equippedBackgroundClass]">
    <!-- Banner -->
    <div class="relative w-full h-48 bg-gray-200 dark:bg-gray-700">
      <img
        v-if="user.banner_photo_path"
        :src="user.banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
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
      <!-- Profile photo with equipped icon -->
      <div class="absolute -top-16 left-6 flex flex-col items-center">
        <!-- Equipped Profile Icon (e.g., Crown) -->
        <img
          v-if="user.equipped_profile_icon_path"
          :src="user.equipped_profile_icon_path"
          alt="Equipped Profile Icon"
          class="equipped-icon"
        />
        <!-- Profile Photo -->
        <img
          :src="user.profile_photo_url"
          alt="Profile Pic"
          class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover"
        />
      </div>

      <!-- Info and buttons -->
      <div class="pt-20 flex flex-col md:flex-row md:items-start">
        <!-- User info -->
        <div class="flex-1">
          <h3 :class="['text-2xl font-bold text-gray-900 dark:text-white', getNameEffectClass(user.equipped_name_effect_path)]">
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

  <!-- Inventory Modal -->
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

const equippedBackgroundClass = computed(() => {
  return props.user.equipped_background_path ? `bg-[url(${props.user.equipped_background_path})]` : '';
});

const loadEquippedState = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/equipped-items`);

    props.user.equipped_profile_icon_path = response.data.equipped_profile_icon_path;
    props.user.equipped_background_path = response.data.equipped_background_path;
    props.user.equipped_name_effect_path = response.data.equipped_name_effect_path;
  } catch (error) {
    console.error('Failed to load equipped state:', error.response?.data || error.message);
  }
};

const updateUser = (updatedFields) => {
  Object.assign(props.user, updatedFields);
};

const getNameEffectClass = (path) => {
  if (!path) return '';
  if (path.includes('lamp.svg')) return 'soft-glow';
  if (path.includes('blend.svg')) return 'gradient-fade';
  if (path.includes('badge.svg')) return 'golden-outline';
  if (path.includes('vibrate.svg')) return 'dark-pulse';
  if (path.includes('stars.svg')) return 'cosmic-shine';
  if (path.includes('lightbulb.svg')) return 'neon-edge';
  if (path.includes('snowflake.svg')) return 'frost-glow';
  if (path.includes('flame.svg')) return 'fire-flicker';
  if (path.includes('gem.svg')) return 'emerald-sheen';
  if (path.includes('shadow.svg')) return 'shadow-drift';
  if (path.includes('zap.svg')) return 'electric-glow';
  if (path.includes('moon.svg')) return 'lunar-haze';
  if (path.includes('sun.svg')) return 'solar-flare';
  if (path.includes('waves.svg')) return 'wave-shimmer';
  if (path.includes('diamond.svg')) return 'crystal-pulse';
  if (path.includes('rainbow.svg')) return 'rainbow-gleam';
  return '';
};

onMounted(() => {
  loadEquippedState();
});
</script>

<style scoped>
.equipped-icon {
  width: 2rem;
  height: 2rem;
  position: absolute;
  top: -1.5rem;
  left: 50%;
  transform: translateX(-50%);
}

/* Name Effect Styles */
.soft-glow {
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
}

.gradient-fade {
  background: linear-gradient(to right, #ff7e5f, #feb47b);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.golden-outline {
  color: #fff;
  text-shadow: 0 0 2px #ffd700, 0 0 4px #ffd700, 0 0 6px #ffd700;
}

.dark-pulse {
  color: #fff;
  animation: darkPulse 1.5s infinite;
}

.cosmic-shine {
  color: #fff;
  text-shadow: 0 0 5px #00ffff, 0 0 10px #00ffff, 0 0 15px #00ffff;
}

.neon-edge {
  color: #fff;
  text-shadow: 0 0 5px #ff00ff, 0 0 10px #ff00ff;
}

.frost-glow {
  color: #e0f7ff;
  text-shadow: 0 0 8px rgba(173, 216, 230, 0.8);
}

.fire-flicker {
  color: #ff4500;
  animation: fireFlicker 0.5s infinite;
}

.emerald-sheen {
  color: #00ff7f;
  text-shadow: 0 0 6px rgba(0, 255, 127, 0.7);
}

.shadow-drift {
  color: #fff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  animation: shadowDrift 2s infinite;
}

.electric-glow {
  color: #00b7eb;
  text-shadow: 0 0 5px #00b7eb, 0 0 10px #00b7eb;
  animation: electricGlow 1s infinite;
}

.lunar-haze {
  color: #d3d3d3;
  text-shadow: 0 0 8px rgba(211, 211, 211, 0.6);
}

.solar-flare {
  color: #ffeb3b;
  text-shadow: 0 0 6px #ffeb3b, 0 0 12px #ffeb3b;
  animation: solarFlare 1.2s infinite;
}

.wave-shimmer {
  color: #1e90ff;
  text-shadow: 0 0 5px #1e90ff;
  animation: waveShimmer 2s infinite;
}

.crystal-pulse {
  color: #b9f2ff;
  text-shadow: 0 0 5px #b9f2ff, 0 0 10px #b9f2ff;
  animation: crystalPulse 1.5s infinite;
}

.rainbow-gleam {
  background: linear-gradient(45deg, #ff0000, #ff8000, #ffff00, #00ff00, #00ffff, #0000ff, #8000ff);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  animation: rainbowGleam 3s infinite;
}

/* Animations */
@keyframes darkPulse {
  0% { text-shadow: 0 0 5px #000; }
  50% { text-shadow: 0 0 10px #000; }
  100% { text-shadow: 0 0 5px #000; }
}

@keyframes fireFlicker {
  0% { text-shadow: 0 0 5px #ff4500; }
  50% { text-shadow: 0 0 10px #ff4500, 0 0 15px #ff8c00; }
  100% { text-shadow: 0 0 5px #ff4500; }
}

@keyframes shadowDrift {
  0% { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); }
  50% { text-shadow: 4px 4px 6px rgba(0, 0, 0, 0.7); }
  100% { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); }
}

@keyframes electricGlow {
  0% { text-shadow: 0 0 5px #00b7eb; }
  50% { text-shadow: 0 0 10px #00b7eb, 0 0 15px #00b7eb; }
  100% { text-shadow: 0 0 5px #00b7eb; }
}

@keyframes solarFlare {
  0% { text-shadow: 0 0 6px #ffeb3b; }
  50% { text-shadow: 0 0 12px #ffeb3b, 0 0 18px #ffeb3b; }
  100% { text-shadow: 0 0 6px #ffeb3b; }
}

@keyframes waveShimmer {
  0% { text-shadow: 0 0 5px #1e90ff; }
  50% { text-shadow: 0 0 10px #1e90ff; }
  100% { text-shadow: 0 0 5px #1e90ff; }
}

@keyframes crystalPulse {
  0% { text-shadow: 0 0 5px #b9f2ff; }
  50% { text-shadow: 0 0 10px #b9f2ff, 0 0 15px #b9f2ff; }
  100% { text-shadow: 0 0 5px #b9f2ff; }
}

@keyframes rainbowGleam {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
