<template>
  <div class="home-container">
    <Navbar />
    <h1>Home</h1>
    <StoriesBar :stories="stories.data"/> <!-- Añade el componente StoriesBar -->
 
    <div class="post-card" v-for="post in posts.data" :key="post.id">
      <div class="post-header">
        <img :src="getProfilePhotoById(post.id_user)" class="profile-pic" alt="Profile" />
        <div class="post-info">
          <ul>
            <li>
              <strong>{{ getUserNameById(post.id_user) }}</strong>
              <h5>{{ post.description }}</h5>
              <img :src="getImageUrl(post.file_path)" alt="file Image" class="post-content" />
            </li>
          </ul>
        </div>
        <div class="post-menu">
          <button @click="togglePostMenu(post.id)">
            <i class="mdi mdi-dots-vertical"></i>
          </button>
          <div v-if="postMenuVisible === post.id" class="dropdown-menu">
            <ul>
              <li v-show="isPostFromUser(post)" @click="editPost(post)">Edit</li>
              <li v-show="isPostFromUser(post)" @click="deletePost(post.id)" :disabled="isDeleting">Delete</li>
              <li @click="reportPost(post)">Report</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="post-actions">
        <div class="action-item" @click="toggleLike(post)">
          <i class="mdi" :class="post.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
          <span>{{ post.likes_count }} Likes</span>
        </div>
        <div class="action-item" @click="openCommentModal(post)">
          <i class="mdi mdi-comment-outline"></i>
          <span>{{ post.comments ? post.comments.length : 0 }} Comments</span>
        </div>
        <div class="action-item" @click="sharePost">
          <i class="mdi mdi-share-outline"></i>
          <span>{{ shares }} Shares</span>
        </div>
      </div>
    </div>

    <CommentModal :visible="isCommentModalVisible" :comments="selectedPostComments" @close="closeCommentModal"
      @add-comment="addComment" :post="selectedPost" />

    <UserRecommendation />
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import UserRecommendation from '@/components/UserRecommendation.vue';
import CommentModal from '@/components/CommentModal.vue';
import StoriesBar from '@/components/StoriesBar.vue'; // Importa el componente StoriesBar
import { ref } from 'vue';
import { onMounted } from 'vue';
import axios from 'axios';

const posts = ref([]);
const users = ref({});
const isCommentModalVisible = ref(false);
const selectedPostComments = ref([]);
const selectedPostId = ref(null);
const selectedPost = ref(null);

const currentUser = ref({
  name: 'Current User', // Replace with actual user data
  profile_photo_path: 'https://via.placeholder.com/50' // Replace with actual user profile photo URL
});

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');

    if (!token) {
      console.error('No hay token disponible');
      return;
    }

    const result = await axios.get('http://localhost:8000/api/posts', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });
    posts.value = result.data;

    const usersResult = await axios.get('http://localhost:8000/api/users', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });

    if (!usersResult.data || !usersResult.data.data) {
      console.error('Estructura inesperada en la respuesta de usuarios:', usersResult.data);
      return;
    }

    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = {
        name: user.name,
        profile_photo_path: user.profile_photo_path
      };
      return acc;
    }, {});

    console.log('Usuarios:', users.value);

    // Fetch current user data
    const userResult = await axios.get('http://localhost:8000/api/user', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });
    currentUser.value = userResult.data;

    // Fetch stories data
    const storiesResult = await axios.get('http://localhost:8000/api/stories', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });
    stories.value = storiesResult.data;

  } catch (error) {
    console.error('Error al obtener datos', error);
  }
});

const getImageUrl = (path) => {
  if (!path) return 'https://via.placeholder.com/150';
  return `http://localhost:8000/storage/${path}`;
};

const getProfilePhotoById = (id) => {
  const user = users.value[id];
  return user && user.profile_photo_path ? user.profile_photo_path : 'https://via.placeholder.com/50';
};

const getUserNameById = (id) => {
  const user = users.value[id];
  return user && user.name ? user.name : 'usuario desconocido';
};

const toggleLike = async (post) => {
  console.log('Toggling like for post:', post);
  const token = localStorage.getItem('token');
  if (!token) {
    console.error('No token available');
    return;
  }

  const url = `http://localhost:8000/api/posts/${post.id}/like`;
  const method = post.liked_by_user ? 'delete' : 'post';

  try {
    await axios({
      method,
      url,
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    post.liked_by_user = !post.liked_by_user;
    post.likes_count += post.liked_by_user ? 1 : -1;
  } catch (error) {
    console.error('Error toggling like for post:', error.response?.data || error.message);
  }
};

const openCommentModal = async (post) => {
  selectedPost.value = post;
  selectedPostId.value = post.id;
  isCommentModalVisible.value = true;

  try {
    const response = await axios.get(
      `http://localhost:8000/api/posts/${post.id}/comments`,
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    );
    selectedPostComments.value = response.data;
    isCommentModalVisible.value = true;
  } catch (error) {
    console.error('Error:', error.response.data);
  }
};

const closeCommentModal = () => {
  isCommentModalVisible.value = false;
};

const addComment = async (comment) => {
  try {
    const response = await axios.post(`http://localhost:8000/api/posts/${selectedPostId.value}/comments`, {
      content: comment,
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    selectedPostComments.value.push(response.data);
  } catch (error) {
    console.error('Error adding comment:', error.response?.data || error.message);
  }
};

const togglePostMenu = (postId) => {
  postMenuVisible.value = postMenuVisible.value === postId ? null : postId;
};

const editPost = async (post) => {
  // Implement edit post logic

  location.reload();

};

const isPostFromUser = (post) => {
  return post.id_user === currentUser.value.id;
};

const deletePost = async (postId) => {
  isDeleting.value = true;
  try {
    const response = await axios.delete(`http://localhost:8000/api/posts/${postId}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    if (Array.isArray(posts.value)) {
      posts.value = posts.value.filter(post => post.id !== postId);
    }

    location.reload();

  } catch (error) {
    console.error('Error deleting post:', error.response?.data || error.message);
  } finally {
    isDeleting.value = false;
  }
};

const reportPost = (post) => {
  // Implement report post logic
  alert(`Reported post with ID: ${post.id}`);
};

const sharePost = (post) => {
  // Implement share post logic
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
  border-radius: 24px;
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
  margin-bottom: 20px;
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
  border-radius: 20px;
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

.post-content {
  width: 100%;
  border-radius: 20px;
  margin-bottom: 15px;
}
</style>
