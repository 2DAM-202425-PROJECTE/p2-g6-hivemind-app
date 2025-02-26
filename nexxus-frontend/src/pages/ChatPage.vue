<template>
  <div class="min-h-screen bg-gray-800 p-5 flex flex-col h-screen overflow-hidden">
    <Navbar />
    <div class="flex gap-5 flex-1 mt-16 mb-16 overflow-hidden">
      <ChatList :chats="chats" @select-chat="selectChat" />
      <ChatArea v-if="selectedChat"
                :chat="selectedChat"
                :user-id="userId"
                @send-message="sendMessage"
                @edit-message="confirmEditMessage"
                @delete-message="confirmDeleteMessage"
                @report-message="reportMessage"/>
    </div>
    <Footer class="absolute bottom-0 w-full" />
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import apiClient from "@/axios.js";
import "@/echo.js";

const ChatsList = ref([])

const selectedChat = ref(null)
const newMessage = ref('')
const chatMenuVisible = ref(null)
const messageMenuVisible = ref(false)
const menuPosition = ref({ x: 0, y: 0 })
const selectedMessageIndex = ref(null)
const userId = ref(null);
const isMine = ref(false)

const fetchUserId = async () => {
  try {
    const response = await apiClient.get("/api/user", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    userId.value = response.data.id;
  } catch (error) {
    console.error("Error al obtenir l'ID de l'usuari:", error.response?.data || error.message);
  }
};

const fetchUsers = async () => {
  try {
    const response = await apiClient.get('/api/users', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    ChatsList.value = response.data.data.map(user => ({
      id: user.id,
      name: user.name,
      email: user.email,
      profile_photo_url: user.profile_photo_url,
    }))
  } catch (error) {
    console.error("Error al obtener la lista de usuarios:", error.response?.data || error.message)
  }
}

const selectChat = async (chat) => {
  try {

    const findChat = await apiClient.get("/api/chats/private",
      { params: { recipient_id: chat.id },
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    )
    selectedChat.value = findChat.data;

    // Obtenim els missatges del xat
    const messagesResponse = await apiClient.get(`/api/chats/${selectedChat.value.chat.name}/messages`);
    selectedChat.value.messages = messagesResponse.data.messages.map(msg => ({
      id: msg.id,
      text: msg.content,
      isMine: msg.user_id === userId.value,
    }));

    window.Echo.channel(`${selectedChat.value.chat.name}`)
      .listen('MessageSentEvent', (message) => {
        if (message.user_id !== userId.value) { // Evitar mensajes duplicados
          selectedChat.value.messages.push({
            id: message.id,
            text: message.content,
            isMine: message.user_id === userId.value,
          });
        }
      });

    console.log("Missatges carregats:", selectedChat.value.messages);
  } catch (error) {
    console.error("Error al obtener o crear el chat:", error.response?.data || error.message);
    selectedChat.value = { ...chat, messages: [] };
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;
  try {
    const response = await apiClient.post(`/api/chats/${selectedChat.value.chat.name}/messages`,
      { content: newMessage.value.trim() },
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    );

    // Assegurar que `messages` està inicialitzat com a array
    if (!selectedChat.value.messages) {
      selectedChat.value.messages = [];
    }

    selectedChat.value.messages.push({
      id: response.data.message.id,
      text: response.data.message.content,
      isMine: true,
    });

    newMessage.value = '';
  } catch (error) {
    console.error("Error al enviar mensaje:", error.response?.data || error.message);
  }
};


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
  fetchUserId();
  fetchChats();
});
</script>
