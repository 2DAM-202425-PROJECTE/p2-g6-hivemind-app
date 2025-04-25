<template>
  <v-dialog v-model="showCreateStoryModal" max-width="500">
    <v-card>
      <v-card-title>Create a Story</v-card-title>
      <v-card-text>
        <v-file-input
          v-model="newStoryFile"
          label="Upload Image/Video (.png, .mp4)"
          accept=".png, .mp4"
          outlined
        ></v-file-input>
        <v-text-field v-model="newStoryDescription" label="Description" outlined></v-text-field>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="showCreateStoryModal = false">Cancel</v-btn>
        <v-btn color="primary" :disabled="isLoadingUser" @click="submitNewStory">Submit</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import apiClient from "@/axios.js";

const showCreateStoryModal = ref(false);
const newStoryFile = ref(null);
const newStoryDescription = ref('');
const currentUser = ref({});
const isLoadingUser = ref(true);


onMounted(() => {
  fetchUser();
});

const fetchUser = async () => {
  try {
    const response = await apiClient.get('/api/user');
    currentUser.value = response.data;
  } catch (error) {
    console.error('Error fetching user:', error);
  } finally {
    isLoadingUser.value = false; // Marcar como cargado
  }
};

const submitNewStory = async () => {
  if (!newStoryFile.value && !newStoryDescription.value) {
    alert('Please provide a file or description.');
    return;
  }

  const formData = new FormData();
  formData.append('description', newStoryDescription.value || '');
  formData.append('publish_date', new Date().toISOString());

  if (newStoryFile.value) {
    formData.append('file', newStoryFile.value);
  } else {
    alert('Please upload a valid file.');
    return;
  }

  // Verificar si currentUser està definit
  if (!currentUser.value || !currentUser.value.id) {
    alert('User is not authenticated. Please log in again.');
    console.error('currentUser is undefined or missing id.');
    console.log(currentUser.value);
    console.log(currentUser.value.id);
    return;
  }

  const userId = currentUser.value.id;
  formData.append('id_user', userId);

  try {
    await apiClient.post('/api/stories', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    showCreateStoryModal.value = false;
    newStoryFile.value = null;
    newStoryDescription.value = '';

    window.location.reload();
  } catch (error) {
    console.error('Error creating story:', error);
    alert('Failed to create story. Please try again.');
  }
};

onMounted(fetchUser);
</script>
