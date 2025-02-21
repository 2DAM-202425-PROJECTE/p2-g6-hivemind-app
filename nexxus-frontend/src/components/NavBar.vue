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
  { text: 'Settings', to: '/settings', icon: 'mdi-cog' }

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
  <v-app-bar app flat class="fixed top-0 w-full bg-black shadow-md flex justify-between px-4">
    <div class="left-section flex items-center">
      <img src="/logo.png" alt="Logo" class="logo" />
      <v-btn text :to="'/home'" class="title-btn text-white flex items-center">
        Hivemind
      </v-btn>
    </div>

    <v-text-field class="search-field" v-model="searchQuery" placeholder="Search" hide-details solo flat
                  prepend-inner-icon="mdi-magnify"></v-text-field>

    <div class="right-section flex items-center">
      <template v-if="user">
        <v-btn icon class="text-white ml-2" :to="'/profile'">
          <v-avatar size="32">
            <img :src="user.profilePic" alt="Profile Picture" />
          </v-avatar>
        </v-btn>
        <span class="user-greeting text-white ml-2">{{ user.name }}</span>
        <v-btn text class="text-white ml-2" :to="'/shop'">
          <span>{{ user.credits || 0 }} Credits</span>
        </v-btn>
        <v-btn icon class="text-white ml-2" @click="popup = true">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-btn icon class="text-white ml-2" @click="showNotifications = true">
          <v-icon :class="{ 'has-notifications': hasNotifications }">mdi-bell</v-icon>
        </v-btn>
      </template>

      <v-app-bar-nav-icon @click="menu = !menu" class="text-white ml-2"></v-app-bar-nav-icon>
    </div>
  </v-app-bar>

  <!-- Pop-out Menu -->
  <v-navigation-drawer v-model="menu" temporary location="right" class="bg-black d-flex flex-column justify-between">
    <div>
      <div class="menu-header flex justify-center items-center py-4">
        <img src="/logo.png" alt="Logo" class="menu-logo" />
      </div>
      <v-divider></v-divider>
      <v-list class="menu-list flex flex-col items-center justify-center gap-4">
        <v-list-item
          v-for="item in menuItems"
          :key="item.text"
          :to="item.to"
          class="menu-item text-white flex items-center justify-start w-full px-4"
          @click="item.action && item.action()"
        >
          <v-icon class="mr-4">{{ item.icon }}</v-icon>
          <v-list-item-title>{{ item.text }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </div>
    <div class="logout-container">
      <v-divider></v-divider>
      <template v-if="user">
        <v-list-item
          :to="'#'"
          class="menu-item text-white flex items-center justify-start w-full px-4"
          @click="confirmLogout"
        >
          <v-icon class="mr-4">mdi-logout</v-icon>
          <v-list-item-title>Logout</v-list-item-title>
        </v-list-item>
      </template>
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
        <v-file-input label="Upload Image/Video (.png, .mp4)" accept=".png, .mp4" @change="handleFileUpload"
                      outlined></v-file-input>

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
  background-color: black;
  display: flex;
  justify-content: space-between;
  padding: 0 20px;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
}

.left-section,
.right-section {
  display: flex;
  align-items: center;
}

.logo {
  width: 40px;
  height: 40px;
  margin-right: 10px;
}

.menu-logo {
  width: 60px;
  height: 60px;
  display: block;
}

.user-greeting {
  margin-right: 10px;
  color: #fff;
}

.text-white {
  color: white !important;
}

.search-field {
  max-width: 300px;
  margin: 0 auto;
  flex-grow: 1;
  text-align: center;
  margin-left: 650px; /* Adjust this value to move the search bar to the right */

}

.credits-display {
  display: flex;
  align-items: center;
}

.logout-container {
  position: absolute;
  bottom: 0;
  width: 100%;
}

@media (max-width: 1024px) {
  .search-field {
    max-width: 200px;
  }
}
.has-notifications::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 10px;
  height: 10px;
  background-color: red;
  border-radius: 50%;
}
</style>
