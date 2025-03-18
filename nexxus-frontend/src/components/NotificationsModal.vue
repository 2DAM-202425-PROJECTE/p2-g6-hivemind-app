<template>
  <div v-if="visible" class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <div class="modal-header">
        <span>Notifications</span>
        <button @click="close">X</button>
      </div>
      <div class="modal-body">
        <div v-for="notification in notifications" :key="notification.id" class="notification">
          <p>{{ notification.message }}</p>
          <button @click="removeNotification(notification.id)">X</button>
        </div>
        <button class="clear-all" @click="clearAll">Clear All</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  visible: Boolean,
  notifications: {
    type: Array,
    default: () => [
      { id: 1, message: 'Notification 1' },
      { id: 2, message: 'Notification 2' },
      { id: 3, message: 'Notification 3' }
    ]
  }
});

const emit = defineEmits(['close', 'update:notifications']);

const close = () => {
  emit('close');
};

const removeNotification = (id) => {
  const updatedNotifications = props.notifications.filter(notification => notification.id !== id);
  emit('update:notifications', updatedNotifications);
};

const clearAll = () => {
  emit('update:notifications', []);
};
</script>

<style scoped>
.modal-overlay {
  margin-right: 5px;
  position: fixed;
  top: 60px; /* Adjust to avoid clipping into the navbar */
  right: 0;
  width: 300px;
  height: calc(100% - 60px); /* Adjust to avoid clipping into the navbar */
  background: transparent; /* Remove the grayed-out background */
  display: flex;
  justify-content: flex-end;
  align-items: flex-start;
}

.modal-content {
  background: #7e7e7e;
  padding: 10px;
  border-radius: 10px;
  width: 100%;
  max-width: 300px;
  margin-top: 20px; /* Move the popup down a little */
  box-shadow: none;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  margin-top: 10px;
}

.notification {
  background: #f0f2f5;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.notification p {
  color: black;
}
.clear-all {
  background: #000000;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
  text-align: center;
}

.clear-all:hover {
  background: #0056b3;
}
</style>
