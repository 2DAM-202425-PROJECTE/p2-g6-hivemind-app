<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../axios'
import { clearAuthToken } from '../auth'
import NotificationsModal from './NotificationsModal.vue' // Import the NotificationsModal component
import { debounce } from 'lodash'

const router = useRouter()
const menu = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const showSearchResults = ref(false)
const user = ref(null)
const users = ref([])
const popup = ref(false)
const postPopup = ref(false)
const storyPopup = ref(false)
const storyContent = ref('')
const storyDescription = ref('')
const storyFile = ref(null)
const postContent = ref('')
const postDescription = ref('')
const postFile = ref(null)
const showLogoutConfirm = ref(false) // Add this line
const showNotifications = ref(false) // Add this line
const notifications = ref([   // Add notifications here
  { id: 1, message: 'New notification' },
  { id: 2, message: 'Another notification' }
])
const menuItems = ref([
  { text: 'Chat', to: '/chat', icon: 'mdi-chat' },
  { text: 'Servers', to: '/servers', icon: 'mdi-server' },
  { text: 'Live Now', to: '/live', icon: 'mdi-video' },
  { text: 'Videos', to: '/videos', icon: 'mdi-video-outline' },
  { text: 'Shop', to: '/shop', icon: 'mdi-cart' },
  { text: 'My Profile', to: '/profile', icon: 'mdi-account' },
  { text: 'Account Settings', to: '/account-settings', icon: 'mdi-account-cog' }, // Add account settings
  { text: 'App Settings', to: '/settings', icon: 'mdi-cog' } // Add app settings
])

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
    if (!user.value) {
      menuItems.value.push(
        { text: 'Login', to: '/login', icon: 'mdi-login' },
        { text: 'Register', to: '/register', icon: 'mdi-account-plus' }
      )
    }
  } catch (error) {
    console.log(error)
  }
}

const fetchUsers = async () => {
  try {
    const response = await axios.get('/api/users')
    users.value = response.data.data
  } catch (error) {
    console.log(error)
  }
}

const searchUsers = () => {
  if (searchQuery.value.trim() === '') {
    searchResults.value = []
    showSearchResults.value = false
    return
  }
  searchResults.value = users.value.filter(user => 
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
  showSearchResults.value = true
  console.log(searchResults.value)
}

const debouncedSearchUsers = debounce(searchUsers, 300)

const handleBlur = () => {
  setTimeout(() => {
    showSearchResults.value = false
  }, 200)
}

const preventBlur = (e) => {
  e.preventDefault()
}

const goToUserProfile = (username) => {
  router.push(`/profile/${username}`)
}

const logout = async () => {
  try {
    const token = localStorage.getItem("token")

    if (!token) {
      throw new Error("No token found")
    }

    await axios.post('/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    localStorage.removeItem("token")
    clearAuthToken()
    user.value = null
    window.location.href = "/"
  } catch (err) {
    console.error(err)
    alert('Logout failed.')
  }
}

const handleFileUpload = (event) => {
  postFile.value = event.target.files[0]
}

const submitStory = async () => {
  if (!storyDescription.value && !storyFile.value) {
    alert('Please enter a description or upload a file!')
    return
  }

  const formData = new FormData()
  formData.append('description', storyDescription.value)
  formData.append('id_user', user.value.id) // Assuming user is logged in
  formData.append('publish_date', new Date().toISOString()) // Add publish_date

  if (storyFile.value) {
    formData.append('file', storyFile.value)
  }

  try {
    const response = await axios.post('http://localhost:8000/api/stories', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        Accept: 'application/json',
        'Content-Type': 'multipart/form-data'
      }
    })
    alert('Story created successfully!')
    storyPopup.value = false
    storyDescription.value = ''
    storyFile.value = null

    // Update the home page with the new story
    window.location.href = "/home"
  } catch (error) {
    console.error(error)
    alert('Failed to create story.')
  }
}

const submitPost = async () => {
  localStorage.setItem('postContent', postContent.value)

  if (!postContent.value && !postFile.value) {
    alert('Please enter content or upload a file!')
    return
  }

  const formData = new FormData()
  formData.append('content', postContent.value)
  formData.append('description', postDescription.value)
  formData.append('id_user', user.value.id) // Assuming user is logged in
  formData.append('publish_date', new Date().toISOString()) // Add publish_date

  if (postFile.value) {
    formData.append('file', postFile.value)
  }

  try {
    const response = await axios.post('http://localhost:8000/api/posts', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        Accept: 'application/json',
        'Content-Type': 'multipart/form-data'
      }
    })
    alert('Post created successfully!')
    postPopup.value = false
    postContent.value = ''
    postDescription.value = ''
    postFile.value = null

    // Update the home page with the new post
    window.location.href = "/home"
  } catch (error) {
    console.error(error)
    alert('Failed to create post.')
  }
}

const confirmLogout = () => {
  showLogoutConfirm.value = true
}

const handleLogoutConfirm = (confirm) => {
  if (confirm) {
    logout()
  }
  showLogoutConfirm.value = false
}

const updateNotifications = (updatedNotifications) => {
  notifications.value = updatedNotifications
}

onMounted(() => {
  fetchUser()
  fetchUsers()
})

watch(searchQuery, () => {
  debouncedSearchUsers()
})

watch([menu, showNotifications], ([newMenu, newShowNotifications]) => {
  if (newMenu && newShowNotifications) {
    showNotifications.value = false
  }
})
const hasNotifications = computed(() => notifications.value.length > 0)

</script>

<template>
  <!-- v-app-bar -->
  <v-app-bar app flat class="bg-black shadow-md flex items-center justify-between px-4 md:px-6 z-[9999]">
    <!-- Left Section -->
    <div class="flex items-center space-x-2">
      <v-btn icon :to="'/home'" class="text-white">
        <img src="/logo.png" alt="Logo" class="h-10 w-10 md:h-12 md:w-12"/>
      </v-btn>
      <v-btn text :to="'/home'" class="text-white text-lg hidden sm:flex items-center">
        Hivemind
      </v-btn>
    </div>

    <!-- Search Field Container -->
    <div class="flex-1 mx-4 relative" ref="searchContainer">
      <v-text-field
        v-model="searchQuery"
        placeholder="Search users..."
        hide-details
        solo
        flat
        prepend-inner-icon="mdi-magnify"
        class="bg-gray-800 text-white rounded-lg"
        @focus="showSearchResults = true"
        @focus="handleFocus"
        @input="debouncedSearchUsers"
        @blur="handleBlur"
      ></v-text-field>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2">
      <template v-if="user">
        <v-btn icon :to="`/profile/${user.username}`" class="text-white">
          <v-avatar size="48">
            <img
              :src="getProfilePhotoUrl"
              alt="Profile Picture"
              class="object-cover w-full h-full"
              @error="e => e.target.src = generateAvatar(user.value?.name || 'User')"
            />
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
          <v-icon class="relative" :class="{ 'after:content-[\'\'] after:absolute after:top-0 after:right-0 after:w-2 after:h-2 after:bg-red-500 after:rounded-full': hasNotifications }">
            mdi-bell
          </v-icon>
        </v-btn>
      </template>
      <v-app-bar-nav-icon @click="menu = !menu" class="text-white"></v-app-bar-nav-icon>
    </div>
  </v-app-bar>

  <!-- Search Results Overlay -->
  <div
    v-if="showSearchResults && searchQuery.trim()"
    class="search-results"
    :style="searchResultsStyle"
    ref="searchResultsContainer"
    @mousedown="preventBlur"
    v-if="showSearchResults && searchQuery"
    class="fixed inset-0 z-[10000] pointer-events-none"
  >
    <div
      class="absolute bg-gray-800 text-white rounded-b-lg shadow-lg max-h-60 overflow-y-auto pointer-events-auto"
      :style="searchResultsStyle"
      @mousedown.prevent
    >
      <ul v-if="isSearching" class="py-1">
        <li class="px-4 py-2 text-gray-400 text-sm">Searching...</li>
      </ul>
      <ul v-else-if="searchResults.length > 0" class="py-1">
        <v-list-item
          v-for="(result, index) in searchResults"
          :key="result.id"
          @click="goToUserProfile(result.username)"
          class="px-4 py-2 hover:bg-gray-700 cursor-pointer flex items-center"
          :class="{ 'border-b border-gray-700': index < searchResults.length - 1 }"
        >
          <v-avatar size="32" class="mr-3 flex-shrink-0">
            <img
              :src="getSearchResultPhotoUrl(result)"
              alt="User Avatar"
              class="object-cover w-full h-full"
              @error="e => e.target.src = generateAvatar(result.name || 'User')"
            />
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
        <img src="/logo.png" alt="Logo" class="h-12 w-12"/>
      </div>
      <v-divider class="bg-gray-700"></v-divider>
      <v-list class="flex-1 py-4">
        <v-list-item
          v-for="item in menuItems"
          :key="item.text"
          :to="item.to"
          class="text-white py-4 px-6 flex items-center"
          @click="item.action && item.action()"
        >
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
  <v-dialog v-model="popup" max-width="400">
    <v-card>
      <v-card-title>Select an option</v-card-title>
      <v-card-text>
        <v-btn block class="mb-2" @click="storyPopup = true; popup = false">Create a Story</v-btn>
        <v-btn block @click="postPopup = true; popup = false">Create a Post (Image/Video)</v-btn>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="popup = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <v-dialog v-model="postPopup" max-width="500">
    <v-card>
      <v-card-title>Create a Post</v-card-title>
      <v-card-text>
        <v-file-input
          label="Upload Image/Video (.png, .mp4)"
          accept=".png, .mp4"
          v-model="postFile"
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

  <v-dialog v-model="storyPopup" max-width="500">
    <v-card>
      <v-card-title>Create a Story</v-card-title>
      <v-card-text>
        <v-file-input
          v-model="storyFile"
          label="Upload Image/Video (.png, .mp4)"
          accept=".png, .mp4"
          outlined
        ></v-file-input>
        <v-text-field v-model="storyDescription" label="Description" outlined></v-text-field>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="storyPopup = false">Cancel</v-btn>
        <v-btn color="primary" @click="submitStory">Submit</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

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

  <!-- Snackbars -->
  <v-snackbar
    v-model="showPostSuccessSnackbar"
    :timeout="3000"
    color="black"
    class="white--text"
    bottom
    right
  >
    <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
    Post Created Successfully!
  </v-snackbar>

  <v-snackbar
    v-model="showPostFailedSnackbar"
    :timeout="3000"
    color="black"
    class="white--text"
    bottom
    right
  >
    <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
    Post Creation Failed - Retrying in a moment...
  </v-snackbar>

  <v-snackbar
    v-model="showStorySuccessSnackbar"
    :timeout="3000"
    color="black"
    class="white--text"
    bottom
    right
  >
    <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
    Story Created Successfully!
  </v-snackbar>

  <v-snackbar
    v-model="showStoryFailedSnackbar"
    :timeout="3000"
    color="black"
    class="white--text"
    bottom
    right
  >
    <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
    Story Creation Failed - Retrying in a moment...
  </v-snackbar>

  <NotificationsModal
    :visible="showNotifications"
    :notifications="notifications"
    @update:notifications="updateNotifications"
    @close="showNotifications = false"
  />
</template>

<script setup>
import { ref, onMounted, watch, computed, nextTick, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../axios';
import { clearAuthToken } from '../auth';
import NotificationsModal from './NotificationsModal.vue';
import { generateAvatar } from '../utils/avatar';

const router = useRouter();
const menu = ref(false);
const searchQuery = ref('');
const user = ref(null);
const popup = ref(false);
const postPopup = ref(false);
const storyPopup = ref(false);
const postDescription = ref('');
const postFile = ref(null);
const storyFile = ref(null);
const storyDescription = ref('');
const showLogoutConfirm = ref(false);
const showNotifications = ref(false);
const searchResults = ref([]);
const showSearchResults = ref(false);
const showPostSuccessSnackbar = ref(false);
const showPostFailedSnackbar = ref(false);
const showStorySuccessSnackbar = ref(false); // Added for story success
const showStoryFailedSnackbar = ref(false);  // Added for story failure
const searchContainer = ref(null);
const searchResultsStyle = ref({});
const isSearching = ref(false);

const notifications = ref([
  { id: 1, message: 'New notification' },
  { id: 2, message: 'Another notification' },
]);

const menuItems = ref([
  { text: 'Messages', to: '/chat', icon: 'mdi-message-text' },
  { text: 'Communities', to: '/servers', icon: 'mdi-account-group' },
  { text: 'Live Streams', to: '/live', icon: 'mdi-video' },
  { text: 'Video Content', to: '/videos', icon: 'mdi-play-circle' },
  { text: 'Store', to: '/shop', icon: 'mdi-store' },
  { text: 'My Profile', to: '/profile', icon: 'mdi-account-circle' },
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
    await axios.post('/api/logout', {}, { headers: { Authorization: `Bearer ${token}` } });
    localStorage.removeItem('token');
    clearAuthToken();
    user.value = null;
    router.push('/');
  } catch (error) {
    console.error('Logout failed:', error);
    alert('Logout failed.');
  }
};

const submitPost = async () => {
  if (!postFile.value && !postDescription.value) {
    alert('Please upload a file or add a description!');
    return;
  }
  const formData = new FormData();
  formData.append('description', postDescription.value);
  formData.append('id_user', user.value.id);
  formData.append('publish_date', new Date().toISOString());
  if (postFile.value) formData.append('file', postFile.value);
  try {
    await axios.post('/api/posts', formData, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'multipart/form-data' },
    });
    postPopup.value = false;
    showPostSuccessSnackbar.value = true;
    setTimeout(() => {
      showPostSuccessSnackbar.value = false;
      postDescription.value = '';
      postFile.value = null;
      router.push('/home');
    }, 3000);
  } catch (error) {
    console.error('Failed to create post:', error);
    postPopup.value = false;
    showPostFailedSnackbar.value = true;
    setTimeout(() => {
      showPostFailedSnackbar.value = false;
      postPopup.value = true;
    }, 3000);
  }
};

const submitStory = async () => {
  if (!storyFile.value && !storyDescription.value) {
    alert('Please upload a file or add a description!');
    return;
  }
  const formData = new FormData();
  formData.append('description', storyDescription.value);
  formData.append('id_user', user.value.id);
  formData.append('publish_date', new Date().toISOString());
  if (storyFile.value) formData.append('file', storyFile.value);
  try {
    await axios.post('/api/stories', formData, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'multipart/form-data' },
    });
    storyPopup.value = false;
    showStorySuccessSnackbar.value = true; // Use story-specific success snackbar
    setTimeout(() => {
      showStorySuccessSnackbar.value = false;
      storyDescription.value = '';
      storyFile.value = null;
      router.push('/home');
    }, 3000);
  } catch (error) {
    console.error('Failed to create story:', error);
    storyPopup.value = false;
    showStoryFailedSnackbar.value = true; // Use story-specific failure snackbar
    setTimeout(() => {
      showStoryFailedSnackbar.value = false;
      storyPopup.value = true;
    }, 3000);
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
    const response = await axios.get('/api/search/users', { params: { username: searchQuery.value } });
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

const updateNotifications = (updatedNotifications) => {
  notifications.value = updatedNotifications;
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

const hasNotifications = computed(() => notifications.value.length > 0);

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
});

onUnmounted(() => {
  window.removeEventListener('scroll', updateSearchResultsPosition);
  window.removeEventListener('resize', updateSearchResultsPosition);
  showPostSuccessSnackbar.value = false;
  showPostFailedSnackbar.value = false;
  showStorySuccessSnackbar.value = false; // Cleanup story snackbars
  showStoryFailedSnackbar.value = false;
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
