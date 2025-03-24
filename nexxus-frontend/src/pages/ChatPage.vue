<template>
  <div class="min-h-screen bg-gray-100 p-5 flex flex-col h-screen overflow-hidden">
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
import {ref, onMounted} from 'vue';
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
  fetchChats,
} = useChat();
const {userId, fetchUserId} = useUser();

onMounted(() => {
  fetchUserId();
});
</script>
