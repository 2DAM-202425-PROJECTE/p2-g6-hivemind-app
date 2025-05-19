import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/axios.js';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { generateAvatar } from '@/utils/avatar.js';

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
  const userUsername = ref(null);
  const isMine = ref(false);
  const messageToDelete = ref(null);
  const showEditModal = ref(false);
  const showDeleteModal = ref(false);
  let echo = null;

  const fetchUser = async () => {
    try {
      const response = await apiClient.get('/api/user', { withCredentials: true });
      userId.value = response.data.id;
      userUsername.value = response.data.username;
      console.log('Fetched user:', { id: userId.value, username: userUsername.value });
    } catch (error) {
      console.error('Error al obtener usuario:', error.response?.data || error.message);
      router.push('/login');
    }
  };

  const getImageUrl = (path) => {
    if (!path) return generateAvatar('User');
    if (path.startsWith('data:image/')) return path;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return `http://localhost:8000/storage/${path}`;
  };

  const fetchChats = async () => {
    if (!userId.value) {
      console.log('fetchChats: No userId, fetching user...');
      await fetchUser();
    }
    try {
      const response = await apiClient.get('/api/chats', { withCredentials: true });
      chats.value = await Promise.all(response.data.chats.map(async chat => {
        const otherUser = chat.users.find(u => u.id !== userId.value);
        const messagesResponse = await apiClient.get(`/api/chats/${chat.name}/messages`, {
          withCredentials: true,
          params: { per_page: 1 },
        });
        const lastMessage = messagesResponse.data.messages[0] || null;
        const unreadCount = lastMessage && !lastMessage.is_read && lastMessage.user_id !== userId.value ? 1 : 0;
        return {
          id: otherUser.id,
          name: otherUser.name,
          username: otherUser.username,
          profile_photo_url: getImageUrl(otherUser.profile_photo_url),
          unread_count: unreadCount,
          last_message_time: lastMessage ? lastMessage.created_at : null,
          last_message: lastMessage ? { text: lastMessage.content, is_edited: lastMessage.is_edited } : null,
          chat: chat,
        };
      }));
      console.log('Fetched chats:', JSON.stringify(chats.value, null, 2));
    } catch (error) {
      console.error('Error al obtener chats:', error.response?.data || error.message);
      chats.value = [];
    }
  };

  const initializeWebSocket = async () => {
    if (!userId.value) {
      console.log('initializeWebSocket - No userId, redirecting to login');
      router.push('/login');
      return;
    }

    window.Pusher = Pusher;
    try {
      echo = new Echo({
        broadcaster: 'reverb',
        key: 'rhymuvs4zyt8qnajgilh',
        wsHost: 'localhost',
        wsPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws'],
        withCredentials: true,
      });
      console.log('WebSocket initialized');
    } catch (error) {
      console.error('Error initializing WebSocket:', error);
      return;
    }

    // Subscribe to all chats
    chats.value.forEach(chat => {
      if (chat.chat?.name) {
        subscribeToChatChannel(chat.chat.name);
      } else {
        console.warn('Chat name missing for chat:', chat);
      }
    });

    if (selectedChat.value?.chat?.name) {
      subscribeToChatChannel(selectedChat.value.chat.name);
    }
  };

  const subscribeToChatChannel = (channelName) => {
    if (!channelName || channelName === 'undefined' || channelName === 'null') {
      console.error('Invalid channel name:', channelName);
      return;
    }
    console.log('Subscribing to channel:', channelName);
    try {
      echo.channel(channelName)
        .subscribed(() => {
          console.log('Subscribed to channel:', channelName);
        })
        .listen('MessageSentEvent', (event) => {
          console.log('Received MessageSentEvent:', JSON.stringify(event, null, 2));
          if (!event.message?.chat?.name || !event.message?.user) {
            console.error('Invalid MessageSentEvent structure:', event);
            return;
          }
          const chatName = event.message.chat.name;
          const sender = event.message.user;

          let chat = chats.value.find(c => c.chat.name === chatName);
          if (!chat) {
            if (sender.id !== userId.value) {
              apiClient.get('/api/chats/private', {
                params: { recipient_id: sender.id },
                withCredentials: true,
              }).then(response => {
                const newChat = response.data.chat;
                const otherUser = newChat.users.find(u => u.id !== userId.value);
                chats.value.push({
                  id: otherUser.id,
                  name: otherUser.name,
                  username: otherUser.username,
                  profile_photo_url: getImageUrl(otherUser.profile_photo_url),
                  unread_count: 1,
                  last_message_time: event.message.created_at,
                  last_message: { text: event.message.content, is_edited: event.message.is_edited },
                  chat: newChat,
                });
                subscribeToChatChannel(newChat.name);
              }).catch(error => {
                console.error('Error fetching new chat:', error);
              });
            }
            return;
          }

          const isChatSelected = selectedChat.value?.chat?.name === chatName;
          if (sender.id !== userId.value && !isChatSelected) {
            chat.unread_count = (chat.unread_count || 0) + 1;
          } else if (isChatSelected) {
            chat.unread_count = 0;
          }
          chat.last_message = { text: event.message.content, is_edited: event.message.is_edited };
          chat.last_message_time = event.message.created_at;

          if (isChatSelected) {
            if (!selectedChat.value.messages) selectedChat.value.messages = [];
            if (!selectedChat.value.messages.some(msg => msg.id === event.message.id)) {
              selectedChat.value.messages.push({
                id: event.message.id,
                text: event.message.content,
                isMine: event.message.user_id === userId.value,
                user: {
                  id: sender.id,
                  name: sender.name,
                  profile_photo_url: getImageUrl(sender.profile_photo_url),
                },
                timestamp: new Date(event.message.created_at).toLocaleString(),
                is_edited: event.message.is_edited,
              });

              selectedChat.value.messages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
              const latestMessage = selectedChat.value.messages[selectedChat.value.messages.length - 1];
              chat.last_message = {
                text: latestMessage.text,
                is_edited: latestMessage.is_edited,
              };
              chat.last_message_time = latestMessage.timestamp;
            }

            if (sender.id !== userId.value) {
              apiClient.post(`/api/chats/${chatName}/messages/${event.message.id}/read`, {}, { withCredentials: true })
                .then(() => {
                  console.log(`Marked message ${event.message.id} as read`);
                })
                .catch(error => {
                  console.error('Error marking message as read:', error);
                });
            }
          }
        })
        .listen('MessageEditedEvent', (event) => {
          console.log('Received MessageEditedEvent:', JSON.stringify(event, null, 2));
          if (selectedChat.value?.chat?.name && event.message) {
            const messageIndex = selectedChat.value.messages.findIndex(msg => msg.id === event.message.id);
            if (messageIndex !== -1) {
              selectedChat.value.messages[messageIndex].text = event.message.content;
              selectedChat.value.messages[messageIndex].is_edited = event.message.is_edited || true; // Asegurar que is_edited se actualice
            }
            // Actualizar last_message si este mensaje es el más reciente
            const chat = chats.value.find(c => c.chat.name === selectedChat.value.chat.name);
            if (chat && selectedChat.value.messages.length > 0) {
              const latestMessage = selectedChat.value.messages[selectedChat.value.messages.length - 1];
              chat.last_message = {
                text: latestMessage.text,
                is_edited: latestMessage.is_edited,
              };
              chat.last_message_time = latestMessage.timestamp;
            }
          }
        })
        .listen('MessageDeletedEvent', (event) => {
          console.log('Received MessageDeletedEvent:', JSON.stringify(event, null, 2));
          if (selectedChat.value?.chat?.name && event.message_id) {
            const messageIndex = selectedChat.value.messages.findIndex(msg => msg.id === event.message_id);
            if (messageIndex !== -1) {
              selectedChat.value.messages.splice(messageIndex, 1);
            }
            // Actualizar last_message si se eliminó el último mensaje
            const chat = chats.value.find(c => c.chat.name === selectedChat.value.chat.name);
            if (chat && selectedChat.value.messages.length > 0) {
              const latestMessage = selectedChat.value.messages[selectedChat.value.messages.length - 1];
              chat.last_message = {
                text: latestMessage.text,
                is_edited: latestMessage.is_edited,
              };
              chat.last_message_time = latestMessage.timestamp;
            } else if (chat) {
              chat.last_message = null;
              chat.last_message_time = null;
            }
          }
        });
    } catch (error) {
      console.error('Error subscribing to channel:', channelName, error);
    }
  };

  const sendMessage = async (messageContent) => {
    if (!messageContent.trim()) return;
    try {
      const chatName = selectedChat.value.chat?.name;
      if (!chatName) {
        console.error('El nombre del chat es undefined');
        return;
      }

      const response = await apiClient.post(`/api/chats/${chatName}/messages`, {
        content: messageContent.trim(),
      }, { withCredentials: true });
      console.log('Message sent:', response.data);

      if (!selectedChat.value.messages) selectedChat.value.messages = [];
      const message = response.data.message;
      if (!selectedChat.value.messages.some(msg => msg.id === message.id)) {
        selectedChat.value.messages.push({
          id: message.id,
          text: message.content,
          isMine: message.user_id === userId.value,
          user: {
            id: message.user.id,
            name: message.user.name,
            profile_photo_url: getImageUrl(message.user.profile_photo_url),
          },
          timestamp: new Date(message.created_at).toLocaleString(),
          is_edited: message.is_edited,
        });

        // Actualizar el chat en chats.value
        const chat = chats.value.find(c => c.chat.name === chatName);
        if (chat) {
          chat.last_message = { text: message.content, is_edited: false };
          chat.last_message_time = message.created_at;
          chat.unread_count = 0; // Resetear el contador de no leídos porque el usuario está enviando el mensaje
        }
      }

      newMessage.value = '';
    } catch (error) {
      console.error('Error al enviar mensaje:', error.response?.data || error.message);
    }
  };

  const selectChat = async (chat) => {
    try {
      if (!userId.value) {
        console.log('selectChat - userId no definido, llamando a fetchUser');
        await fetchUser();
      }

      console.log('selectChat - userId:', userId.value);

      const findChat = await apiClient.get('/api/chats/private', {
        params: { recipient_id: chat.id },
        withCredentials: true,
      });

      console.log('Selected chat data:', JSON.stringify(findChat.data, null, 2));

      selectedChat.value = findChat.data;

      const chatName = selectedChat.value.chat?.name;
      if (!chatName) {
        console.error('El nombre del chat es undefined');
        return;
      }

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
          profile_photo_url: getImageUrl(msg.user.profile_photo_url),
        },
        timestamp: new Date(msg.created_at).toLocaleString(),
        is_edited: msg.is_edited,
      }));

      // Actualizar last_message en chats.value con el mensaje más reciente
      const chatInList = chats.value.find(c => c.id === chat.id);
      if (chatInList && selectedChat.value.messages.length > 0) {
        const latestMessage = selectedChat.value.messages[selectedChat.value.messages.length - 1];
        chatInList.last_message = {
          text: latestMessage.text,
          is_edited: latestMessage.is_edited,
        };
        chatInList.last_message_time = latestMessage.timestamp;
        chatInList.unread_count = 0; // Resetear el contador de no leídos
      }

      subscribeToChatChannel(chatName);
    } catch (error) {
      console.error('Error al obtener o crear el chat:', error.response?.data || error.message);
    }
  };

  const confirmEditMessage = async (message) => {
    try {
      const response = await apiClient.patch(`/api/messages/${message.id}`, {
        content: message.text,
      }, { withCredentials: true });
      console.log('Message edited:', response.data);
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
      await apiClient.delete(`/api/messages/${message.id}`, { withCredentials: true });
      console.log('Message deleted:', message.id);
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
    fetchUser();
    fetchChats().then(() => {
      initializeWebSocket();
    });
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
    if (echo) {
      chats.value.forEach(chat => {
        if (chat.chat?.name) {
          echo.leaveChannel(chat.chat.name);
        }
      });
      if (selectedChat.value?.chat?.name) {
        echo.leaveChannel(selectedChat.value.chat.name);
      }
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
    fetchUser,
    fetchChats,
    selectChat,
    sendMessage,
    confirmEditMessage,
    confirmDeleteMessage,
    reportMessage,
  };
}
