import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/axios.js';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

export function useChat() {
  const router = useRouter();
  const chats = ref([]);
  const chatMessages = ref(null);
  const selectedChat = ref(null);
  const newMessage = ref('');
  const chatMenuVisible = ref(null);
  const messageMenuVisible = ref(false);
  const menuPosition = ref({ x: 0, y: 0 });
  const selectedMessageIndex = ref(null);
  const userId = ref(null);
  const isMine = ref(false);
  const messageToDelete = ref(null);
  const showEditModal = ref(false);
  const showDeleteModal = ref(false);
  let echo = null;

  const fetchCsrfToken = async () => {
    try {
      await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
        withCredentials: true,
      });
    } catch (error) {
      console.error('Error fetching CSRF token:', error);
    }
  };

  const fetchUserId = async () => {
    try {
      await fetchCsrfToken();
      const response = await apiClient.get('/api/user', {
        withCredentials: true,
      });
      userId.value = response.data.id;
    } catch (error) {
      console.error('Error al obtener el ID del usuario:', error.response?.data || error.message);
      router.push('/login');
    }
  };

  const fetchChats = async () => {
    try {
      await fetchCsrfToken();
      const response = await apiClient.get('/api/users', {
        withCredentials: true,
      });
      chats.value = response.data.data.map(user => ({
        id: user.id,
        name: user.name,
        username: user.username,
        profile_photo_url: user.profile_photo_url,
      }));
    } catch (error) {
      console.error('Error al obtener la lista de usuarios:', error.response?.data || error.message);
    }
  };

  const initializeWebSocket = async (chatName) => {
    if (!userId.value) {
      router.push('/login');
      return;
    }

    await fetchCsrfToken();
    console.log('Subscribing to channel:', `private-${chatName}`);

    window.Pusher = Pusher;
    echo = new Echo({
      authEndpoint: 'http://localhost:8000/broadcasting/auth',
      broadcaster: 'reverb',
      key: 'rhymuvs4zyt8qnajgilh',
      wsHost: 'localhost',
      wsPort: 8080,
      forceTLS: false,
      enabledTransports: ['ws'],
      withCredentials: true,
      auth: {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || localStorage.getItem('XSRF-TOKEN') || '',
        },
      },
    });

    echo.channel(`${chatName}`)
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
            is_edited: false,
          });
        }
      })
      .listen('MessageEditedEvent', (event) => {
        const message = selectedChat.value.messages.find(msg => msg.id === event.id);
        if (message) {
          message.text = event.content;
          message.is_edited = true;
        }
      })
      .listen('MessageDeletedEvent', (event) => {
        const index = selectedChat.value.messages.findIndex(msg => msg.id === event.message_id);
        if (index !== -1) {
          selectedChat.value.messages.splice(index, 1);
        }
      });
  };

  const sendMessage = async (messageContent) => {
    if (!messageContent.trim()) return;
    try {
      const chatName = selectedChat.value.chat?.name;
      if (!chatName) {
        console.error('El nombre del chat es undefined');
        return;
      }

      await fetchCsrfToken();
      const response = await apiClient.post(`/api/chats/${chatName}/messages`, {
        content: messageContent.trim(),
      }, {
        withCredentials: true,
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
    } catch (error) {
      console.error('Error al enviar mensaje:', error.response?.data || error.message);
    }
  };

  const selectChat = async (chat) => {
    try {
      await fetchCsrfToken();
      const findChat = await apiClient.get('/api/chats/private', {
        params: { recipient_id: chat.id },
        withCredentials: true,
      });

      selectedChat.value = findChat.data;

      const chatName = selectedChat.value.chat?.name;
      if (!chatName) {
        console.error('El nombre del chat es undefined');
        return;
      }

      initializeWebSocket(chatName);

      const messagesResponse = await apiClient.get(`/api/chats/${chatName}/messages`, {
        withCredentials: true,
      });
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
        is_edited: msg.is_edited,
      }));
    } catch (error) {
      console.error('Error al obtener o crear el chat:', error.response?.data || error.message);
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

  const confirmEditMessage = async (message) => {
    try {
      await fetchCsrfToken();
      const response = await apiClient.patch(`/api/messages/${message.id}`, {
        content: message.text,
      }, {
        withCredentials: true,
      });
      const index = selectedChat.value.messages.findIndex(m => m.id === message.id);
      if (index !== -1) {
        selectedChat.value.messages[index].text = response.data.message.content;
        selectedChat.value.messages[index].is_edited = true;
      }
    } catch (error) {
      console.error('Error editing message:', error.response?.data || error.message);
    }
    showEditModal.value = false;
  };

  const confirmDeleteMessage = async (message) => {
    try {
      await fetchCsrfToken();
      await apiClient.delete(`/api/messages/${message.id}`, {
        withCredentials: true,
      });
      const index = selectedChat.value.messages.findIndex(m => m.id === message.id);
      if (index !== -1) {
        selectedChat.value.messages.splice(index, 1);
      }
    } catch (error) {
      console.error('Error al eliminar el mensaje:', error.response?.data || error.message);
    }
    showDeleteModal.value = false;
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
    fetchChats();
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
    if (echo && selectedChat.value?.chat?.name) {
      echo.leaveChannel(`private-${selectedChat.value.chat.name}`);
    }
  });

  return {
    chats,
    chatMessages,
    selectedChat,
    newMessage,
    chatMenuVisible,
    messageMenuVisible,
    menuPosition,
    selectedMessageIndex,
    userId,
    isMine,
    messageToDelete,
    fetchUserId,
    fetchChats,
    selectChat,
    sendMessage,
    toggleChatMenu,
    showMessageMenu,
    confirmEditMessage,
    confirmDeleteMessage,
    reportMessage,
  };
}
