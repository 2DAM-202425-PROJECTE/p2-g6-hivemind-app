<template>
  <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-100">
    <img :src="message.user.profile_photo_url" alt="Avatar" class="w-10 h-10 rounded-full">
    <div class="flex-1">
      <div class="flex items-center gap-2">
        <h4 class="text-sm font-semibold text-black">{{ message.user.name }}</h4>
        <span class="text-xs text-gray-600">{{ message.timestamp }}</span>
      </div>
      <div v-if="isEditing">
        <textarea v-model="editedContent" @blur="saveEdit" @keydown.enter="saveEdit" class="w-full mt-2 p-2 bg-gray-200 rounded-lg text-black resize-none" maxlength="5000"></textarea>
      </div>
      <p v-else class="text-black break-words">
        {{ message.text }}
        <span v-if="message.is_edited" class="text-xs text-gray-600">(edited)</span>
      </p>
    </div>
    <button v-if="message.isMine && !isEditing" @click="startEditing">✏️</button>
    <button v-if="message.isMine" @click="$emit('delete', message)">🗑️</button>
    <button v-else @click="$emit('report', message)">⚠️</button>
  </div>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps({
  message: Object
});

const emit = defineEmits(['edit', 'delete', 'report']);
const isEditing = ref(false);
const editedContent = ref(props.message.text);

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
</script>
