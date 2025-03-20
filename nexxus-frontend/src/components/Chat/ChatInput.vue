<template>
  <div class="flex gap-3 items-center border-t pt-3 border-gray-300 h-[60px] flex-shrink-0">
    <textarea v-model="message" placeholder="Type a message"
              class="flex-1 border rounded-lg p-2 bg-gray-100 text-black focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none overflow-hidden"
              @input="adjustHeight" @keydown.enter="sendMessage" maxlength="1000"></textarea>
    <button @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Send</button>
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';

const message = ref('');
const emit = defineEmits(['send']);

const sendMessage = () => {
  if (message.value.trim()) {
    emit('send', message.value);
    message.value = '';
    adjustHeight();
  }
};

const adjustHeight = () => {
  const textarea = document.querySelector('textarea');
  textarea.style.height = 'auto';
  textarea.style.height = `${textarea.scrollHeight}px`;
};

onMounted(() => {
  adjustHeight();
});
</script>
