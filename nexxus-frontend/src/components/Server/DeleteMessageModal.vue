<template>
  <Teleport to="body">
    <div class="modal-overlay">
      <div class="modal">
        <h2 class="modal-title">Delete Message</h2>
        <p class="modal-text">Are you sure you want to delete this message?</p>

        <div v-if="message && message.user" class="message-preview">
          <div class="avatar">
            <img
              v-if="message.user.avatar"
              :src="message.user.avatar"
              :alt="message.user.name"
              class="avatar-img"
            />
            <span v-else>{{ message.user.name?.charAt(0) }}</span>
          </div>
          <div class="message-details">
            <div class="message-header">
              <h4 class="username">{{ message.user.name }}</h4>
              <span class="timestamp">{{ message.time }}</span>
            </div>
            <p class="message-text">{{ message.text }}</p>
          </div>
        </div>

        <div class="modal-buttons">
          <button @click="$emit('cancel')" class="cancel-button">Cancel</button>
          <button @click="$emit('confirm')" class="delete-button">Delete</button>
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

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: #fff8e1;
  border-radius: 12px;
  padding: 24px;
  width: 400px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.modal-title {
  font-size: 18px;
  font-weight: 600;
  color: #78350f;
}

.modal-text {
  color: #7c2d12;
  font-size: 14px;
}

.message-preview {
  background: #fef3c7;
  padding: 12px;
  border-radius: 8px;
  display: flex;
  gap: 12px;
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

.message-details {
  flex: 1;
}

.message-header {
  display: flex;
  align-items: center;
  gap: 8px;
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

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.cancel-button {
  padding: 8px 16px;
  background: #e5d5b3;
  color: #78350f;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.cancel-button:hover {
  background: #d9c7a0;
}

.delete-button {
  padding: 8px 16px;
  background: #b45309;
  color: #fff8e1;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.delete-button:hover {
  background: #a03e08;
}
</style>
