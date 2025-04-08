<template>
  <div class="stories-bar">
    <!-- Botó per afegir històries -->
    <div class="story-item add-story" @click="showCreateStoryModal = true">
      <div class="add-story-icon">+</div>
      <p>Add Story</p>
    </div>

    <!-- Històries existents -->
    <div class="story-item" v-for="(story, index) in stories" :key="index" @click="viewStory(story)">
      <img :src="getProfilePhotoById(story.id_user)" alt="Story" class="story-image" />
      <p>{{ getUserNameById(story.id_user) }}</p>
    </div>

    <CreateStoryImage v-model="showCreateStoryModal" @storyCreated="fetchStories" />

    <!-- Modal para mostrar la historia -->
    <v-dialog v-model="showStoryModal" max-width="500">
      <v-card>
        <v-card-title>{{ getUserNameById(selectedStory?.id_user) }}</v-card-title>
        <v-card-text>
          <img :src="getStoryImagePath(selectedStory.file_path)" alt="Story Image" class="story-modal-image" />
          <p>{{ selectedStory?.description }}</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="showStoryModal = false">Close</v-btn>
          <v-btn color="red" text @click="deleteStory(selectedStory.id)">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

import CreateStoryImage from './CreateStoryImage.vue';


const users = ref([]);
const showCreateStoryModal = ref(false);
const showStoryModal = ref(false);
const showDeleteConfirm = ref(false); // For delete confirmation dialog
const showSuccessPopup = ref(false); // For success message popup
const showFailurePopup = ref(false); // For failure message popup
const selectedStory = ref(null);
const story = ref([]);
const storyIdToDelete = ref(null); // Store the ID of the story to delete

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

onMounted(async () => {


const token = localStorage.getItem('token');
const usersResult = await axios.get('http://localhost:8000/api/users', {
  headers: {
    Authorization: `Bearer ${token}`,
    Accept: 'application/json'
  }
});
users.value = usersResult.data.data;
console.log(users.value);

const storiesResult = await axios.get('http://localhost:8000/api/stories', {
  headers: {
    Authorization: `Bearer ${token}`,
    Accept: 'application/json'
  }
});
story.value = storiesResult.data;


  const userResult = await axios.get('http://localhost:8000/api/user', {
    headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
  });
  currentUser.value = userResult.data;
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

  return user && user.profile_photo_path ? user.profile_photo_path : user.profile_photo_url;

};

const getUserNameById = (id) => {
  const position = users.value.findIndex(user => user.id === id);
  const user = users.value[position];
  return user && user.name ? user.name : 'usuario desconocido';
};

const getStoryImagePath = (path) => {
  if (!path) return 'https://via.placeholder.com/150';
  return `http://localhost:8000/storage/${path}`;
};

const fetchStories = async () => {
  const response = await axios.get('http://localhost:8000/api/stories', {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  });
  story.value = response.data;
};

const deleteStory = async (id) => {
  try {
    await axios.delete(`http://localhost:8000/api/stories/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    //showStoryModal.value = false;
    showSuccessPopup.value = true;
    await fetchStories();

    setTimeout(() => {
      window.location.reload();
    }, 500);

  } catch (error) {
    console.error('Error deleting story:', error);
    showFailurePopup.value = true;
  }
}

const deleteStoryConfirmed = async () => {
  try {
    await axios.delete(`http://localhost:8000/api/stories/${storyIdToDelete.value}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    showStoryModal.value = false; // Close the story modal
    showDeleteConfirm.value = false; // Close the confirmation dialog
    showSuccessPopup.value = true; // Show success popup
    await fetchStories(); // Refresh the stories list
    setTimeout(() => {
      window.location.reload(); // Reload the page after a short delay
    }, 1000); // Delay to allow the user to see the success message
  } catch (error) {
    console.error('Error deleting story:', error);
    showDeleteConfirm.value = false; // Close the confirmation dialog
    showFailurePopup.value = true; // Show failure popup
  }
};

const openCreateStoryModal = () => {
  showCreateStoryModal.value = true;
};

const submitNewStory = async () => {
  if (!newStoryFile.value && !newStoryDescription.value) {
    alert('Please upload a file or add a description')
    return;
  }

  const formData = new FormData();
  formData.append('description', newStoryDescription.value);
  formData.append('id_user', currentUser.value.id);
  formData.append('publish_date', new Date().toISOString());
  if (newStoryFile.value) formData.append('file', newStoryFile.value);

  try {
    await axios.post('/api/stories', formData, {

      headers: { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'multipart/form-data' },

    });
    showCreateStoryModal.value = false;
    await fetchStories();
  } catch (error) {
    console.error('Error creating story:', error);

  }
}

</script>

<style scoped>
.stories-bar {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding: 10px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.story-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
}

.story-image {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid #007bff;
}

.story-item p {
  margin: 5px 0 0;
  font-size: 12px;
  text-align: center;
}

.story-modal-image {
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
  color: #4caf50; /* Green for success */
}

.headline.error {
  color: #f44336; /* Red for error */
}
</style>
