<template>
  <div class="min-h-screen bg-gray-800 p-5 flex flex-col h-screen overflow-hidden">
    <Navbar />
    <div class="flex gap-5 flex-1 mt-16 mb-16 overflow-hidden">
      <!-- Chat List -->
      <div class="bg-gray-900 rounded-lg shadow-md p-5 max-w-xs w-full overflow-y-auto max-h-[calc(100vh-150px)]">
        <div v-for="(chat, index) in ChatsList" :key="index" @click="selectChat(chat)"
             class="flex items-center gap-3 p-3 border-b border-gray-700 hover:bg-gray-700 cursor-pointer">
          <img :src="chat.profile_photo_url" alt="Avatar" class="w-12 h-12 rounded-full">
          <div>
            <h3 class="text-lg font-semibold text-white">{{ chat.name }}</h3>
            <p class="text-sm text-gray-400">{{ chat.email }}</p>
          </div>
        </div>
      </div>
      <!-- Chat Area -->
      <div v-if="selectedChat" class="flex-1 bg-gray-900 rounded-lg shadow-md p-5 flex flex-col max-h-[calc(100vh-150px)] overflow-hidden">
        <!-- Chat Header -->
        <div class="flex items-center gap-3 border-b pb-3 border-gray-700 flex-shrink-0">
          <img :src="selectedChat.avatar" alt="Avatar" class="w-12 h-12 rounded-full">
          <h3 class="text-lg font-semibold text-white">{{ selectedChat.name }}</h3>
          <button @click.stop="toggleChatMenu('header')" class="ml-auto text-gray-400">...</button>
        </div>

        <!-- Chat Messages -->
        <div ref="chatMessages" class="flex-1 overflow-y-auto my-3 space-y-2">
          <div v-for="(message, index) in selectedChat.messages" :key="index"
               @contextmenu.prevent="showMessageMenu($event, index, message.isMine)"
               class="flex items-start gap-3 p-3 rounded-lg bg-gray-800">
            <img :src="message.user.profile_photo_url" alt="Avatar" class="w-10 h-10 rounded-full">
            <div>
              <div class="flex items-center gap-2">
                <h4 class="text-sm font-semibold text-white">{{ message.user.name }}</h4>
                <span class="text-xs text-gray-400">{{ message.timestamp }}</span>
              </div>
              <p class="text-white">{{ message.text }}</p>
            </div>
          </div>
        </div>

        <!-- Chat Input -->
        <div class="flex gap-3 items-center border-t pt-3 border-gray-700 h-[60px] flex-shrink-0">
          <input v-model="newMessage" type="text" placeholder="Type a message"
                 class="flex-1 border rounded-lg p-2 bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                 @keydown.enter="sendMessage">
          <button @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Send</button>
        </div>
      </div>
    </div>
    <Footer class="absolute bottom-0 w-full" />
    <!-- Context Menu -->
    <div v-if="messageMenuVisible" :style="{ top: `${menuPosition.y}px`, left: `${menuPosition.x}px` }" class="context-menu fixed bg-gray-900 shadow-lg rounded-lg p-2">
      <button v-if="isMine" @click="editMessage(selectedMessageIndex)" class="block w-full text-left px-4 py-2 hover:bg-gray-700 text-white">Edit</button>
      <button v-if="isMine" @click="deleteMessage(selectedMessageIndex)" class="block w-full text-left px-4 py-2 hover:bg-gray-700 text-white">Delete</button>
      <button v-else @click="reportMessage(selectedMessageIndex)" class="block w-full text-left px-4 py-2 hover:bg-gray-700 text-white">Report</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import apiClient from "@/axios.js";
import "@/echo.js";

const ChatsList = ref([]);
const chatMessages = ref(null);
const selectedChat = ref(null);
const newMessage = ref('');
const chatMenuVisible = ref(null);
const messageMenuVisible = ref(false);
const menuPosition = ref({ x: 0, y: 0 });
const selectedMessageIndex = ref(null);
const userId = ref(null);
const isMine = ref(false);

const fetchUserId = async () => {
  try {
    const response = await apiClient.get("/api/user", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    userId.value = response.data.id;
  } catch (error) {
    console.error("Error al obtener el ID del usuario:", error.response?.data || error.message);
  }
};

const fetchUsers = async () => {
  try {
    const response = await apiClient.get('/api/users', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    ChatsList.value = response.data.data.map(user => ({
      id: user.id,
      name: user.name,
      email: user.email,
      profile_photo_url: user.profile_photo_url,
    }));
  } catch (error) {
    console.error("Error al obtener la lista de usuarios:", error.response?.data || error.message);
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    if (chatMessages.value) {
      chatMessages.value.scrollTop = chatMessages.value.scrollHeight;
    }
  });
};

const selectChat = async (chat) => {
  try {
    const findChat = await apiClient.get("/api/chats/private", {
      params: { recipient_id: chat.id },
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });

    selectedChat.value = findChat.data;

    const chatName = selectedChat.value.chat?.name;
    if (!chatName) {
      console.error("El nombre del chat es undefined");
      return;
    }

    const messagesResponse = await apiClient.get(`/api/chats/${chatName}/messages`);
    selectedChat.value.messages = messagesResponse.data.messages.map(msg => ({
      id: msg.id,
      text: msg.content,
      isMine: msg.user_id === userId.value,
      user: {
        id: msg.user.id,
        name: msg.user.name,
        profile_photo_url: msg.user.profile_photo_url,
      },
      timestamp: new Date(msg.created_at).toLocaleString(),
    }));

    window.Echo.channel(`${chatName}`)
      .listen('MessageSentEvent', (message) => {
        if (message.user_id !== userId.value) {
          selectedChat.value.messages.push({
            id: message.id,
            text: message.content,
            isMine: message.user_id === userId.value,
            user: {
              id: message.user_id,
              name: message.user?.name || 'Unknown',
              profile_photo_url: message.user?.profile_photo_url || '',
            },
            timestamp: new Date(message.created_at).toLocaleString(),
          });
        }
      })
      .listen('MessageEditedEvent', (event) => {
        const message = selectedChat.value.messages.find(msg => msg.id === event.id);
        if (message) {
          message.text = event.content;
          message.is_edited = event.is_edited;
        }
      })
      .listen('MessageDeletedEvent', (event) => {
        const message = selectedChat.value.messages.find(msg => msg.id === event.message_id);
        if (message) {
          message.text = event.content;
        }
      });

    scrollToBottom();
  } catch (error) {
    console.error("Error al obtener o crear el chat:", error.response?.data || error.message);
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;
  try {
    const chatName = selectedChat.value.chat?.name;
    if (!chatName) {
      console.error("El nombre del chat es undefined");
      return;
    }

    const response = await apiClient.post(`/api/chats/${chatName}/messages`, {
      content: newMessage.value.trim(),
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });

    if (!selectedChat.value.messages) {
      selectedChat.value.messages = [];
    }

    const user = response.data.message.user || {};
    selectedChat.value.messages.push({
      id: response.data.message.id,
      text: response.data.message.content,
      isMine: true,
      user: {
        id: user.id || userId.value,
        name: user.name || 'Unknown',
        profile_photo_url: user.profile_photo_url || '',
      },
      timestamp: new Date(response.data.message.created_at).toLocaleString(),
    });

    newMessage.value = '';
    await nextTick();
    scrollToBottom();
  } catch (error) {
    console.error("Error al enviar mensaje:", error.response?.data || error.message);
  }
};

const toggleChatMenu = (index) => {
  chatMenuVisible.value = chatMenuVisible.value === index ? null : index;
};

const showMessageMenu = (event, index, mine) => {
  selectedMessageIndex.value = index;
  isMine.value = mine;
  menuPosition.value = { x: event.clientX, y: event.clientY };
  messageMenuVisible.value = true;
};

const editMessage = async (index) => {
  const message = selectedChat.value.messages[index];
  const newContent = prompt("Edit your message:", message.text);
  if (newContent !== null) {
    try {
      const response = await apiClient.patch(`/api/messages/${message.id}`, {
        content: newContent,
      }, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      });
      selectedChat.value.messages[index].text = response.data.message.content;
      selectedChat.value.messages[index].is_edited = true;
      alert("Message edited successfully");
    } catch (error) {
      console.error("Error al editar el mensaje:", error.response?.data || error.message);
    }
  }
  messageMenuVisible.value = false;
};

const deleteMessage = async (index) => {
  const message = selectedChat.value.messages[index];
  if (confirm("Are you sure you want to delete this message?")) {
    try {
      await apiClient.delete(`/api/messages/${message.id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      });
      selectedChat.value.messages.splice(index, 1);
      alert("Message deleted successfully");
    } catch (error) {
      console.error("Error al eliminar el mensaje:", error.response?.data || error.message);
    }
  }
  messageMenuVisible.value = false;
};

const reportMessage = (index) => {
  alert(`Reporting message at index ${index}`);
  messageMenuVisible.value = false;
};

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu') && !event.target.closest('.chat-options button')) {
    messageMenuVisible.value = false;
    chatMenuVisible.value = null;
  }
};

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
  fetchUserId();
  fetchUsers();
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});
</script>
