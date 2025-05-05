<template>
  <div class="h-screen bg-gradient-to-br from-amber-50 to-amber-100 flex flex-col overflow-hidden py-4">
    <Navbar />
    <div class="flex gap-5 flex-1 mt-16 mb-16 overflow-hidden px-4">
      <ChatList :chats="chats" @select-chat="handleSelectChat" />
      <ChatArea v-if="selectedChat"
                :chat="getOtherUser(selectedChat)"
                :user-id="userId"
                @send-message="sendMessage"
                @edit-message="confirmEditMessage"
                @delete-message="confirmDeleteMessage"
                @report-message="reportMessage"/>
      <div v-else class="flex-1 flex items-center justify-center">
        <div class="text-center p-8 bg-white rounded-xl shadow-lg max-w-md border border-amber-200">
          <div class="h-16 w-16 mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full text-amber-500 animate-chat-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-amber-800 mt-4">Buzz into a chat</h3>
          <p class="text-amber-600 mt-2">Select a hive conversation to start messaging with your swarm</p>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import ChatList from '@/components/Chat/ChatList.vue';
import ChatArea from '@/components/Chat/ChatArea.vue';
import { useChat } from '@/composables/Chat/useChat';
import { useUser } from '@/composables/Chat/useUser';

const { chats, selectedChat, selectChat, sendMessage, confirmEditMessage, confirmDeleteMessage, reportMessage } = useChat();
const { userId, fetchUserId } = useUser();

// Función para obtener el usuario no logueado
const getOtherUser = (chat) => {

  if (!chat || !chat.chat || !chat.chat.users) {
    return { name: 'Unknown User', username: null, profile_photo_url: 'https://placehold.co/48x48' };
  }

  const otherUser = chat.chat.users.find(user => user.id !== userId.value);

  return {
    name: otherUser ? otherUser.name : 'Unknown User',
    username: otherUser ? otherUser.username : null,
    profile_photo_url: otherUser ? otherUser.profile_photo_url : 'https://placehold.co/48x48',
    messages: chat.messages || []
  };
};

// Manejar la selección de chat asegurando que userId esté definido
const handleSelectChat = async (chat) => {
  if (!userId.value) {
    console.log('handleSelectChat - userId no definido, llamando a fetchUserId');
    await fetchUserId();
  }
  console.log('handleSelectChat - userId:', userId.value);
  await selectChat(chat);
};

onMounted(() => {
  fetchUserId();
});
</script>

<style scoped>
@keyframes chat-pulse {
  0%, 100% {
    transform: translateX(0) rotate(0deg);
  }
  15% {
    transform: translateX(-1px) rotate(-6deg);
  }
  30% {
    transform: translateX(1px) rotate(6deg);
  }
  45% {
    transform: translateX(-0.5px) rotate(-5deg);
  }
  60% {
    transform: translateX(0.5px) rotate(5deg);
  }
  75% {
    transform: translateX(-0.3px) rotate(-4deg);
  }
  90% {
    transform: translateX(0.3px) rotate(4deg);
  }
}

.animate-chat-pulse {
  animation: chat-pulse 4s ease-in-out infinite;
  transform-origin: center bottom;
}

.text-gray-800 {
  color: #2d3748;
}

.text-gray-600 {
  color: #4a5568;
}
</style>
