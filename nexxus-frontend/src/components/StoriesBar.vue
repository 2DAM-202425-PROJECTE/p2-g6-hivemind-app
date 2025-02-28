<template>
    <div class="stories-bar">
            <div class="story-item" v-for="story in stories" :key="index" @click="viewStory(story)">
            <img :src="getProfilePhotoById(story.id_user)" alt="Story" class="story-image" />
            <p>{{ getUserNameById(story.id_user) }}</p>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const users = ref([]);
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
})

const viewStory = (story) => {
    alert(`Viewing story: ${getUserNameById(story.id_user)}`);
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
</style>