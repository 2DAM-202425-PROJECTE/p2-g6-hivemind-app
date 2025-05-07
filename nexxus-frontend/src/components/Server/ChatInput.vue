<template>
  <div class="input-container">
    <textarea
      v-model="message"
      :placeholder="'Message #' + (channelName || 'channel')"
      class="textarea"
      @input="adjustHeight"
      @keydown="handleKeydown"
      maxlength="1000"
    ></textarea>
    <button @click="sendMessage" class="send-button">Send</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  channelName: String,
});

const message = ref('');
const emit = defineEmits(['send']);

const sendMessage = () => {
  if (message.value.trim()) {
    emit('send', message.value);
    message.value = '';
    adjustHeight();
  }
};

const handleKeydown = (event) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};

const adjustHeight = () => {
  const textarea = document.querySelector('.textarea');
  if (textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = `${textarea.scrollHeight}px`;
  }
};

onMounted(() => {
  adjustHeight();
});
</script>

<style scoped>
.input-container {
  padding: 16px;
  background: #fff8e1;
  border-top: 1px solid #e5d5b3;
  display: flex;
  align-items: center;
  gap: 12px;
}

.textarea {
  flex: 1;
  border: 1px solid #e5d5b3;
  border-radius: 8px;
  padding: 10px;
  background: #fff8e1;
  color: #78350f;
  resize: none;
  overflow: hidden;
  outline: none;
  max-height: 100px;
}

.textarea:focus {
  border-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
}

.send-button {
  padding: 8px 16px;
  background: linear-gradient(to right, #f59e0b, #d97706);
  color: #78350f;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.send-button:hover {
  background: linear-gradient(to right, #f59e0b, #b45309);
  transform: translateY(-1px);
}
</style>
