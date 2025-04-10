<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 flex flex-col h-screen overflow-hidden">
    <Navbar />
    <div class="flex gap-5 flex-1 mt-16 mb-16 overflow-hidden px-4">
      <ChatList :chats="chats" @select-chat="selectChat" />
      <ChatArea v-if="selectedChat"
                :chat="selectedChat"
                :user-id="userId"
                @send-message="sendMessage"
                @edit-message="confirmEditMessage"
                @delete-message="confirmDeleteMessage"
                @report-message="reportMessage"/>
      <div v-else class="flex-1 flex items-center justify-center">
        <div class="text-center p-8 bg-white rounded-xl shadow-lg max-w-md">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
          <h3 class="text-xl font-bold text-amber-800 mt-4">Select a chat</h3>
          <p class="text-amber-600 mt-2">Choose a conversation from the list to start messaging</p>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import {onMounted} from 'vue';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import ChatList from '@/components/Chat/ChatList.vue';
import ChatArea from '@/components/Chat/ChatArea.vue';
import {useChat} from '@/composables/Chat/useChat';
import {useUser} from '@/composables/Chat/useUser';

const {
  chats,
  selectedChat,
  selectChat,
  sendMessage,
  confirmEditMessage,
  confirmDeleteMessage,
  reportMessage,
} = useChat();
const {userId, fetchUserId} = useUser();

onMounted(() => {
  fetchUserId();
});
</script>
