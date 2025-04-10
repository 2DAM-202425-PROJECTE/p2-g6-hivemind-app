<template>
  <Teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 backdrop-blur-sm">
      <div class="bg-white rounded-xl p-6 w-96 shadow-xl border border-amber-200 animate-pop-in">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-2 bg-amber-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h2 class="text-xl font-bold text-amber-800">Delete Message</h2>
        </div>

        <p class="text-gray-600 mb-4">Are you sure you want to delete this message? This action cannot be undone.</p>

        <div v-if="message && message.user" class="mb-4 p-3 bg-amber-50 rounded-lg border border-amber-200">
          <div class="flex items-start gap-2">
            <img :src="message.user.profile_photo_url" alt="Avatar" class="w-8 h-8 rounded-full border border-amber-300">
            <div>
              <div class="flex items-center gap-2">
                <h4 class="text-sm font-semibold text-amber-800">{{ message.user.name }}</h4>
                <span class="text-xs text-amber-600">{{ formatTime(message.timestamp) }}</span>
              </div>
              <p class="text-sm text-gray-700 mt-1">{{ message.text }}</p>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <button @click="$emit('cancel')"
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
            Cancel
          </button>
          <button @click="$emit('confirm')"
                  class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-lg hover:from-amber-600 hover:to-amber-700 transition-all shadow-md">
            Delete
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  message: Object,
});

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>

<style scoped>
.animate-pop-in {
  animation: pop-in 0.2s ease-out forwards;
}

@keyframes pop-in {
  0% {
    transform: scale(0.95);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
