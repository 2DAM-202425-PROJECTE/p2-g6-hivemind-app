<template>
  <div :class="['flex items-start gap-3 p-3 rounded-lg transition-all',
               message.isMine ? 'bg-amber-100 ml-8' : 'bg-white mr-8 border border-amber-200']">
    <img :src="message.user.profile_photo_url" alt="Avatar" class="w-8 h-8 rounded-full border border-amber-300 flex-shrink-0">
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 mb-1">
        <h4 class="text-sm font-semibold" :class="message.isMine ? 'text-amber-800' : 'text-amber-900'">
          {{ message.isMine ? 'You' : message.user.name }}
        </h4>
        <span class="text-xs" :class="message.isMine ? 'text-amber-600' : 'text-amber-500'">
          {{ formatTime(message.timestamp) }}
        </span>
        <span v-if="message.is_edited" class="text-xs text-amber-500">(edited)</span>
      </div>

      <div v-if="isEditing" class="mt-1">
        <textarea v-model="editedContent"
                  @blur="saveEdit"
                  @keydown.enter.exact.prevent="saveEdit"
                  class="w-full p-2 bg-amber-50 rounded-lg text-amber-900 border border-amber-300 focus:outline-none focus:ring-1 focus:ring-amber-400 resize-none"
                  rows="3"
                  maxlength="5000"
                  autofocus></textarea>
        <div class="flex justify-end gap-2 mt-2">
          <button @click="cancelEdit" class="text-xs text-amber-600 hover:text-amber-800 px-2 py-1">
            Cancel
          </button>
          <button @click="saveEdit" class="text-xs bg-amber-500 text-white px-2 py-1 rounded hover:bg-amber-600">
            Save
          </button>
        </div>
      </div>
      <p v-else class="text-sm" :class="message.isMine ? 'text-amber-900' : 'text-gray-800'">
        {{ message.text }}
      </p>
    </div>

    <div v-if="!isEditing" class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
      <button v-if="message.isMine" @click="startEditing" class="p-1 text-amber-500 hover:text-amber-700 rounded-full hover:bg-amber-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
      </button>
      <button v-if="message.isMine" @click="$emit('delete', message)" class="p-1 text-amber-500 hover:text-amber-700 rounded-full hover:bg-amber-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </button>
      <button v-else @click="$emit('report', message)" class="p-1 text-amber-500 hover:text-amber-700 rounded-full hover:bg-amber-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps({
  message: Object,
  userId: Number
});

const emit = defineEmits(['edit', 'delete', 'report']);
const isEditing = ref(false);
const editedContent = ref(props.message.text);

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const startEditing = () => {
  isEditing.value = true;
  editedContent.value = props.message.text;
};

const saveEdit = () => {
  if (editedContent.value.trim() && editedContent.value !== props.message.text) {
    emit('edit', {...props.message, text: editedContent.value.trim()});
  }
  isEditing.value = false;
};

const cancelEdit = () => {
  isEditing.value = false;
};
</script>
