<template>
  <div class="edit-profile-container">
    <Navbar />
    <h1>Edit Profile</h1>
    <form @submit.prevent="saveChanges">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" v-model="editableUser.name" />
      </div>

      <div class="form-group">
        <label for="profile-pic">Profile Picture</label>
        <input type="text" id="profile-pic" v-model="editableUser.profilePic" placeholder="Paste image URL here" />
      </div>

      <div class="form-group">
        <label for="level">Level</label>
        <input type="number" id="level" v-model="editableUser.level" />
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" v-model="editableUser.description"></textarea>
      </div>

      <div class="form-group">
        <label>Stories</label>
        <div v-for="(story, index) in editableUser.stories" :key="index" class="story-input">
          <input type="text" v-model="story.name" placeholder="Story name" />
          <input type="text" v-model="story.image" placeholder="Story image URL" />
          <button @click.prevent="removeStory(index)">Remove</button>
        </div>
        <button @click.prevent="addStory">Add Story</button>
      </div>

      <div class="form-group">
        <label for="posts">Number of Posts</label>
        <input type="number" id="posts" v-model="editableUser.posts" />
      </div>

      <div class="form-buttons">
        <button type="submit" class="save-btn">Save Changes</button>
        <button @click.prevent="cancelEdit" class="cancel-btn">Cancel</button>
      </div>
    </form>
    <Footer />
  </div>
</template>

<script setup>
import axios from '@/axios'
import { ref, onMounted } from 'vue'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Load user data from ProfilePage
const user = ref({
  profile_photo_path: '',
  name: '',
  level: 0,
  description: '',
  stories: [],
  posts: 0,
})

// Fetch user data from the server
const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
    editableUser.value = { ...user.value } 
  } catch (error) {
    console.error('Failed to fetch user data:', error)
  }
}

onMounted(() => {
  fetchUser()
})

// Clone the user object for editing
const editableUser = ref({ ...user.value })

// Save changes and redirect back to the Profile page
const saveChanges = () => {
  user.value = { ...editableUser.value } // Save changes to the user data
  alert('Profile updated successfully!')
  router.push('/profile') // Redirect to the Profile page
}

// Cancel editing and return to the Profile page without saving
const cancelEdit = () => {
  router.push('/profile')
}

// Add a new story
const addStory = () => {
  editableUser.value.stories.push({ name: '', image: '' })
}

// Remove a specific story by index
const removeStory = (index) => {
  editableUser.value.stories.splice(index, 1)
}
</script>

<style scoped>
.edit-profile-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: #f0f2f5;
  min-height: 100vh;
}

h1 {
  font-size: 24px;
  margin-bottom: 20px;
}

form {
  max-width: 600px;
  margin: 0 auto;
  background: #706e6e;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

input,
textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

textarea {
  resize: none;
}

.story-input {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

.story-input input {
  flex: 1;
}

button {
  cursor: pointer;
}

.save-btn {
  background-color: #7f7f7f;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  margin-right: 10px;
}

.cancel-btn {
  background-color: grey;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
}
</style>
