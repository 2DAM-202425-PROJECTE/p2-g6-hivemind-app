<template>
  <div class="bg-white rounded-lg shadow-md p-5 max-w-xs w-full overflow-y-auto max-h-[calc(100vh-150px)]">
    <div v-for="(chat, index) in chats" :key="index" @click="$emit('select-chat', chat)"
         class="flex items-center gap-3 p-3 border-b border-gray-300 hover:bg-gray-200 cursor-pointer">
      <img :src="chat.profile_photo_url" alt="Avatar" class="w-12 h-12 rounded-full">
      <div>
        <h3 class="text-lg font-semibold text-black">{{ chat.name }}</h3>
        <p class="text-sm text-gray-600">{{ chat.email }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  chats: Array
});
</script>
