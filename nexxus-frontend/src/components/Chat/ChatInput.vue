<template>
  <div class="flex gap-3 items-center p-3 border-t border-amber-200 bg-white">
    <button class="p-2 text-amber-600 hover:text-amber-800 rounded-full hover:bg-amber-100">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
      </svg>
    </button>
    <textarea v-model="message"
              placeholder="Type your message here..."
              class="flex-1 border border-amber-200 rounded-lg p-3 bg-amber-50 text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-300 resize-none overflow-hidden placeholder-amber-400"
              @input="adjustHeight"
              @keydown.enter.exact.prevent="sendMessage"
              maxlength="1000"
              rows="1"></textarea>
    <button @click="sendMessage"
            class="bg-gradient-to-r from-amber-500 to-amber-600 text-white px-4 py-2 rounded-lg hover:from-amber-600 hover:to-amber-700 transition-all shadow-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
      </svg>
    </button>
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
