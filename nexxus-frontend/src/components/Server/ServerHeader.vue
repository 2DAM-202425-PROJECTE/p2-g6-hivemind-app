<template>
  <div class="server-header">
    <h2 id="server-name">{{ selectedServer ? selectedServer.name : '' }}</h2>
    <button
      id="server-options-btn"
      class="options-button"
      @click="$emit('toggle-options-menu')"
    >
      ⋮
    </button>
    <div
      id="server-options-menu"
      class="options-menu"
      :class="{ hidden: !showOptionsMenu }"
    >
      <ul>
        <li @click="$emit('show-server-details')">View Server Details</li>
        <li @click="$emit('edit-server')">Server Settings</li>
        <li @click="$emit('leave-server')">Leave Server</li>
        <li @click="$emit('report-server')">Report Server</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
defineProps({
  selectedServer: Object,
  showOptionsMenu: Boolean,
});

defineEmits([
  'toggle-options-menu',
  'edit-server',
  'leave-server',
  'report-server',
  'show-server-details',
]);
</script>

<style scoped>
.server-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.options-button {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  margin-left: 10px;
}

.options-menu {
  position: absolute;
  right: 10px;
  top: 40px;
  background: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
  z-index: 100;
}

.options-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.options-menu li {
  padding: 10px;
  cursor: pointer;
  background-color: #7f7f7f;
  color: white;
  border-radius: 5px;
}

.options-menu li:hover {
  background-color: #0056b3;
}

.hidden {
  display: none;
}
</style>
