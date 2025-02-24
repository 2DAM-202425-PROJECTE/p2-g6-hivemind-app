<template>
  <div class="flex-1 bg-gray-900 rounded-lg shadow-md p-5 flex flex-col max-h-[calc(100vh-150px)] overflow-hidden">
    <div class="flex items-center gap-3 border-b pb-3 border-gray-700 flex-shrink-0">
      <img :src="chat.avatar" alt="Avatar" class="w-12 h-12 rounded-full">
      <h3 class="text-lg font-semibold text-white">{{ chat.name }}</h3>
    </div>

    <div ref="chatMessages" class="flex-1 overflow-y-auto my-3 space-y-2 break-words" @scroll="handleScroll">
      <ChatMessage v-for="(message, index) in chat.messages"
                   :key="index"
                   :message="message"
                   :user-id="userId"
                   @edit="(msg) => $emit('edit-message', msg)"
                   @delete="(msg) => displayDeleteModal(msg)"
                   @report="(msg) => $emit('report-message', msg)" />
    </div>

    <ChatInput @send="sendMessage" />

    <DeleteMessageModal v-if="showDeleteModal" :message="messageToDelete" @confirm="confirmDeleteMessage" @cancel="cancelDeleteMessage" />
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount } from 'vue';
import ChatMessage from './ChatMessage.vue';
import ChatInput from './ChatInput.vue';
import DeleteMessageModal from './DeleteMessageModal.vue';

const emit = defineEmits(['send-message', 'edit-message', 'delete-message', 'report-message']);

const props = defineProps({
  chat: Object,
  userId: Number
});

const chatMessages = ref(null);
const isNearBottom = ref(true);
const showDeleteModal = ref(false);
const messageToDelete = ref(null);

const sendMessage = async (messageContent) => {
  emit('send-message', messageContent);
};

const displayDeleteModal = (msg) => {
  messageToDelete.value = msg;
  showDeleteModal.value = true;
};

const confirmDeleteMessage = () => {
  emit('delete-message', messageToDelete.value);
  showDeleteModal.value = false;
};

const cancelDeleteMessage = () => {
  showDeleteModal.value = false;
};

const scrollToBottom = () => {
  nextTick(() => {
    if (chatMessages.value && isNearBottom.value) {
      chatMessages.value.scrollTop = chatMessages.value.scrollHeight;
    }
  });
};

const handleScroll = () => {
  if (chatMessages.value) {
    const threshold = 75;
    isNearBottom.value = chatMessages.value.scrollHeight - chatMessages.value.scrollTop <= chatMessages.value.clientHeight + threshold;
  }
};

const observer = new MutationObserver(() => {
  scrollToBottom();
});

onMounted(() => {
  if (chatMessages.value) {
    observer.observe(chatMessages.value, {childList: true, subtree: true});
  }
});

onBeforeUnmount(() => {
  observer.disconnect();
});
</script>
