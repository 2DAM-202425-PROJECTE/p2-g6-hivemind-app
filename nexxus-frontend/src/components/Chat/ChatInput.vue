<template>
  <div class="flex gap-3 items-center border-t pt-3 border-gray-700 h-[60px] flex-shrink-0">
    <input v-model="message" type="text" placeholder="Type a message"
           class="flex-1 border rounded-lg p-2 bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
           @keydown.enter="sendMessage">
    <button @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Send</button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref('');
const emit = defineEmits(['send']);

const sendMessage = () => {
  if (message.value.trim()) {
    emit('send', message.value);
    message.value = '';
  }
};
</script>
