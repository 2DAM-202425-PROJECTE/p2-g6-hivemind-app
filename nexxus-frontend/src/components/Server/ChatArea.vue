<template>
  <div class="chat-area">
    <div class="chat-header">
      <div class="channel-info">
        <i class="fas fa-hashtag"></i>
        <span>{{ selectedChannel?.name || 'Select a channel' }}</span>
      </div>
    </div>

    <div class="messages-container">
      <div
        v-for="(message, index) in selectedChannel?.messages"
        :key="index"
        class="message"
      >
        <div class="avatar">
          <img v-if="message.user?.avatar" :src="message.user.avatar" :alt="message.user.name">
          <span v-else>{{ message.user?.name?.charAt(0) }}</span>
        </div>
        <div class="message-content">
          <div class="message-header">
            <span class="username">{{ message.user?.name || 'Guest' }}</span>
            <span class="timestamp">{{ message.time }}</span>
          </div>
          <div class="message-text">{{ message.text }}</div>
        </div>
      </div>
    </div>

    <div class="message-input">
      <div class="input-container">
        <i class="fas fa-plus"></i>
        <input
          v-model="newMessage"
          :placeholder="'Message #' + (selectedChannel?.name || '')"
          @keyup.enter="sendMessage"
          :disabled="!selectedChannel"
        />
        <div class="input-icons">
          <i class="fas fa-gift"></i>
          <i class="fas fa-grin"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  selectedChannel: Object,
});

const emit = defineEmits(['send-message']);

const newMessage = ref('');

const sendMessage = () => {
  if (newMessage.value.trim()) {
    emit('send-message', newMessage.value);
    newMessage.value = ''; // Clear input after sending
  }
};
</script>

<style scoped>
.chat-area {
  flex: 1;
  background: #f3e8d3;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.chat-header {
  padding: 16px;
  border-bottom: 1px solid #e5d5b3;
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
  color: #78350f;
  display: flex;
  align-items: center;
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
}

.message {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f59e0b;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar span {
  color: #78350f;
  font-size: 18px;
  font-weight: bold;
}

.message-content {
  display: flex;
  flex-direction: column;
}

.message-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.username {
  font-weight: bold;
  color: #78350f;
}

.timestamp {
  font-size: 12px;
  color: #7c2d12;
}

.message-text {
  color: #78350f;
}

.message-input {
  padding: 16px;
}

.input-container {
  background: #fff8e1;
  border-radius: 8px;
  padding: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid #e5d5b3;
}

.input-container input {
  flex: 1;
  background: transparent;
  border: none;
  color: #78350f;
  outline: none;
}

.input-container input:disabled {
  color: #b0a68b;
  cursor: not-allowed;
}

.input-icons {
  display: flex;
  gap: 8px;
  color: #78350f;
}

.input-icons i {
  cursor: pointer;
}

.input-icons i:hover {
  color: #7c2d12;
}
</style>
