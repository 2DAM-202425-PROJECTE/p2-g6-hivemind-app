<template>
  <div class="chat-container">
    <Navbar />
    <h1>Messages</h1>
    <div class="chat-content">
      <div class="chat-list">
        <div class="chat-item" v-for="(chat, index) in ChatsList" :key="index" @click="selectChat(chat)">
          <img :src="chat.avatar" alt="Avatar" class="chat-avatar" />
          <div class="chat-info">
            <h3>{{ chat.name }}</h3>
            <p>{{ chat.email }}</p>
          </div>
        </div>
      </div>
      <div class="chat-area" v-if="selectedChat">
        <div class="chat-header">
          <img :src="selectedChat.avatar" alt="Avatar" class="chat-avatar" />
          <h3>{{ selectedChat.name }}</h3>
          <div class="chat-options">
            <button @click.stop="toggleChatMenu('header')">...</button>
            <div v-if="chatMenuVisible === 'header'" class="dropdown-menu">
              <ul>
                <li @click="viewProfile(selectedChat)">View Profile</li>
                <li @click="reportProfile(selectedChat)">Report Profile</li>
                <li @click="blockProfile(selectedChat)">Block Profile</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="chat-messages">
          <div class="message" v-for="(message, index) in selectedChat.messages" :key="index" @contextmenu.prevent="showMessageMenu($event, index, message.isMine)">
            <p :class="{'my-message': message.isMine, 'their-message': !message.isMine}">{{ message.text }}</p>
          </div>
        </div>
        <div class="chat-input">
          <v-text-field v-model="newMessage" label="Type a message" @keyup.enter="sendMessage" />
          <v-btn @click="sendMessage">Send</v-btn>
        </div>
      </div>
    </div>
    <Footer />
    <div v-if="messageMenuVisible" class="context-menu" :style="{ top: `${menuPosition.y}px`, left: `${menuPosition.x}px` }">
      <ul v-if="isMine">
        <li @click="editMessage(selectedMessageIndex)">Edit</li>
        <li @click="deleteMessage(selectedMessageIndex)">Delete</li>
      </ul>
      <ul v-else>
        <li @click="reportMessage(selectedMessageIndex)">Report</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import apiClient from "@/axios.js";

const ChatsList = ref([])

const selectedChat = ref(null)
const newMessage = ref('')
const chatMenuVisible = ref(null)
const messageMenuVisible = ref(false)
const menuPosition = ref({ x: 0, y: 0 })
const selectedMessageIndex = ref(null)
const isMine = ref(false)

const fetchUsers = async () => {
  try {
    const response = await apiClient.get('/api/users', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    ChatsList.value = response.data
  } catch (error) {
    console.error("Error al obtener la lista de usuarios:", error.response?.data || error.message)
  }
}

const selectChat = (chat) => {
  selectedChat.value = chat
}

const sendMessage = () => {
  if (newMessage.value.trim() !== '') {
    selectedChat.value.messages.push({ text: newMessage.value, isMine: true })
    newMessage.value = ''
  }
}

const toggleChatMenu = (index) => {
  chatMenuVisible.value = chatMenuVisible.value === index ? null : index
}

const showMessageMenu = (event, index, mine) => {
  selectedMessageIndex.value = index
  isMine.value = mine
  menuPosition.value = { x: event.clientX, y: event.clientY }
  messageMenuVisible.value = true
}

const viewProfile = (chat) => {
  alert(`Viewing profile of ${chat.name}`)
}

const reportProfile = (chat) => {
  alert(`Reporting profile of ${chat.name}`)
}

const blockProfile = (chat) => {
  alert(`Blocking profile of ${chat.name}`)
}

const editMessage = (index) => {
  alert(`Editing message at index ${index}`)
  messageMenuVisible.value = false
}

const deleteMessage = (index) => {
  alert(`Deleting message at index ${index}`)
  messageMenuVisible.value = false
}

const reportMessage = (index) => {
  alert(`Reporting message at index ${index}`)
  messageMenuVisible.value = false
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu') && !event.target.closest('.chat-options button')) {
    messageMenuVisible.value = false
    chatMenuVisible.value = null
  }
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside)
  fetchUsers();
})

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.chat-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: #f0f2f5;
  min-height: 100vh;
  color: #1c1e21;
  padding-bottom: 90px;
  display: flex;
  flex-direction: column;
}

h1 {
  font-size: 24px;
  margin-bottom: 20px;
  text-align: center;
}

.chat-content {
  display: flex;
  gap: 20px;
  flex: 1;
}

.chat-list {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 300px;
  width: 100%;
  overflow-y: auto;
  flex: 1;
}

.new-chat-button {
  background-color: #7f7f7f;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  margin-bottom: 10px;
}

.new-chat-button:hover {
  background-color: #0056b3;
}

.chat-item {
  display: flex;
  gap: 10px;
  align-items: center;
  padding: 10px;
  cursor: pointer;
  border-bottom: 1px solid #e0e0e0;
  position: relative;
}

.chat-item:hover {
  background-color: #f0f0f0;
}

.chat-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.chat-info h3 {
  font-size: 16px;
  margin: 0;
}

.chat-info p {
  font-size: 12px;
  margin: 0;
  color: #7f7f7f;
}

.chat-options {
  margin-left: auto;
  position: relative;
}

.chat-options button {
  background: none;
  border: none;
  cursor: pointer;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown-menu li {
  padding: 10px;
  cursor: pointer;
}

.dropdown-menu li:hover {
  background: #f0f0f0;
}

.chat-area {
  flex: 3;
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.chat-header {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 20px;
  position: relative;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 20px;
}

.message {
  margin-bottom: 10px;
  position: relative;
}

.my-message {
  text-align: right;
  background-color: #d1ffd1;
  padding: 15px;
  border-radius: 10px;
  display: inline-block;
  max-width: 70%;
  word-wrap: break-word;
}

.their-message {
  text-align: left;
  background-color: #f0f0f0;
  padding: 15px;
  border-radius: 10px;
  display: inline-block;
  max-width: 70%;
  word-wrap: break-word;
}

.chat-input {
  display: flex;
  gap: 10px;
  align-items: center;
}

.context-menu {
  position: absolute;
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.context-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.context-menu li {
  padding: 10px;
  cursor: pointer;
}

.context-menu li:hover {
  background: #f0f0f0;
}

.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.popup {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
}

.popup h2 {
  margin-top: 0;
}

.popup ul {
  list-style: none;
  padding: 0;
}

.popup li {
  padding: 10px;
  cursor: pointer;
}

.popup li:hover {
  background: #f0f0f0;
}

.popup button {
  background-color: #7f7f7f;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}

.popup button:hover {
  background-color: #0056b3;
}
</style>
