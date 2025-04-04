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
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h3>
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
  user: {
    type: Object,
    required: true,
  },
  isCurrentUser: {
    type: Boolean,
    required: true,
  },
  editProfile: {
    type: Function,
    required: true,
  },
  connectSocial: {
    type: Function,
    default: () => console.log('Connect Social clicked'),
  },
  shareProfile: {
    type: Function,
    required: true,
  },
});

const showInventory = ref(false);

const equippedBackgroundClass = computed(() => {
  return props.user.equipped_background_path ? `bg-[url(${props.user.equipped_background_path})]` : '';
});

// Load equipped state
const loadEquippedState = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/equipped-items`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    props.user.equipped_profile_icon_path = response.data.equipped_profile_icon_path;
    props.user.equipped_background_path = response.data.equipped_background_path;
  } catch (error) {
    console.error('Failed to load equipped state:', error.response?.data || error.message);
  }
};

// Update user data from inventory modal
const updateUser = (updatedFields) => {
  Object.assign(props.user, updatedFields);
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
</style>
