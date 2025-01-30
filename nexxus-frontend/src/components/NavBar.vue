<template>
  <v-app-bar app flat>
    <div class="left-section">
      <img src="/logo.png" alt="Logo" class="logo" />
      <v-btn text :to="'/'" class="title-btn">Hivemind</v-btn>
    </div>

    <!-- Centered and Responsive Search Input Field -->
    <v-text-field
      class="search-field"
      v-model="searchQuery"
      @input="highlightText"
      placeholder="Search"
      hide-details
      solo
      flat
      prepend-inner-icon="mdi-magnify"
    ></v-text-field>

    <div class="right-section">
      <v-app-bar-nav-icon v-if="isMobile" @click="menu = !menu"></v-app-bar-nav-icon>
      <v-menu v-if="isMobile" v-model="menu" offset-y>
        <v-list>
          <v-list-item v-for="item in menuItems" :key="item.text" :to="item.to">
            <v-list-item-icon>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>{{ item.text }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
      <template v-else>
        <v-tooltip bottom v-for="item in menuItems" :key="item.text">
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon :to="item.to" class="nav-btn" v-bind="attrs" v-on="on">
              <v-icon>{{ item.icon }}</v-icon>
            </v-btn>
          </template>
          <span>{{ item.text }}</span>
        </v-tooltip>
      </template>
      <template v-if="user">
        <span class="user-greeting">Hello, {{ user.name }}</span>
        <v-btn text @click="logout">Logout</v-btn>
      </template>
    </div>
  </v-app-bar>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../axios'
import apiClient from "../axios.js"
import { clearAuthToken } from '../auth'

const isMobile = ref(window.innerWidth <= 768)
const menu = ref(false)
const searchQuery = ref('')
const user = ref(null)

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth <= 768
})

const menuItems = [
  { text: 'Contact', to: '/contact', icon: 'mdi-phone' },
  { text: 'Talk', to: '/talk', icon: 'mdi-chat' },
  { text: 'Live Now', to: '/live', icon: 'mdi-video' },
  { text: 'Videos', to: '/videos', icon: 'mdi-video-outline' },
  { text: 'My Profile', to: '/profile', icon: 'mdi-account' },
  { text: 'Login', to: '/login', icon: 'mdi-login' },
  { text: 'Register', to: '/register', icon: 'mdi-account-plus' },
]

const highlightText = () => {
  const query = searchQuery.value
  const content = document.querySelector('.content') // Adjust the selector to target your content
  if (content) {
    const regex = new RegExp(`(${query})`, 'gi')
    content.innerHTML = content.textContent.replace(regex, '<span class="highlight">$1</span>')
  }
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/user')
    user.value = response.data
  } catch (error) {
    console.log(error)
  }
}

const logout = async () => {
  try {
    await apiClient.post('/api/logout')
    localStorage.removeItem("token")
    clearAuthToken()
    user.value = null
    window.location.href = "/"
  } catch (err) {
    alert('Logout failed.')
  }
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
.v-app-bar {
  background-color: transparent;
  box-shadow: none;
  display: flex;
  justify-content: space-between;
  padding: 0 20px;
  position: relative;
}

.left-section, .right-section {
  display: flex;
  align-items: center;
}

.left-section {
  position: absolute;
  left: 20px;
}

.right-section {
  position: absolute;
  right: 20px;
}

.v-toolbar-title {
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.v-btn {
  color: #fff;
  display: flex;
  align-items: center;
  background-color: transparent;
  border: none;
  justify-content: center;
}

.nav-btn {
  margin-right: 8px;
}

.title-btn {
  margin-left: 0;
  padding-left: 0;
  max-width: 200px;
  display: flex;
  align-items: center;
}

.search-field {
  max-width: 600px; /* Adjusted width for full window */
  width: 100%;
  margin: 0 auto;   /* Center the search bar */
  border-radius: 25px;
  overflow: hidden;
  height: 50px;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
}

.logo {
  width: 40px;
  height: 40px;
  margin-right: 10px;
}

.user-greeting {
  margin-right: 10px;
  color: #fff;
}

@media (max-width: 1024px) {
  .search-field {
    max-width: 250px; /* Smaller width for half window */
    position: static;
    transform: none;
    margin-left: 200px; /* Move more to the right */
  }

  .right-section {
    margin-left: auto;
  }
}

@media (max-width: 768px) {
  .search-field {
    max-width: 200px;
    position: static;
    transform: none;
    margin-left: 200px; /* Move more to the right */
  }

  .nav-btn {
    display: none;
  }

  .right-section {
    margin-left: auto;
  }
}

@media (max-width: 512px) {
  .search-field {
    max-width: 150px;
    position: static;
    transform: none;
    margin-left: 200px; /* Move more to the right */
  }
}

.highlight {
  background-color: yellow;
}
</style>
