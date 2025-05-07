<template>
  <div class="channel-sidebar">
    <div class="server-header">
      <h2 class="text-2xl font-bold text-amber-900">{{ selectedServer?.name || 'Select a server' }}</h2>
    </div>

    <div class="channels-container">
      <div v-for="category in categories" :key="category.id" class="category">
        <div class="category-header" @click="toggleCategory(category.id)">
          <i class="fas fa-chevron-down" :class="{ rotated: !collapsedCategories[category.id] }"></i>
          <span class="text-amber-900">{{ category.name }}</span>
        </div>

        <div v-if="!collapsedCategories[category.id]" class="channel-list">
          <div
            v-for="channel in category.channels"
            :key="channel.id"
            class="channel-item"
            :class="{ active: selectedChannel?.id === channel.id }"
            @click="$emit('select-channel', channel)"
          >
            <span># {{ channel.name }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="user-panel">
      <div class="user-info" @click="goToProfile">
        <div class="avatar" :style="{ backgroundImage: `url(${currentUser.avatar})` }"></div>
        <div class="username">
          <span class="font-bold text-amber-900">{{ currentUser.username }}</span>
          <span class="text-amber-700">#{{ currentUser.id }}</span>
        </div>
      </div>
      <div class="user-controls">
        <i class="fas fa-microphone text-amber-700 hover:text-amber-600"></i>
        <i class="fas fa-headphones text-amber-700 hover:text-amber-600"></i>
        <i class="fas fa-cog text-amber-700 hover:text-amber-600"></i>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  selectedServer: Object,
  selectedChannel: Object,
  currentUser: Object,
});

defineEmits(['select-channel']);

const collapsedCategories = ref({});

// Dynamically compute categories and their channels
const categories = computed(() => {
  if (!props.selectedServer) return [];

  // Get all categories
  const cats = props.selectedServer.channels
    .filter((c) => c.isCategory)
    .map((category) => ({
      ...category,
      channels: props.selectedServer.channels.filter(
        (c) => c.parentCategoryId === category.id && !c.isCategory
      ),
    }));

  // Add uncategorized channels under a default category
  const uncategorized = props.selectedServer.channels.filter(
    (c) => !c.isCategory && !c.parentCategoryId
  );

  if (uncategorized.length) {
    cats.unshift({
      id: 0,
      name: 'TEXT CHANNELS',
      isCategory: true,
      channels: uncategorized,
    });
  }

  return cats;
});

const toggleCategory = (id) => {
  collapsedCategories.value[id] = !collapsedCategories.value[id];
};

const goToProfile = () => {
  router.push(`/profile/${props.currentUser.username}`);
};
</script>

<style scoped>
.channel-sidebar {
  width: 240px;
  background: #fff8e1;
  color: #78350f;
  display: flex;
  flex-direction: column;
  height: 100vh;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.server-header {
  padding: 16px;
  border-bottom: 1px solid #f3e8d3;
  font-weight: bold;
}

.channels-container {
  flex: 1;
  overflow-y: auto;
  padding: 8px 0;
}

.category {
  margin-bottom: 16px;
}

.category-header {
  padding: 6px 16px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 4px;
  cursor: pointer;
  user-select: none;
}

.category-header i {
  font-size: 10px;
  transition: transform 0.2s;
  color: #78350f;
}

.category-header i.rotated {
  transform: rotate(-90deg);
}

.channel-list {
  padding-left: 8px;
}

.channel-item {
  padding: 6px 8px 6px 30px;
  margin: 2px 8px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  color: #78350f;
}

.channel-item:hover {
  background: #fef3c7;
  color: #7c2d12;
}

.channel-item.active {
  background: #fef3c7;
  color: #7c2d12;
}

.user-panel {
  background: #fef3c7;
  padding: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.user-info:hover .username span {
  color: #7c2d12;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #f59e0b;
  background-size: cover;
  background-position: center;
}

.username {
  display: flex;
  flex-direction: column;
}

.username span:first-child {
  font-size: 14px;
}

.username span:last-child {
  font-size: 12px;
}

.user-controls {
  display: flex;
  gap: 8px;
}

.user-controls i {
  cursor: pointer;
}
</style>
