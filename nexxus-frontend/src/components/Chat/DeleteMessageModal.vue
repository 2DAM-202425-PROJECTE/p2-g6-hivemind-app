<template>
  <Teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center bg-black/75 z-50">
      <div class="bg-white text-black rounded-lg p-6 w-96 shadow-lg">
        <h2 class="text-lg font-semibold">Delete Message</h2>
        <p class="mt-2 text-gray-600">Are you sure you want to delete this message?</p>

        <div v-if="message && message.user" class="mt-4 p-3 bg-gray-200 rounded-lg flex items-start gap-3">
          <img :src="message.user.profile_photo_url" alt="Avatar" class="w-10 h-10 rounded-full">
          <div>
            <div class="flex items-center gap-2">
              <h4 class="text-sm font-semibold">{{ message.user.name }}</h4>
              <span class="text-xs text-gray-600">{{ message.timestamp }}</span>
            </div>
            <p class="text-gray-800">{{ message.text }}</p>
          </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button @click="$emit('cancel')" class="px-4 py-2 bg-gray-300 text-black rounded-lg">Cancel</button>
          <button @click="$emit('confirm')" class="px-4 py-2 bg-red-500 text-white rounded-lg">Delete</button>
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
