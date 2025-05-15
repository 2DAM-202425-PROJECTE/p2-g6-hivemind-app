<template>
  <div class="members-sidebar">
    <div class="search-container">
      <input placeholder="Search" />
    </div>

    <div class="members-list">
      <div class="section-header">ONLINE — {{ onlineUsers.length }}</div>
      <div
        v-for="user in onlineUsers"
        :key="user.id"
        class="member"
      >
        <div class="avatar"></div>
        <div class="username">
          <span>{{ user.name }}</span>
          <span v-if="user.activity" class="activity">{{ user.activity }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  onlineUsers: Array,
});
</script>

<style scoped>
.members-sidebar {
  width: 240px;
  background: #2b2d31;
  color: #96989d;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.search-container {
  padding: 12px;
  border-bottom: 1px solid #1e1f22;
}

.search-container input {
  width: 100%;
  padding: 6px;
  background: #1e1f22;
  border: none;
  border-radius: 4px;
  color: white;
  outline: none;
}

.members-list {
  flex: 1;
  overflow-y: auto;
  padding: 12px;
}

.section-header {
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
  margin-bottom: 16px;
}

.member {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  padding: 4px;
  border-radius: 4px;
}

.member:hover {
  background: #3d3f45;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #5865f2;
}

.username {
  display: flex;
  flex-direction: column;
}

.username span:first-child {
  font-size: 14px;
  color: white;
}

.activity {
  font-size: 12px;
  color: #b5bac1;
}
</style>
