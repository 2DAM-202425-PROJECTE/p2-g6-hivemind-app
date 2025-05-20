<template>
  <!-- v-app-bar -->
  <v-app-bar app flat class="bg-black shadow-md flex items-center justify-between px-4 md:px-6 z-[9999]">
    <!-- Left Section -->
    <div class="flex items-center space-x-2">
      <v-btn icon :to="'/home'" class="text-white">
        <img src="/logo.png" alt="Logo" class="h-10 w-10 md:h-12 md:w-12" />
      </v-btn>
      <v-btn text :to="'/home'" class="text-white text-lg hidden sm:flex items-center">
        Hivemind
      </v-btn>
    </div>

    <!-- Search Field Container -->
    <div class="flex-1 mx-4 relative" ref="searchContainer">
      <v-text-field v-model="searchQuery" placeholder="Search users..." hide-details solo flat
                    prepend-inner-icon="mdi-magnify" class="bg-gray-800 text-white rounded-lg" @focus="handleFocus"
                    @input="debouncedSearchUsers" @blur="handleBlur"></v-text-field>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2">
      <template v-if="user">
        <v-btn icon :to="`/profile/${user.username}`" class="text-white">
          <v-avatar size="48">
            <img :src="getProfilePhotoUrl" alt="Profile Picture" class="object-cover w-full h-full"
                 @error="e => e.target.src = generateAvatar(user.value?.name || 'User')" />
          </v-avatar>
        </v-btn>
        <span class="text-white hidden md:block">{{ user.name }}</span>
        <v-btn text :to="'/shop'" class="text-white hidden md:flex items-center">
          {{ user.credits || 0 }} Credits
        </v-btn>
        <v-btn icon @click="showNotifications = true" class="text-white relative">
          <v-icon>mdi-bell</v-icon>
          <span v-if="notifications.length"
                class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1">
            {{ notifications.length }}
          </span>
        </v-btn>
      </template>
      <v-app-bar-nav-icon @click="menu = !menu" class="text-white"></v-app-bar-nav-icon>
    </div>
  </v-app-bar>

  <!-- Search Results Overlay -->
  <div v-if="showSearchResults && searchQuery" class="fixed inset-0 z-[10000] pointer-events-none">
    <div class="absolute bg-gray-800 text-white rounded-b-lg shadow-lg max-h-60 overflow-y-auto pointer-events-auto"
         :style="searchResultsStyle" @mousedown.prevent>
      <ul v-if="isSearching" class="py-1">
        <li class="px-4 py-2 text-gray-400 text-sm">Searching...</li>
      </ul>
      <ul v-else-if="searchResults.length > 0" class="py-1">
        <v-list-item v-for="(result, index) in searchResults" :key="result.id" @click="goToUserProfile(result.username)"
                     class="px-4 py-2 hover:bg-gray-700 cursor-pointer flex items-center"
                     :class="{ 'border-b border-gray-700': index < searchResults.length - 1 }">
          <v-avatar size="32" class="mr-3 flex-shrink-0">
            <img :src="getSearchResultPhotoUrl(result)" alt="User Avatar" class="object-cover w-full h-full"
                 @error="e => e.target.src = generateAvatar(result.name || 'User')" />
          </v-avatar>
          <div class="flex flex-col justify-center flex-grow">
            <span class="text-white font-medium text-base leading-tight">{{ result.name }}</span>
            <span class="text-gray-400 text-sm leading-tight">@{{ result.username }}</span>
          </div>
        </v-list-item>
      </ul>
      <ul v-else class="py-1">
        <li class="px-4 py-2 text-gray-400 text-sm">No users found</li>
      </ul>
    </div>
  </div>

  <!-- Navigation Drawer -->
  <v-navigation-drawer v-model="menu" temporary location="right" class="bg-black flex flex-col h-full">
    <div class="flex flex-col h-full">
      <div class="py-4 flex justify-center">
        <img src="/logo.png" alt="Logo" class="h-12 w-12" />
      </div>
      <v-divider class="bg-gray-700"></v-divider>
      <v-list class="flex-1 py-4">
        <v-list-item v-for="item in menuItems" :key="item.text" :to="item.to"
                     class="text-white py-4 px-6 flex items-center" @click="item.action && item.action()">
          <v-icon class="mr-4 text-xl flex-shrink-0">{{ item.icon }}</v-icon>
          <v-list-item-title class="text-lg font-medium flex-grow">{{ item.text }}</v-list-item-title>
        </v-list-item>
      </v-list>
      <v-divider class="bg-gray-700"></v-divider>
      <v-list-item v-if="user" @click="confirmLogout" class="text-white py-4 px-6 flex items-center mt-auto">
        <v-icon class="mr-4 text-xl flex-shrink-0">mdi-logout</v-icon>
        <v-list-item-title class="text-lg font-medium flex-grow">Logout</v-list-item-title>
      </v-list-item>
    </div>
  </v-navigation-drawer>

  <!-- Dialogs -->
  <v-dialog v-model="showLogoutConfirm" max-width="400">
    <v-card>
      <v-card-title>Confirm Logout</v-card-title>
      <v-card-text>Are you sure you want to logout?</v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="handleLogoutConfirm(true)">Yes</v-btn>
        <v-btn color="primary" @click="handleLogoutConfirm(false)">No</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Notifications Modal -->
  <NotificationsModal :visible="showNotifications" :notifications="notifications" @close="showNotifications = false"
                      @update:notifications="notifications = $event" />
</template>

<script setup>
import { ref, onMounted, watch, computed, nextTick, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import NotificationsModal from './NotificationsModal.vue';
import { generateAvatar } from '../utils/avatar';
import apiClient from "../axios";

const router = useRouter();
const menu = ref(false);
const searchQuery = ref('');
const user = ref(null);
const showLogoutConfirm = ref(false);
const showNotifications = ref(false);
const searchResults = ref([]);
const showSearchResults = ref(false);
const searchContainer = ref(null);
const searchResultsStyle = ref({});
const isSearching = ref(false);
const notifications = ref([]);

const menuItems = ref([
  { text: 'Messages', to: '/chat', icon: 'mdi-message-text' },
  { text: 'Communities', to: '/servers', icon: 'mdi-account-group' },
  { text: 'Live Streams', to: '/live-streams', icon: 'mdi-video' },
  { text: 'Video Content', to: '/videos', icon: 'mdi-play-circle' },
  { text: 'Store', to: '/shop', icon: 'mdi-store' },
  { text: 'My Profile', to: '/profile', icon: 'mdi-account-circle' },
  { text: 'App Settings', to: '/settings', icon: 'mdi-cog' },
]);

const fetchUser = async () => {
  try {
    const response = await apiClient.get('/api/user');
    user.value = response.data;
    updateMenuItems();
  } catch (error) {
    console.error('Error fetching user:', error);
    user.value = null;
  }
};

const updateMenuItems = () => {
  menuItems.value = menuItems.value.map(item => {
    if (item.text === 'My Profile' && user.value?.username) {
      return { ...item, to: `/profile/${user.value.username}` };
    }
    return item;
  });
};

const logout = async () => {
  try {
    await apiClient.post('/api/logout');
    await router.push('/auth/login');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};

const debouncedSearchUsers = debounce(async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    showSearchResults.value = false;
    return;
  }
  isSearching.value = true;
  try {
    const response = await apiClient.get('/api/search/users', { params: { username: searchQuery.value } });
    searchResults.value = response.data.data || [];
    showSearchResults.value = true;
  } catch (error) {
    console.error('Error searching users:', error);
    searchResults.value = [];
    showSearchResults.value = true;
  } finally {
    isSearching.value = false;
  }
}, 300);

const getSearchResultPhotoUrl = (result) => {
  if (result.profile_photo_url) return result.profile_photo_url;
  if (result.profile_photo_path) {
    return result.profile_photo_path.startsWith('http')
      ? result.profile_photo_path
      : `http://localhost:8000/storage/${result.profile_photo_path}`;
  }
  return generateAvatar(result.name || 'User');
};

const goToUserProfile = (username) => {
  router.push(`/profile/${username}`);
  searchQuery.value = '';
  showSearchResults.value = false;
};

const confirmLogout = () => {
  showLogoutConfirm.value = true;
};

const handleLogoutConfirm = (confirm) => {
  if (confirm) logout();
  showLogoutConfirm.value = false;
};

const fetchNotifications = async () => {
  try {
    const response = await apiClient.get('/api/notifications');
    notifications.value = response.data;
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

const getProfilePhotoUrl = computed(() => {
  if (!user.value) return generateAvatar('User');
  if (user.value.profile_photo_path) {
    return user.value.profile_photo_path.startsWith('http')
      ? user.value.profile_photo_path
      : `http://localhost:8000/storage/${user.value.profile_photo_path}`;
  }
  return generateAvatar(user.value.name || 'User');
});

const handleBlur = () => {
  setTimeout(() => {
    if (!document.activeElement.closest('.search-results')) {
      if (!searchQuery.value.trim()) {
        searchResults.value = [];
        showSearchResults.value = false;
      }
    }
  }, 200);
};

const handleFocus = () => {
  showSearchResults.value = true;
  if (searchQuery.value.trim() && searchResults.value.length === 0 && !isSearching.value) {
    debouncedSearchUsers();
  }
};

const updateSearchResultsPosition = () => {
  if (searchContainer.value) {
    const rect = searchContainer.value.getBoundingClientRect();
    searchResultsStyle.value = {
      top: `${rect.bottom}px`,
      left: `${rect.left}px`,
      width: `${rect.width}px`,
    };
  }
};

watch(showSearchResults, (newValue) => {
  if (newValue) {
    nextTick(() => updateSearchResultsPosition());
    window.addEventListener('scroll', updateSearchResultsPosition);
    window.addEventListener('resize', updateSearchResultsPosition);
  } else {
    if (!searchQuery.value.trim()) {
      searchResults.value = [];
    }
    window.removeEventListener('scroll', updateSearchResultsPosition);
    window.removeEventListener('resize', updateSearchResultsPosition);
  }
});

watch(searchQuery, (newValue) => {
  if (!newValue.trim()) {
    searchResults.value = [];
    showSearchResults.value = false;
  }
});

onMounted(() => {
  fetchUser();
  fetchNotifications();
});

onUnmounted(() => {
  window.removeEventListener('scroll', updateSearchResultsPosition);
  window.removeEventListener('resize', updateSearchResultsPosition);
});

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}
</script>

<style scoped>
.fixed.inset-0 {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
</style>
