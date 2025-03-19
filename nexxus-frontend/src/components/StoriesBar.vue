<template>
    <div class="stories-bar">
        <div class="story-item" v-for="(story, index) in stories" :key="index" @click="viewStory(story)">
            <img :src="getProfilePhotoById(story.id_user)" alt="Story" class="story-image" />
            <p>{{ getUserNameById(story.id_user) }}</p>
        </div>
    </div>

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
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const users = ref([]);
const showStoryModal = ref(false);
const selectedStory = ref(null);
const story = ref([]);

const props = defineProps({
    stories: {
        type: Array,
        required: true
    }
})

onMounted(async () => {
    const token = localStorage.getItem('token');
    const usersResult = await axios.get('http://localhost:8000/api/users', {
        headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
        }
    });
    users.value = usersResult.data.data;

    // Fetch stories data
    const storiesResult = await axios.get('http://localhost:8000/api/stories', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });
    story.value = storiesResult.data;
})

const viewStory = (story) => {
    // Mostrar imagen de la historia

    getStoryImagePath(story.file_path);
    

    selectedStory.value = story;
    showStoryModal.value = true;

}

const getProfilePhotoById = (id) => {
  const position = users.value.findIndex(user => user.id === id);
  const user = users.value[position];
  return user && user.profile_photo_path ? user.profile_photo_path : 'https://via.placeholder.com/50';
};

const getUserNameById = (id) => {
  const position = users.value.findIndex(user => user.id === id);
  const user = users.value[position];
  return user && user.name ? user.name : 'usuario desconocido';
};

const getStoryImagePath = (path) => {
    if (!path) return 'https://via.placeholder.com/150';
    console.log(`http://localhost:8000/storage/${path}`);
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
    if (confirm('Are you sure you want to delete this story?')) {
        try {
            await axios.delete(`http://localhost:8000/api/stories/${id}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`,
                },
            });
            alert('Story deleted successfully');
            showStoryModal.value = false; // Tanca el popup de la història
            fetchStories(); // Actualiza la llista de les històries
            window.location.reload(); // Recarrega la pàgina
        } catch (error) {
            console.error(error);
            alert('Failed to delete story');
        }
    }
};
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
</style>