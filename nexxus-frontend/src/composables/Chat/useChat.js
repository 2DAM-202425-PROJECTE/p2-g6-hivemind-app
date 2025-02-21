import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import apiClient from '@/axios.js';
import '@/echo.js';

export function useChat() {
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
  const showModal = ref(false);
  const messageToDelete = ref(null);
  const isShiftPressed = ref(false);

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

  const fetchChats = async () => {
    try {
      const response = await apiClient.get('/api/users', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      chats.value = response.data.data.map(user => ({
        id: user.id,
        name: user.name,
        email: user.email,
        profile_photo_url: user.profile_photo_url,
      }));
    } catch (error) {
      console.error("Error al obtener la lista de usuarios:", error.response?.data || error.message);
    }
  };

  const sendMessage = async (messageContent) => {
    if (!messageContent.trim()) return;
    try {
      const chatName = selectedChat.value.chat?.name;
      if (!chatName) {
        console.error("El nombre del chat es undefined");
        return;
      }

      const response = await apiClient.post(`/api/chats/${chatName}/messages`, {
        content: messageContent.trim(),
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
    } catch (error) {
      console.error("Error al enviar mensaje:", error.response?.data || error.message);
    }
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
        .listen('MessageSentEvent', async (message) => {
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
    } catch (error) {
      console.error("Error al obtener o crear el chat:", error.response?.data || error.message);
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

  const deleteMessage = async (message) => {
    if (!message) {
      console.error("Mensaje no proporcionado");
      return;
    }

    // Buscar el índice del mensaje en la lista
    const index = selectedChat.value.messages.findIndex(m => m.id === message.id);
    if (index === -1) {
      console.error("Mensaje no encontrado en la lista");
      return;
    }

    if (isShiftPressed.value) {
      await confirmDeleteMessage();
    } else {
      messageToDelete.value = message;
      showModal.value = true;
    }
  };

  const confirmDeleteMessage = async () => {
    const message = messageToDelete.value;
    if (!message) return;

    try {
      await apiClient.delete(`/api/messages/${message.id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      });
      const index = selectedChat.value.messages.findIndex(m => m.id === message.id);
      if (index !== -1) {
        selectedChat.value.messages.splice(index, 1);
      }
    } catch (error) {
      console.error("Error al eliminar el mensaje:", error.response?.data || error.message);
    }
    showModal.value = false;
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
    showModal,
    messageToDelete,
    fetchUserId,
    fetchChats,
    selectChat,
    sendMessage,
    toggleChatMenu,
    showMessageMenu,
    editMessage,
    deleteMessage,
    confirmDeleteMessage,
    reportMessage,
  };
}
