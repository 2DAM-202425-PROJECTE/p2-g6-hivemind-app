<template>
  <div class="stories-bar">
    <!-- Add Story Button -->
    <div
      v-if="isCurrentUser"
      class="story-item add-story"
      @click="showCreateStoryModal = true"
    >
      <div class="add-story-icon">
        <span class="plus-sign">+</span>
      </div>
    </div>

    <!-- User's Stories -->
    <div
      class="story-item"
      v-for="story in userStories"
      :key="story.id"
      @click="viewStory(story)"
    >
      <img
        :src="getStoryImagePath(story.file_path)"
        :alt="story.description || 'Story'"
        class="story-image"
      />
    </div>

    <!-- Create Story Modal -->
    <CreateStoryImage v-model="showCreateStoryModal" @storyCreated="fetchStories" />

    <!-- Story View Modal -->
    <v-dialog v-model="showStoryModal" max-width="500">
      <v-card>
        <v-card-title>Your Story</v-card-title>
        <v-card-text>
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
          <p>{{ selectedStory?.description || 'No description' }}</p>
          <!-- Navigation Buttons -->
          <div class="story-navigation" v-if="userStories.length > 1">
            <v-btn
              icon
              @click="navigateStory('prev')"
              :disabled="currentStoryIndex === 0"
              class="nav-btn"
            >
              <v-icon>mdi-chevron-left</v-icon>
            </v-btn>
            <v-btn
              icon
              @click="navigateStory('next')"
              :disabled="currentStoryIndex === userStories.length - 1"
              class="nav-btn"
            >
              <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="showStoryModal = false">Close</v-btn>
          <v-btn
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
import { ref, computed, onMounted } from 'vue';
import apiClient from '@/axios.js';
import CreateStoryImage from '@/components/CreateStoryImage.vue';

const props = defineProps({
  stories: {
    type: Array,
    default: () => [],
    validator: (stories) =>
      stories.every((story) => typeof story === 'object' && 'id' in story && 'id_user' in story),
  },
  profileUserId: {
    type: Number,
    required: true,
  },
  role: {
    type: String,
    default: null,
  },
  isCurrentUser: {
    type: Boolean,
    required: true,
  },
});

const showCreateStoryModal = ref(false);
const showStoryModal = ref(false);
const selectedStory = ref(null);
const currentStoryIndex = ref(0);
const API_BASE_URL = 'http://localhost:8000';

const userStories = computed(() => {
  return props.stories
    .filter((story) => story.id_user === props.profileUserId && (!props.role || story.role === props.role))
    .sort((a, b) => {
      const dateA = new Date(a.publish_date || a.created_at);
      const dateB = new Date(b.publish_date || b.created_at);
      return dateB - dateA; // Newest first
    });
});

const isImage = (filePath) => /\.(jpeg|jpg|png)$/i.test(filePath);

const getStoryImagePath = (filePath) => {
  if (!filePath) return 'https://via.placeholder.com/150';
  return `${API_BASE_URL}/storage/${filePath}`;
};

const isStoryFromUser = (story) => {
  return story?.id_user === props.profileUserId;
};

const viewStory = (story) => {
  selectedStory.value = story;
  currentStoryIndex.value = userStories.value.findIndex((s) => s.id === story.id);
  showStoryModal.value = true;
};

const navigateStory = (direction) => {
  if (direction === 'next' && currentStoryIndex.value < userStories.value.length - 1) {
    currentStoryIndex.value++;
    selectedStory.value = userStories.value[currentStoryIndex.value];
  } else if (direction === 'prev' && currentStoryIndex.value > 0) {
    currentStoryIndex.value--;
    selectedStory.value = userStories.value[currentStoryIndex.value];
  }
};

const fetchStories = async () => {
  try {
    // Fetch stories with profile_user_id and role as query parameters
    const response = await apiClient.get('/api/stories', {
      params: {
        profile_user_id: props.profileUserId,
        role: props.role,
      },
    });
    props.stories.splice(0, props.stories.length, ...(response.data.data || []));
    currentStoryIndex.value = 0; // Reset index after fetching
  } catch (error) {
    console.error('Error fetching stories:', error.response?.data || error.message);
  }
};

const deleteStory = async (id) => {
  try {
    await apiClient.delete(`/api/stories/${id}`);
    await fetchStories();
    showStoryModal.value = false;
  } catch (error) {
    console.error('Error deleting story:', error.response?.data || error.message);
  }
};
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

.story-modal-image,
.story-modal-video {
  width: 100%;
  max-width: 450px;
  height: 450px;
  object-fit: contain;
  border-radius: 10px;
  margin-bottom: 10px;
  display: block;
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
</style>
