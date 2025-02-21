<template>
  <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-800">
    <img :src="message.user.profile_photo_url" alt="Avatar" class="w-10 h-10 rounded-full">
    <div>
      <div class="flex items-center gap-2">
        <h4 class="text-sm font-semibold text-white">{{ message.user.name }}</h4>
        <span class="text-xs text-gray-400">{{ message.timestamp }}</span>
      </div>
      <p class="text-white">{{ message.text }}</p>
    </div>
    <button v-if="message.isMine" @click="$emit('edit', message)">✏️</button>
    <button v-if="message.isMine" @click="$emit('delete', message)">🗑️</button>
    <button v-else @click="$emit('report', message)">⚠️</button>
  </div>
</template>

<script setup>
defineProps({
  message: Object
});
</script>
