<template>
  <div class="flex-1 bg-white rounded-lg shadow-md p-5 flex flex-col overflow-hidden">
    <router-link
      :to="`/profile/${chat.username}`"
      class="flex items-center gap-3 border-b pb-3 border-gray-300 flex-shrink-0 hover:bg-amber-50 transition-colors"
    >
      <img
        :src="chat.profile_photo_url || 'https://placehold.co/48x48'"
        alt="Avatar"
        class="w-12 h-12 rounded-full"
      >
      <div>
        <h3 class="text-lg font-semibold text-black">{{ chat.name || 'Unknown User' }}</h3>
        <p class="text-sm text-gray-500">{{ chat.username ? '@' + chat.username : 'No username' }}</p>
      </div>
    </router-link>
    <div ref="chatMessages" class="flex-1 overflow-y-auto my-3 space-y-2 break-words" @scroll="handleScroll">
      <ChatMessage
        v-for="(message, index) in chat.messages"
        :key="index"
        :message="message"
        :user-id="userId"
        @edit="(msg) => $emit('edit-message', msg)"
        @delete="(msg) => displayDeleteModal(msg)"
        @report="(msg) => displayReportModal(msg)"
      />
    </div>
    <ChatInput @send="sendMessage" />
    <DeleteMessageModal
      v-if="showDeleteModal"
      :message="messageToDelete"
      @confirm="confirmDeleteMessage"
      @cancel="cancelDeleteMessage"
    />
    <!-- Report Popup -->
    <v-dialog v-model="reportPopup" max-width="400">
      <v-card>
        <v-card-title class="headline">Message Reported</v-card-title>
        <v-card-text>
          <p>{{ reportMessage }}</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-white" @click="closeReportPopup">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount, computed } from 'vue';
import { RouterLink } from 'vue-router';
import ChatMessage from './ChatMessage.vue';
import ChatInput from './ChatInput.vue';
import DeleteMessageModal from './DeleteMessageModal.vue';

const emit = defineEmits(['send-message', 'edit-message', 'delete-message', 'report-message']);

const props = defineProps({
  chat: Object,
  userId: Number,
});

const chatMessages = ref(null);
const isNearBottom = ref(true);
const showDeleteModal = ref(false);
const messageToDelete = ref(null);
const reportPopup = ref(false);
const reportedMessage = ref(null);

const reportMessage = computed(() => {
  if (!reportedMessage.value || !props.chat) return '';
  const username = props.chat.username || 'unknown_user';
  // Capitalize the first letter of the username
  const capitalizedUsername = username.charAt(0).toUpperCase() + username.slice(1);
  return `${capitalizedUsername}'s message has been reported to HiveMind's admins.`;
});

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

const displayReportModal = (msg) => {
  reportedMessage.value = msg;
  reportPopup.value = true;
  emit('report-message', msg);
};

const closeReportPopup = () => {
  reportPopup.value = false;
  reportedMessage.value = null;
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
    observer.observe(chatMessages.value, { childList: true, subtree: true });
  }
});

onBeforeUnmount(() => {
  observer.disconnect();
});
</script>

<style scoped>
.btn-white {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
  background: #ffffff;
  color: #000000;
  border: 1px solid #555555;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-white:hover:not(:disabled) {
  background: #f5f5f5;
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
}
</style>
