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
  position: relative;
  padding: 0 16px;
}

.options-button {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #78350f;
  padding: 8px;
  transition: color 0.2s ease;
}

.options-button:hover {
  color: #7c2d12;
}

.options-menu {
  position: absolute;
  top: 100%;
  right: 16px;
  background: #fff8e1;
  border: 1px solid #e5d5b3;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  z-index: 1001; /* Increased to ensure visibility above modals */
  min-width: 160px;
}

.options-menu ul {
  list-style: none;
  padding: 8px 0;
  margin: 0;
}

.options-menu li {
  padding: 8px 16px;
  cursor: pointer;
  background: #fff8e1;
  color: #78350f;
  font-size: 14px;
  transition: all 0.3s ease;
  border-radius: 4px;
  margin: 2px 4px;
}

.options-menu li:hover {
  background: #fef3c7;
  color: #7c2d12;
  transform: translateY(-1px);
}

.hidden {
  display: none;
}
</style>
