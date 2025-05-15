<template>
  <div class="chat-area">
    <div class="chat-header">
      <ServerHeader
        :selected-server="selectedServer"
        :show-options-menu="showOptionsMenu"
        @toggle-options-menu="$emit('toggle-options-menu')"
        @show-server-details="$emit('show-server-details')"
        @edit-server="$emit('edit-server')"
        @leave-server="$emit('leave-server')"
        @report-server="$emit('report-server')"
      />
      <div class="channel-info">
        <i class="fas fa-hashtag"></i>
        <span>{{ selectedChannel?.name || 'Select a channel' }}</span>
      </div>
    </div>

    <div ref="chatMessages" class="messages-container" @scroll="handleScroll">
      <ChatMessage
        v-for="(message, index) in selectedChannel?.messages"
        :key="index"
        :message="message"
        :user-id="currentUser?.id"
        @edit="editMessage"
        @delete="displayDeleteModal"
        @report="reportMessage"
      />
    </div>

    <ChatInput :channel-name="selectedChannel?.name" @send="sendMessage" />

    <DeleteMessageModal
      v-if="showDeleteModal"
      :message="messageToDelete"
      @confirm="confirmDeleteMessage"
      @cancel="cancelDeleteMessage"
    />
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount } from 'vue';
import ChatMessage from './ChatMessage.vue';
import ChatInput from './ChatInput.vue';
import DeleteMessageModal from './DeleteMessageModal.vue';
import ServerHeader from './ServerHeader.vue';

const props = defineProps({
  selectedChannel: Object,
  selectedServer: Object,
  currentUser: Object,
  showOptionsMenu: Boolean,
});

const emit = defineEmits([
  'send-message',
  'edit-message',
  'delete-message',
  'report-message',
  'toggle-options-menu',
  'show-server-details',
  'edit-server',
  'leave-server',
  'report-server',
]);

const chatMessages = ref(null);
const isNearBottom = ref(true);
const showDeleteModal = ref(false);
const messageToDelete = ref(null);

const sendMessage = (messageContent) => {
  if (messageContent.trim()) {
    emit('send-message', messageContent);
  }
};

const editMessage = (message) => {
  emit('edit-message', message);
};

const reportMessage = (message) => {
  emit('report-message', message);
};

const displayDeleteModal = (message) => {
  messageToDelete.value = message;
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
    isNearBottom.value =
      chatMessages.value.scrollHeight - chatMessages.value.scrollTop <=
      chatMessages.value.clientHeight + threshold;
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
.chat-area {
  flex: 1;
  background: #f3e8d3;
  display: flex;
  flex-direction: column;
  height: calc(100vh - 128px);
}

.chat-header {
  padding: 16px;
  border-bottom: 1px solid #e5d5b3;
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
  color: #78350f;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.channel-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.channel-info i {
  color: #78350f;
}

.messages-container {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
</style>
