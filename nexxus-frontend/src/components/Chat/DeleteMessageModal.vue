<template>
  <Teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center bg-black/75 z-50">
      <div class="bg-gray-800 text-white rounded-lg p-6 w-96 shadow-lg">
        <h2 class="text-lg font-semibold">Delete Message</h2>
        <p class="mt-2 text-gray-300">Are you sure you want to delete this message?</p>

        <!-- Check if message and message.user are defined -->
        <div v-if="message && message.user" class="mt-4 p-3 bg-gray-700 rounded-lg flex items-start gap-3">
          <img :src="message.user.profile_photo_url" alt="Avatar" class="w-10 h-10 rounded-full">
          <div>
            <div class="flex items-center gap-2">
              <h4 class="text-sm font-semibold">{{ message.user.name }}</h4>
              <span class="text-xs text-gray-400">{{ message.timestamp }}</span>
            </div>
            <p class="text-gray-200">{{ message.text }}</p>
          </div>
        </div>

        <!-- Botones -->
        <div class="mt-4 flex justify-end gap-2">
          <button @click="$emit('cancel')" class="px-4 py-2 bg-gray-600 text-white rounded-lg">Cancel</button>
          <button @click="$emit('confirm')" class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  message: Object,
});
</script>
