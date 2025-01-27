<template>
  <div class="home-container">
    <Navbar />
    <h1>Home</h1>
    <div class="stories">
      <div class="story" v-for="(story, index) in stories" :key="index">
        <img :src="story.image" :alt="story.name" />
        <p>{{ story.name }}</p>
      </div>
    </div>

    <div class="post-card">
      <div class="post-header">
        <img class="profile-pic" src="https://" alt="Profile" />
        <div class="post-info">
          <h3>Radostin Ivanov</h3>
          <p>IES EBRE</p>
        </div>
        <div class="post-menu">
          <button @click="toggleMenu">
            <i class="mdi mdi-dots-vertical"></i>
          </button>
          <div v-if="menuVisible" class="dropdown-menu">
            <ul>
              <li @click="reportPost">Report</li>
              <li @click="editPost">Edit</li>
              <li @click="deletePost">Delete</li>
            </ul>
          </div>
        </div>
      </div>

      <img
        class="post-image"
        src="https://plus.unsplash.com/premium_photo-1672115680958-54438df0ab82?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8bW91bnRhaW5zfGVufDB8fDB8fHww"
        alt="Post"
      />

      <div class="post-actions">
        <div class="action-item" @click="likePost">
          <i class="mdi mdi-thumb-up-outline"></i>
          <span>{{ likes }} Likes</span>
        </div>
        <div class="action-item" @click="commentOnPost">
          <i class="mdi mdi-comment-outline"></i>
          <span>{{ comments }} Comments</span>
        </div>
        <div class="action-item" @click="sharePost">
          <i class="mdi mdi-share-outline"></i>
          <span>{{ shares }} Shares</span>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import { ref } from 'vue';

const stories = [
  { name: 'My Story', image: '' },
  { name: 'mendez', image: 'https://via.placeholder.com/50' },
  {name: 'harrywhite', image: 'https://via.placeholder.com/50'},
  {name: 'garadm', image: 'https://via.placeholder.com/50'},
];

const likes = ref(0);
const comments = ref(0);
const shares = ref(0);
const menuVisible = ref(false);

const likePost = () => {
  likes.value++;
};

const commentOnPost = () => {
  comments.value++;
};

const sharePost = () => {
  shares.value++;
};

const toggleMenu = () => {
  menuVisible.value = !menuVisible.value;
};

const reportPost = () => {
  alert('Post reported');
};

const editPost = () => {
  alert('Edit post');
};

const deletePost = () => {
  alert('Post deleted');
};
</script>

<style scoped>
.home-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  background-color: white;
  min-height: 100vh;
  color: black;
}

h1 {
  font-size: 24px;
  padding-bottom: 20px;
}

.stories {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.story {
  text-align: center;
}

.story img {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  border: 2px solid #7f7f7f;
}

.story p {
  margin-top: 5px;
  font-size: 12px;
}

.post-card {
  background: #f0f0f0;
  border: 1px solid #d3d3d3;
  border-radius: 10px;
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
  width: 100%;
}

.post-header {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 15px;
  position: relative;
}

.profile-pic {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.post-info h3 {
  font-size: 16px;
  margin: 0;
}

.post-info p {
  font-size: 12px;
  margin: 0;
}

.post-menu {
  margin-left: auto;
  position: relative;
}

.post-menu button {
  background: none;
  border: none;
  cursor: pointer;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown-menu li {
  padding: 10px;
  cursor: pointer;
}

.dropdown-menu li:hover {
  background: #f0f0f0;
}

.post-image {
  width: 100%;
  border-radius: 10px;
  margin-bottom: 15px;
}

.post-actions {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}

.action-item {
  display: flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
}

.action-item i {
  font-size: 18px;
  color: #333;
}

.action-item span {
  font-size: 14px;
}
</style>
