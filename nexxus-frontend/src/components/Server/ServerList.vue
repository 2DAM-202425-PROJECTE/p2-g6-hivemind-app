<template>
  <div class="server-sidebar">
    <div class="server-icon home-icon" @click="$emit('select-server', null)">
      <img src="@/assets/logo.png" alt="Home" />
    </div>
    <div class="divider"></div>
    <div
      v-for="server in servers"
      :key="server.id"
      class="server-icon"
      :class="{ active: selectedServer?.id === server.id }"
      @click="$emit('select-server', server)"
    >
      <img v-if="server.icon" :src="server.icon" :alt="server.name" />
      <span v-else>{{ server.name.charAt(0) }}</span>
    </div>
    <div class="server-icon add-server" @click="$emit('show-create-server')">
      <span>+</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  servers: Array,
  selectedServer: Object,
});

defineEmits(['select-server', 'show-create-server']);
</script>

<style scoped>
.server-sidebar {
  width: 72px;
  background: #1e1f22;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 12px 0;
  gap: 8px;
  height: 100vh;
  overflow-y: auto;
}

.server-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #313338;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  overflow: hidden;
}

.server-icon:hover {
  border-radius: 16px;
  background: #5865f2;
}

.server-icon.active {
  border-radius: 16px;
  background: #5865f2;
}

.server-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.server-icon span {
  color: white;
  font-size: 18px;
  font-weight: bold;
}

.home-icon {
  background: #5865f2;
}

.add-server {
  background: #313338;
  color: #3ba55c;
}

.add-server:hover {
  background: #3ba55c;
  color: white;
}

.divider {
  width: 32px;
  height: 2px;
  background: #2b2d31;
  margin: 8px 0;
}
</style>
