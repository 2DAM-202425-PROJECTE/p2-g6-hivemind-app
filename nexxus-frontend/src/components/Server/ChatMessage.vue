<template>
  <div class="message">
    <div class="avatar">
      <img
        v-if="message.user?.avatar"
        :src="message.user.avatar"
        :alt="message.user.name"
        class="avatar-img"
      />
      <span v-else>{{ message.user?.name?.charAt(0) }}</span>
    </div>
    <div class="message-content">
      <div class="message-header">
        <h4 class="username">{{ message.user?.name || 'Guest' }}</h4>
        <span class="timestamp">{{ message.time }}</span>
      </div>
      <div v-if="isEditing" class="edit-container">
        <textarea
          v-model="editedContent"
          @blur="saveEdit"
          @keydown.enter.prevent="saveEdit"
          class="edit-textarea"
          maxlength="1000"
        ></textarea>
      </div>
      <p v-else class="message-text">
        {{ message.text }}
        <span v-if="message.isEdited" class="edited-label">(edited)</span>
      </p>
      <div v-if="message.isMine && !isEditing" class="action-buttons">
        <button @click="startEditing" class="action-button">✏️</button>
        <button @click="$emit('delete', message)" class="action-button">🗑️</button>
      </div>
      <div v-else-if="!message.isMine" class="action-buttons">
        <button @click="$emit('report', message)" class="action-button">⚠️</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  message: Object,
  userId: Number,
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
    emit('edit', { ...props.message, text: editedContent.value.trim(), isEdited: true });
  }
  isEditing.value = false;
};
</script>

<style scoped>
.message {
  display: flex;
  gap: 12px;
  padding: 8px 16px;
  border-radius: 8px;
  background: #fff8e1;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f59e0b;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar span {
  color: #78350f;
  font-size: 18px;
  font-weight: bold;
}

.message-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.message-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.username {
  font-weight: 600;
  color: #78350f;
}

.timestamp {
  font-size: 12px;
  color: #7c2d12;
}

.message-text {
  color: #78350f;
  word-break: break-word;
}

.edited-label {
  font-size: 12px;
  color: #7c2d12;
}

.edit-container {
  margin-top: 8px;
}

.edit-textarea {
  width: 100%;
  padding: 8px;
  background: #fef3c7;
  border: 1px solid #e5d5b3;
  border-radius: 6px;
  color: #78350f;
  resize: none;
  outline: none;
}

.edit-textarea:focus {
  border-color: #f59e0b;
}

.action-buttons {
  display: flex;
  gap: 8px;
  margin-top: 4px;
}

.action-button {
  background: none;
  border: none;
  color: #78350f;
  cursor: pointer;
  font-size: 16px;
}

.action-button:hover {
  color: #7c2d12;
}
</style>
