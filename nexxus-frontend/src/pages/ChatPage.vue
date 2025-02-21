<template>
  <div class="min-h-screen bg-gray-800 p-5 flex flex-col h-screen overflow-hidden">
    <Navbar />
    <div class="flex gap-5 flex-1 mt-16 mb-16 overflow-hidden">
      <ChatList :chats="chats" @select-chat="selectChat" />
      <ChatArea v-if="selectedChat"
                :chat="selectedChat"
                :user-id="userId"
                @send-message="sendMessage"
                @edit-message="editMessage"
                @delete-message="deleteMessage"
                @report-message="reportMessage" />
    </div>
    <Footer class="absolute bottom-0 w-full" />
    <DeleteMessageModal v-if="showModal" :message="messageToDelete" @confirm="confirmDeleteMessage" @cancel="showModal = false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import ChatList from '@/components/Chat/ChatList.vue';
import ChatArea from '@/components/Chat/ChatArea.vue';
import DeleteMessageModal from '@/components/Chat/DeleteMessageModal.vue';
import {useChat} from '@/composables/Chat/useChat';
import {useUser} from '@/composables/useUser';

const {
  chats,
  selectedChat,
  selectChat,
  sendMessage,
  editMessage,
  deleteMessage,
  reportMessage,
  fetchChats,
  showModal,
  messageToDelete,
  confirmDeleteMessage
} = useChat();
const {userId, fetchUserId} = useUser();

onMounted(() => {
  fetchUserId();
  fetchChats();
});
</script>
