<template>
  <div class="stories-bar">
    <!-- Add Story Button -->
    <div class="story-item add-story" @click="showCreateStoryModal = true">
      <div class="add-story-icon">
        <span class="plus-sign">+</span>
      </div>
      <p class="story-name">Add Story</p>
    </div>

    <!-- Grouped Stories by User (Newest on Left) -->
    <div
      class="story-item"
      v-for="(userStories, userId) in groupedStories"
      :key="userId"
      @click="viewUserStories(userId)"
    >
      <img
        :src="getProfilePhotoById(parseInt(userId))"
        alt="User Story"
        class="story-image"
      />
      <p class="story-name">{{ getUserInitialsById(parseInt(userId)) }}</p>
    </div>

    <!-- Create Story Modal -->
    <CreateStoryImage v-model="showCreateStoryModal" @storyCreated="fetchStories" />

    <!-- Story View Modal -->
    <v-dialog v-model="showStoryModal" max-width="500">
      <v-card>
        <v-card-title>
          <span class="clickable-name" @click="goToUserProfile(selectedStory?.id_user)">
            {{ getUserNameById(selectedStory?.id_user) }}
          </span>
        </v-card-title>
        <v-card-text>
          <div class="story-media-container">
            <template v-if="isImage(selectedStory?.file_path)">
              <img
                :src="getStoryImagePath(selectedStory?.file_path)"
                alt="Story Image"
                class="story-modal-image"
              />
            </template>
            <template v-else>
              <video
                :src="getStoryImagePath(selectedStory?.file_path)"
                controls
                class="story-modal-video"
              ></video>
            </template>
          </div>
          <p>{{ selectedStory?.description || 'No description' }}</p>
          <!-- Display Story Count -->
          <p class="story-count">
            {{ currentStoryIndex + 1 }} of {{ currentUserStories.length }}
          </p>
          <!-- Navigation Buttons -->
          <div class="story-navigation">
            <v-btn
              icon
              @click="navigateStory('prev')"
              :disabled="currentStoryIndex === 0 && isFirstUser"
              class="nav-btn"
            >
              <v-icon>mdi-chevron-left</v-icon>
            </v-btn>
            <v-btn
              icon
              @click="navigateStory('next')"
              :disabled="currentStoryIndex === currentUserStories.length - 1 && isLastUser"
              class="nav-btn"
            >
              <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="showStoryModal = false">Close</v-btn>
          `          <v-btn
          color="red"
          text
          @click="deleteStory(selectedStory.id)"
          v-if="isStoryFromUser(selectedStory)"
        >
          Delete
        </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from "@/axios.js";
import CreateStoryImage from './CreateStoryImage.vue';

const router = useRouter();
const users = ref([]);
const showCreateStoryModal = ref(false);
const showStoryModal = ref(false);
const selectedStory = ref(null);
const currentStoryIndex = ref(0);
const lastViewedIndices = ref({}); // Store last viewed index for each user
const stories = ref([]);
const currentUser = ref({
  id: null,
  name: 'Current User',
  profile_photo_path: null,
});

const props = defineProps({
  stories: {
    type: Array,
    required: true,
  },
});

const API_BASE_URL = 'http://localhost:8000';

// Group stories by user ID and sort by newest first
const groupedStories = computed(() => {
  const grouped = {};
  props.stories.forEach((story) => {
    if (!grouped[story.id_user]) {
      grouped[story.id_user] = [];
    }
    grouped[story.id_user].push(story);
  });
  Object.keys(grouped).forEach((userId) => {
    grouped[userId].sort((a, b) => {
      const dateA = new Date(a.publish_date || a.created_at);
      const dateB = new Date(b.publish_date || b.created_at);
      return dateB - dateA;
    });
  });
  return Object.entries(grouped)
    .sort(([, aStories], [, bStories]) => {
      const aDate = new Date(aStories[0].publish_date || aStories[0].created_at);
      const bDate = new Date(bStories[0].publish_date || bStories[0].created_at);
      return bDate - aDate; // Correct: Use aDate
    })
    .reduce((acc, [userId, stories]) => {
      acc[userId] = stories;
      return acc;
    }, {});
});

const currentUserStories = computed(() => {
  if (!selectedStory.value) return [];
  return groupedStories.value[selectedStory.value.id_user] || [];
});

// Check if the current user is the first or last in the groupedStories
const isFirstUser = computed(() => {
  if (!selectedStory.value) return true;
  const userIds = Object.keys(groupedStories.value);
  return userIds.indexOf(String(selectedStory.value.id_user)) === 0;
});

const isLastUser = computed(() => {
  if (!selectedStory.value) return true;
  const userIds = Object.keys(groupedStories.value);
  return userIds.indexOf(String(selectedStory.value.id_user)) === userIds.length - 1;
});

const isImage = (filePath) => /\.(jpeg|jpg|png)$/i.test(filePath);

const isStoryFromUser = (story) => story?.id_user === currentUser.value.id;

const getProfilePhotoById = (id) => {
  const user = users.value.find((user) => user.id === id);
  return user && user.profile_photo_path
    ? `${API_BASE_URL}/storage/${user.profile_photo_path}`
    : user?.profile_photo_url || 'https://via.placeholder.com/150';
};

const getUserNameById = (id) => {
  const user = users.value.find((user) => user.id === id);
  return user?.name.trim() || 'Unknown';
};

const getUserInitialsById = (id) => {
  const user = users.value.find((user) => user.id === id);
  if (!user || !user.name) return 'UN';
  const name = user.name.trim();
  const words = name.split(' ').filter((word) => word.length > 0);
  if (words.length > 1) {
    const firstName = words[0];
    const surname = words[words.length - 1];
    return `${firstName[0]}.${surname[0]}`.toUpperCase();
  } else {
    return words[0].substring(0, 2).toUpperCase();
  }
};

const getUsernameById = (id) => {
  const user = users.value.find((user) => user.id === id);
  return user?.username || null;
};

const goToUserProfile = (userId) => {
  const username = getUsernameById(userId);
  if (username) {
    router.push(`/profile/${username}`);
  } else {
    console.warn('No username found for userId:', userId);
  }
};

const getStoryImagePath = (path) => {
  if (!path) return 'https://via.placeholder.com/150';
  return `${API_BASE_URL}/storage/${path}`;
};

const viewUserStories = (userId) => {
  const userStories = groupedStories.value[userId];
  if (userStories && userStories.length > 0) {
    selectedStory.value = userStories[0];
    currentStoryIndex.value = lastViewedIndices.value[userId] || 0;
    if (currentStoryIndex.value >= userStories.length) {
      currentStoryIndex.value = userStories.length - 1;
    }
    selectedStory.value = userStories[currentStoryIndex.value];
    showStoryModal.value = true;
  }
};

const navigateStory = (direction) => {
  const currentUserId = String(selectedStory.value.id_user);
  // Save current index before navigating
  lastViewedIndices.value[currentUserId] = currentStoryIndex.value;

  if (
    direction === 'next' &&
    currentStoryIndex.value < currentUserStories.value.length - 1
  ) {
    currentStoryIndex.value++;
    selectedStory.value = currentUserStories.value[currentStoryIndex.value];
  } else if (direction === 'prev' && currentStoryIndex.value > 0) {
    currentStoryIndex.value--;
    selectedStory.value = currentUserStories.value[currentStoryIndex.value];
  } else if (direction === 'next' && !isLastUser.value) {
    // Move to the next user's stories
    const userIds = Object.keys(groupedStories.value);
    const currentUserIndex = userIds.indexOf(currentUserId);
    const nextUserIndex = currentUserIndex + 1;
    const nextUserId = userIds[nextUserIndex];
    const nextUserStories = groupedStories.value[nextUserId];
    if (nextUserStories && nextUserStories.length > 0) {
      selectedStory.value = nextUserStories[0];
      currentStoryIndex.value = lastViewedIndices.value[nextUserId] || 0;
      if (currentStoryIndex.value >= nextUserStories.length) {
        currentStoryIndex.value = nextUserStories.length - 1;
      }
      selectedStory.value = nextUserStories[currentStoryIndex.value];
    }
  } else if (direction === 'prev' && !isFirstUser.value) {
    // Move to the previous user's stories
    const userIds = Object.keys(groupedStories.value);
    const currentUserIndex = userIds.indexOf(currentUserId);
    const prevUserIndex = currentUserIndex - 1;
    const prevUserId = userIds[prevUserIndex];
    const prevUserStories = groupedStories.value[prevUserId];
    if (prevUserStories && prevUserStories.length > 0) {
      selectedStory.value = prevUserStories[0];
      currentStoryIndex.value = lastViewedIndices.value[prevUserId] || 0;
      if (currentStoryIndex.value >= prevUserStories.length) {
        currentStoryIndex.value = prevUserStories.length - 1;
      }
      selectedStory.value = prevUserStories[currentStoryIndex.value];
    }
  }
};

const fetchStories = async () => {
  try {
    const response = await apiClient.get('/api/stories');
    stories.value = response.data;
    // Reset indices if stories change
    lastViewedIndices.value = {};
  } catch (error) {
    console.error('Error fetching stories:', error);
  }
};

const deleteStory = async (id) => {
  try {
    await apiClient.delete(`/api/stories/${id}`);
    const currentUserId = String(selectedStory.value.id_user);
    await fetchStories();
    if (currentUserStories.value.length === 0) {
      showStoryModal.value = false;
    } else {
      if (currentStoryIndex.value >= currentUserStories.value.length) {
        currentStoryIndex.value = currentUserStories.value.length - 1;
      }
      selectedStory.value = currentUserStories.value[currentStoryIndex.value];
      // Update last viewed index
      lastViewedIndices.value[currentUserId] = currentStoryIndex.value;
    }
  } catch (error) {
    console.error('Error deleting story:', error);
  }
};

onMounted(async () => {
  try {
    const usersResult = await apiClient.get('/api/users');
    users.value = usersResult.data.data;

    const storiesResult = await apiClient.get('/api/stories');
    stories.value = storiesResult.data;

    const userResult = await apiClient.get('/api/user');
    currentUser.value = userResult.data;
  } catch (error) {
    console.error('Error during initialization:', error);
  }
});
</script>

<style scoped>
.stories-bar {
  display: flex;
  flex-direction: row;
  gap: 2px;
  overflow-x: auto;
  padding: 10px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  scrollbar-width: thin;
  scrollbar-color: #007bff transparent;
}

.stories-bar::-webkit-scrollbar {
  height: 8px;
}

.stories-bar::-webkit-scrollbar-thumb {
  background-color: #007bff;
  border-radius: 10px;
}

.stories-bar::-webkit-scrollbar-track {
  background: transparent;
}

.story-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  width: 80px;
  flex-shrink: 0;
}

.story-image {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid #007bff;
  object-fit: cover;
}

.add-story {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 80px;
  flex-shrink: 0;
}

.add-story-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid #007bff;
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: bold;
  color: #007bff;
}

.story-name {
  margin: 5px 0 0;
  font-size: 12px;
  text-align: center;
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.story-media-container {
  width: 100%;
  max-width: 450px;
  height: 450px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f5f5f5;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 10px;
}

.story-modal-image,
.story-modal-video {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 10px;
}

.story-navigation {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.nav-btn {
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
}

.story-count {
  font-size: 14px;
  color: #666;
  margin-top: 10px;
  text-align: center;
}

.clickable-name {
  cursor: pointer;
}

.clickable-name:hover {
  text-decoration: underline;
}
</style>
