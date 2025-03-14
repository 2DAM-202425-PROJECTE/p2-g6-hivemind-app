<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../axios';
import { clearAuthToken } from '../auth';
import NotificationsModal from './NotificationsModal.vue';
import { routes } from '../router';

const router = useRouter();
const menu = ref(false);
const searchQuery = ref('');
const user = ref(null);
const popup = ref(false);
const postPopup = ref(false);
const postContent = ref('');
const postDescription = ref('');
const postFile = ref(null);
const showLogoutConfirm = ref(false);
const showNotifications = ref(false);
const searchResults = ref([]);
const showSearchResults = ref(false);

const notifications = ref([
  { id: 1, message: 'New notification' },
  { id: 2, message: 'Another notification' },
]);

const menuItems = ref([
  { text: 'Chat', to: '/chat', icon: 'mdi-chat' },
  { text: 'Servers', to: '/servers', icon: 'mdi-server' },
  { text: 'Live Now', to: '/live', icon: 'mdi-video' },
  { text: 'Videos', to: '/videos', icon: 'mdi-video-outline' },
  { text: 'Shop', to: '/shop', icon: 'mdi-cart' },
  { text: 'My Profile', to: '/profile', icon: 'mdi-account' },
  { text: 'Account Settings', to: '/account-settings', icon: 'mdi-account-cog' },
  { text: 'App Settings', to: '/settings', icon: 'mdi-cog' },
]);

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user');
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
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No token found');

    await axios.post('/api/logout', {}, {
      headers: { Authorization: `Bearer ${token}` },
    });

    localStorage.removeItem('token');
    clearAuthToken();
    user.value = null;
    router.push('/');
  } catch (error) {
    console.error('Logout failed:', error);
    alert('Logout failed.');
  }
};

const handleFileUpload = (event) => {
  postFile.value = event.target.files[0];
};

const submitPost = async () => {
  if (!postContent.value && !postFile.value) {
    alert('Please enter content or upload a file!');
    return;
  }

  const formData = new FormData();
  formData.append('content', postContent.value);
  formData.append('description', postDescription.value);
  formData.append('id_user', user.value.id);
  formData.append('publish_date', new Date().toISOString());
  if (postFile.value) formData.append('file', postFile.value);

  try {
    await axios.post('/api/posts', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data',
      },
    });
    alert('Post created successfully!');
    postPopup.value = false;
    postContent.value = '';
    postDescription.value = '';
    postFile.value = null;
    router.push('/home');
  } catch (error) {
    console.error('Failed to create post:', error);
    alert('Failed to create post.');
  }
};

const searchUsers = async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    showSearchResults.value = false;
    return;
  }

  try {
    const response = await axios.get('/api/users');
    const allUsers = response.data;
    searchResults.value = allUsers.filter(u =>
      u.username.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      u.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    showSearchResults.value = true;
  } catch (error) {
    console.error('Error searching users:', error);
    searchResults.value = [];
    showSearchResults.value = false;
  }
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

const updateNotifications = (updatedNotifications) => {
  notifications.value = updatedNotifications;
};

const getProfilePhotoUrl = computed(() => {
  if (user.value?.profile_photo_path) {
    return user.value.profile_photo_path.startsWith('http')
      ? user.value.profile_photo_path
      : `http://localhost:8000/storage/${user.value.profile_photo_path}`;
  }
  return '/default-profile.jpg'; // Imagen por defecto
});

const hasNotifications = computed(() => notifications.value.length > 0);

onMounted(() => {
  fetchUser();
});

watch(searchQuery, () => {
  searchUsers();
});

watch([menu, showNotifications], ([newMenu, newShowNotifications]) => {
  if (newMenu && newShowNotifications) {
    showNotifications.value = false;
  }
});
</script>

<template>
  <v-app-bar app flat class="bg-black shadow-md px-4">
    <!-- Left Section -->
    <div class="flex items-center space-x-2">
      <v-btn icon :to="'/home'" class="text-white">
        <img src="/logo.png" alt="Logo" class="h-10 w-10 md:h-12 md:w-12" />
      </v-btn>
      <v-btn text :to="'/home'" class="text-white text-lg hidden sm:flex items-center">
        Hivemind
      </v-btn>
    </div>

    <!-- Search Field -->
    <div class="flex-1 mx-4 relative">
      <v-text-field
        v-model="searchQuery"
        placeholder="Search users..."
        hide-details
        solo
        flat
        prepend-inner-icon="mdi-magnify"
        class="bg-gray-800 text-white rounded-lg"
        @focus="showSearchResults = true"
        @blur="setTimeout(() => showSearchResults = false, 200)"
      ></v-text-field>
      <div
        v-if="showSearchResults && searchResults.length"
        class="absolute top-full left-0 w-full bg-white dark:bg-gray-800 shadow-lg rounded-b-lg z-10 max-h-60 overflow-y-auto"
      >
        <v-list>
          <v-list-item
            v-for="result in searchResults"
            :key="result.id"
            @click="goToUserProfile(result.username)"
            class="hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
          >
            <v-list-item-avatar>
              <img :src="result.profile_photo_path || '/default-profile.jpg'" alt="User Avatar" />
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>{{ result.name }}</v-list-item-title>
              <v-list-item-subtitle>@{{ result.username }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2">
      <template v-if="user">
        <v-btn icon :to="`/profile/${user.username}`" class="text-white">
          <v-avatar size="32">
            <img :src="getProfilePhotoUrl" alt="Profile Picture" @error="e => e.target.src = '/default-profile.jpg'" />
          </v-avatar>
        </v-btn>
        <span class="text-white hidden md:block">{{ user.name }}</span>
        <v-btn text :to="'/shop'" class="text-white hidden md:flex items-center">
          {{ user.credits || 0 }} Credits
        </v-btn>
        <v-btn icon @click="popup = true" class="text-white">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-btn icon @click="showNotifications = true" class="text-white relative">
          <v-icon :class="{ 'has-notifications': hasNotifications }">mdi-bell</v-icon>
        </v-btn>
      </template>
      <v-app-bar-nav-icon @click="menu = !menu" class="text-white"></v-app-bar-nav-icon>
    </div>
  </v-app-bar>

  <!-- Navigation Drawer -->
  <v-navigation-drawer v-model="menu" temporary location="right" class="bg-black">
    <div class="flex flex-col h-full">
      <div class="py-4 flex justify-center">
        <img src="/logo.png" alt="Logo" class="h-12 w-12" />
      </div>
      <v-divider class="bg-gray-700"></v-divider>
      <v-list class="flex-1">
        <v-list-item
          v-for="item in menuItems"
          :key="item.text"
          :to="item.to"
          class="text-white"
          @click="item.action && item.action()"
        >
          <v-icon class="mr-4">{{ item.icon }}</v-icon>
          <v-list-item-title>{{ item.text }}</v-list-item-title>
        </v-list-item>
      </v-list>
      <v-divider class="bg-gray-700"></v-divider>
      <v-list-item v-if="user" @click="confirmLogout" class="text-white">
        <v-icon class="mr-4">mdi-logout</v-icon>
        <v-list-item-title>Logout</v-list-item-title>
      </v-list-item>
    </div>
  </v-navigation-drawer>

  <!-- Create Post Options Popup -->
  <v-dialog v-model="popup" max-width="400">
    <v-card>
      <v-card-title>Select an option</v-card-title>
      <v-card-text>
        <v-btn block class="mb-2" @click="popup = false">Create a Story</v-btn>
        <v-btn block @click="postPopup = true; popup = false">Create a Post (Image/Video)</v-btn>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="popup = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Create a Post Popup -->
  <v-dialog v-model="postPopup" max-width="500">
    <v-card>
      <v-card-title>Create a Post</v-card-title>
      <v-card-text>
        <v-file-input
          label="Upload Image/Video (.png, .mp4)"
          accept=".png, .mp4"
          @change="handleFileUpload"
          outlined
        ></v-file-input>
        <v-text-field v-model="postDescription" label="Description" outlined></v-text-field>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="postPopup = false">Cancel</v-btn>
        <v-btn color="primary" @click="submitPost">Post</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Logout Confirmation Dialog -->
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
  <NotificationsModal
    :visible="showNotifications"
    :notifications="notifications"
    @update:notifications="updateNotifications"
    @close="showNotifications = false"
  />
</template>

<style scoped>
.v-app-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
}

.search-field {
  background-color: #1f2937; /* gray-800 */
  color: white;
}

.search-field :deep(.v-input__slot) {
  background-color: #1f2937 !important;
  border-radius: 0.5rem;
}

.has-notifications::after {
  content: '';
  position: absolute;
  top: 2px;
  right: 2px;
  width: 8px;
  height: 8px;
  background-color: red;
  border-radius: 50%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .v-app-bar {
    padding: 0 0.5rem;
  }
  .search-field {
    margin-left: 0;
    max-width: 100%;
  }
}
</style>
