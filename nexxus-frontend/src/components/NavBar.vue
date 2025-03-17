<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from '../axios'
import { clearAuthToken } from '../auth'
import NotificationsModal from './NotificationsModal.vue' // Import the NotificationsModal component

const menu = ref(false)
const searchQuery = ref('')
const user = ref(null)
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

const logout = async () => {
  try {
    const token = localStorage.getItem("token");

    if (!token) {
      throw new Error("No token found");
    }

    await axios.post('/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    localStorage.removeItem("token");
    clearAuthToken();
    user.value = null;
    window.location.href = "/";
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
})

watch([menu, showNotifications], ([newMenu, newShowNotifications]) => {
  if (newMenu && newShowNotifications) {
    showNotifications.value = false
  }
})
const hasNotifications = computed(() => notifications.value.length > 0);

</script>


<template>
  <!-- v-app-bar sin cambios -->
  <v-app-bar app flat class="bg-black shadow-md flex items-center justify-between px-4 md:px-6" style="z-index: 9999;">
    <!-- Left Section -->
    <div class="flex items-center space-x-2">
      <v-btn icon :to="'/home'" class="text-white">
        <img src="/logo.png" alt="Logo" class="h-10 w-10 md:h-12 md:w-12"/>
      </v-btn>
      <v-btn text :to="'/home'" class="text-white text-lg hidden sm:flex items-center">
        Hivemind
      </v-btn>
    </div>

    <!-- Search Field -->
    <div class="flex-1 mx-4" ref="searchContainer">
      <v-text-field
        v-model="searchQuery"
        placeholder="Search users..."
        hide-details
        solo
        flat
        prepend-inner-icon="mdi-magnify"
        class="bg-gray-800 text-white rounded-lg"
        @focus="showSearchResults = true"
        @input="searchUsers"
        @blur="handleBlur"
      ></v-text-field>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2">
      <template v-if="user">
        <v-btn icon :to="`/profile/${user.username}`" class="text-white">
          <v-avatar size="32">
            <img
              :src="getProfilePhotoUrl"
              alt="Profile Picture"
              class="object-cover w-full h-full"
              @error="e => e.target.src = '/default-profile.jpg'"
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
          <v-icon
            :class="{ 'relative after:content-[\'\'] after:absolute after:top-0 after:right-0 after:w-2 after:h-2 after:bg-red-500 after:rounded-full': hasNotifications }">
            mdi-bell
          </v-icon>
        </v-btn>
      </template>
      <v-app-bar-nav-icon @click="menu = !menu" class="text-white"></v-app-bar-nav-icon>
    </div>
  </v-app-bar>

  <!-- Lista con separadores y ajustes -->
  <div
    v-if="showSearchResults && searchQuery"
    class="search-results"
    :style="searchResultsStyle"
    ref="searchResultsContainer"
    @mousedown="preventBlur"
  >
    <ul class="py-1">
      <v-list-item
        v-for="(result, index) in searchResults"
        :key="result.id"
        @click="goToUserProfile(result.username)"
        class="px-4 py-2 hover:bg-gray-700 cursor-pointer flex items-center"
        :class="{ 'border-b border-gray-700': index < searchResults.length - 1 && (searchResults.length > 0 || index === 0) }"
      >
        <!-- Contenedor de la imagen -->
        <v-avatar size="32" class="mr-3 flex-shrink-0">
          <img
            :src="result.profile_photo_path ? `http://localhost:8000/storage/${result.profile_photo_path}` : '/default-profile.jpg'"
            alt="User Avatar"
            class="object-cover w-full h-full"
            @error="e => e.target.src = '/default-profile.jpg'"
          />
        </v-avatar>

        <!-- Contenedor del texto -->
        <div class="flex flex-col justify-center flex-grow">
          <span class="text-white font-medium text-base leading-tight">{{ result.name }}</span>
          <span class="text-gray-400 text-sm leading-tight">@{{ result.username }}</span>
        </div>
      </v-list-item>
      <li
        v-if="searchResults.length === 0"
        class="px-4 py-2 text-gray-400 text-sm"
      >
        No users found
      </li>
    </ul>
  </div>

  <!-- Updated Navigation Drawer -->
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


  <!-- Create a Story Popup -->
  <v-dialog v-model="storyPopup" max-width="500">
    <v-card>
      <v-card-title>Create a Story</v-card-title>
      <v-card-text>
        <v-file-input v-model="storyFile" label="Upload Image/Video (.png, .mp4)" accept=".png, .mp4" @change="handleFileUpload"
                      outlined></v-file-input>

        <v-text-field v-model="storyDescription" label="Description" outlined></v-text-field>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="storyPopup = false">Cancel</v-btn>
        <v-btn color="primary" @click="submitStory">Submit</v-btn>
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

  <NotificationsModal
    :visible="showNotifications"
    :notifications="notifications"
    @update:notifications="updateNotifications"
    @close="showNotifications = false"
  />
</template>
