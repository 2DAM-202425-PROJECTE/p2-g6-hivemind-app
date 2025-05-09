<template>
  <div class="stories-bar">
    <!-- Botó per afegir històries -->
    <div class="story-item add-story" @click="showCreateStoryModal = true">
      <div class="add-story-icon">
        <span class="plus-sign">+</span>
      </div>
      <p class="story-name">Add Story</p>
    </div>

    <!-- Històries existents -->
    <div class="story-item" v-for="(story, index) in sortedStories" :key="story.id" @click="viewStory(story)">
      <img :src="getProfilePhotoById(story.id_user)" alt="Story" class="story-image" />
      <p class="story-name">{{ getUserNameById(story.id_user) }}</p>
    </div>

    <CreateStoryImage v-model="showCreateStoryModal" @storyCreated="fetchStories" />

    <!-- Modal para mostrar la historia -->
    <v-dialog v-model="showStoryModal" max-width="500">
      <v-card>
        <v-card-title>{{ getUserNameById(selectedStory?.id_user) }}</v-card-title>
        <v-card-text>
          <template v-if="isImage(selectedStory?.file_path)">
            <img :src="getStoryImagePath(selectedStory.file_path)" alt="Story Image" class="story-modal-image" />
          </template>
          <template v-else>
            <video :src="getStoryImagePath(selectedStory.file_path)" controls class="story-modal-video"></video>
          </template>
          <p>{{ selectedStory?.description }}</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="showStoryModal = false">Close</v-btn>
          <v-btn color="red" text @click="deleteStory(selectedStory.id)" v-if="isStoryFromUser(selectedStory)">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import apiClient from "@/axios.js";
import CreateStoryImage from './CreateStoryImage.vue';

const users = ref([]);
const showCreateStoryModal = ref(false);
const showStoryModal = ref(false);
const showDeleteConfirm = ref(false);
const showSuccessPopup = ref(false);
const showFailurePopup = ref(false);
const selectedStory = ref(null);
const story = ref([]);
const storyIdToDelete = ref(null);
const isImage = (filePath) => /\.(jpeg|jpg|png)$/i.test(filePath);

const currentUser = ref({
  id: null,
  name: 'Current User',
  profile_photo_path: null,
});

const props = defineProps({
  stories: {
    type: Array,
    required: true
  }
});

// Computed property to sort stories by publish_date or created_at (newest first)
const sortedStories = computed(() => {
  return [...props.stories].sort((a, b) => {
    const dateA = new Date(a.publish_date || a.created_at);
    const dateB = new Date(b.publish_date || b.created_at);
    return dateB - dateA; // Newest first
  });
});

onMounted(async () => {
  try {
    const usersResult = await apiClient.get('/api/users');
    users.value = usersResult.data.data;
    console.log('Users:', users.value);

    const storiesResult = await apiClient.get('/api/stories');
    story.value = storiesResult.data;
    console.log('Stories:', story.value);

    const userResult = await apiClient.get('/api/user');
    currentUser.value = userResult.data;
    console.log('Current User:', currentUser.value);
  } catch (error) {
    console.error('Error during onMounted:', error);
  }
});

const viewStory = (story) => {
  getStoryImagePath(story.file_path);
  selectedStory.value = story;
  showStoryModal.value = true;
};

const isStoryFromUser = (story) => story?.id_user === currentUser.value.id;

const getProfilePhotoById = (id) => {
  const position = users.value.findIndex(user => user.id === id);
  const user = users.value[position];
  return user && user.profile_photo_path ? user.profile_photo_path : user?.profile_photo_url || 'https://via.placeholder.com/150';
};

const getUserNameById = (id) => {
  const position = users.value.findIndex(user => user.id === id);
  const user = users.value[position];
  if (!user || !user.name) return 'UN';

  const name = user.name.trim();
  const words = name.split(' ').filter(word => word.length > 0);
  if (words.length > 1) {
    // Take first letter of first two words
    return `${words[0][0]}.${words[1][0]}.`.toUpperCase();
  } else {
    // Take first two letters of single word (or first letter if name is too short)
    return name.length > 1 ? name.substring(0, 2).toUpperCase() : name.substring(0, 1).toUpperCase();
  }
};

const getStoryImagePath = (path) => {
  if (!path) return 'https://via.placeholder.com/150';
  return `http://localhost:8000/storage/${path}`;
};

const fetchStories = async () => {
  try {
    const response = await apiClient.get('/api/stories');
    story.value = response.data;
    console.log('Stories fetched:', story.value);
  } catch (error) {
    console.error('Error fetching stories:', error);
  }
};

const deleteStory = async (id) => {
  try {
    await apiClient.delete(`/api/stories/${id}`);
    showSuccessPopup.value = true;
    await fetchStories();
    showStoryModal.value = false;
    setTimeout(() => {
      showSuccessPopup.value = false;
      window.location.reload();
    }, 500);
  } catch (error) {
    console.error('Error deleting story:', error);
    showFailurePopup.value = true;
  }
};

const deleteStoryConfirmed = async () => {
  try {
    await apiClient.delete(`/api/stories/${storyIdToDelete.value}`);
    showStoryModal.value = false;
    showDeleteConfirm.value = false;
    showSuccessPopup.value = true;
    await fetchStories();
    setTimeout(() => {
      showSuccessPopup.value = false;
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error('Error deleting story:', error);
    showDeleteConfirm.value = false;
    showFailurePopup.value = true;
  }
};

const openCreateStoryModal = () => {
  showCreateStoryModal.value = true;
};

const submitNewStory = async () => {
  if (!newStoryFile.value && !newStoryDescription.value) {
    alert('Please upload a file or add a description');
    return;
  }

  const formData = new FormData();
  formData.append('description', newStoryDescription.value);
  formData.append('id_user', currentUser.value.id);
  formData.append('publish_date', new Date().toISOString());
  if (newStoryFile.value) formData.append('file', newStoryFile.value);

  try {
    await apiClient.post('/api/stories', formData);
    showCreateStoryModal.value = false;
    await fetchStories();
  } catch (error) {
    console.error('Error creating story:', error);
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

.story-name {
  margin: 5px 0 0;
  font-size: 12px;
  text-align: center;
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.story-modal-image {
  width: 100%;
  height: auto;
  border-radius: 10px;
  margin-bottom: 10px;
}

.story-modal-video {
  width: 100%;
  height: auto;
  border-radius: 10px;
  margin-bottom: 10px;
}

.headline {
  font-size: 20px;
  font-weight: bold;
}

.headline.success {
  color: #4caf50;
}

.headline.error {
  color: #f44336;
}
</style>
