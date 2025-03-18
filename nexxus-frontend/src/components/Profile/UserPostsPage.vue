<template>
  <div class="user-posts-container">
    <Navbar />
    <h1>{{ user.name || 'User' }}'s Post</h1>
    <div class="posts-and-comments">
      <div class="posts-column">
        <div v-if="!selectedPost" class="no-posts">
          <p>No post available</p>
        </div>
        <div v-else :id="`post-${selectedPost.id}`" class="post-card">
          <div class="post-header">
            <img :src="getProfilePhotoByUsername(selectedPost.username)" class="profile-pic" alt="Profile" />
            <div class="post-info">
              <strong>{{ getUserNameByUsername(selectedPost.username) }}</strong>
              <h5>{{ selectedPost.description || 'No description' }}</h5>
              <template v-if="selectedPost.file_path && selectedPost.file_path.includes('.mp4')">
                <video :src="getImageUrl(selectedPost.file_path)" alt="Post Video" class="post-content" controls />
              </template>
              <template v-else-if="selectedPost.file_path">
                <img :src="getImageUrl(selectedPost.file_path)" alt="Post Image" class="post-content" />
              </template>
              <template v-else>
                <p>No media available</p>
              </template>
            </div>
            <div class="post-menu">
              <button @click.stop="togglePostMenu(selectedPost.id)">
                <i class="mdi mdi-dots-vertical"></i>
              </button>
              <div v-if="postMenuVisible === selectedPost.id" class="dropdown-menu">
                <ul>
                  <li v-show="isPostFromUser(selectedPost)" @click.stop="editPost(selectedPost)">Edit</li>
                  <li v-show="isPostFromUser(selectedPost)" @click.stop="deletePost(selectedPost.id)" :disabled="isDeleting">Delete</li>
                  <li @click.stop="reportPost(selectedPost)">Report</li>
                </ul>
              </div>
            </div>
          </div>
          <div class="post-actions">
            <div class="action-item" @click.stop="toggleLike(selectedPost)">
              <i class="mdi" :class="selectedPost.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
              <span>{{ selectedPost.likes_count }} Likes</span>
            </div>
            <div class="action-item" @click.stop="sharePost(selectedPost)">
              <i class="mdi mdi-share-outline"></i>
              <span>{{ shares }} Shares</span>
            </div>
          </div>
          <!-- Comment Section -->
          <div class="comments-section">
            <h3>Comments</h3>
            <div v-if="comments.length === 0" class="no-comments">
              <p>No comments yet</p>
            </div>
            <div v-else class="comments-list">
              <div v-for="comment in comments" :key="comment.id" class="comment">
                <strong>{{ comment.user?.name || 'Unknown User' }}</strong>
                <p>{{ comment.content }}</p>
              </div>
            </div>
            <div class="comment-input">
              <input v-model="newComment" placeholder="Add a comment..." @keyup.enter="addComment" />
              <button @click="addComment">Post</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const username = route.params.username;
const user = ref({});
const selectedPost = ref(null);
const comments = ref([]);
const newComment = ref('');
const isDeleting = ref(false);
const postMenuVisible = ref(null);
const currentUser = ref({});
const shares = ref(0);

const API_BASE_URL = 'http://localhost:8000';

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token found, redirecting to login');
      router.push('/login');
      return;
    }

    // Fetch user data by username
    console.log('Fetching user data for username:', username);
    const userResult = await axios.get(`${API_BASE_URL}/api/user/${username}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('User data:', userResult.data);
    user.value = userResult.data.user;

    // Fetch the specific post using postId from query
    const postId = route.query.postId;
    if (!postId) {
      console.error('No postId provided in query');
      return;
    }
    console.log('Fetching post with ID:', postId);
    const postResult = await axios.get(`${API_BASE_URL}/api/posts/${postId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Post data:', postResult.data);
    selectedPost.value = postResult.data.data; // Includes likes_count and liked_by_user

    // Fetch comments for the post
    console.log('Fetching comments for post ID:', postId);
    const commentsResult = await axios.get(`${API_BASE_URL}/api/posts/${postId}/comments`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Comments data:', commentsResult.data);
    comments.value = commentsResult.data || [];

    // Fetch current authenticated user
    console.log('Fetching current user');
    const currentUserResult = await axios.get(`${API_BASE_URL}/api/user`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Current user data:', currentUserResult.data);
    currentUser.value = currentUserResult.data;

    // Scroll to the post
    await nextTick();
    const postElement = document.getElementById(`post-${postId}`);
    if (postElement) {
      postElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      postElement.classList.add('highlight');
      setTimeout(() => postElement.classList.remove('highlight'), 2000);
    } else {
      console.warn(`Post element with ID post-${postId} not found`);
    }
  } catch (error) {
    console.error('Error fetching post or comments:', error.response?.data || error.message);
    console.error('Failed request:', error.config);
    selectedPost.value = null;
  }
});

const getImageUrl = (path) => `${API_BASE_URL}/storage/${path}`;
const getProfilePhotoByUsername = (username) => user.value.profile_photo_path || 'https://via.placeholder.com/50';
const getUserNameByUsername = (username) => user.value.name || 'Unknown User';
const isPostFromUser = (post) => post.username === currentUser.value.username;

const toggleLike = async (post) => {
  try {
    const token = localStorage.getItem('token');
    if (post.liked_by_user) {
      // Unlike the post
      await axios.delete(`${API_BASE_URL}/api/posts/${post.id}/like`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      post.liked_by_user = false;
      post.likes_count -= 1;
    } else {
      // Like the post
      const response = await axios.post(`${API_BASE_URL}/api/posts/${post.id}/like`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      post.liked_by_user = true;
      post.likes_count += 1;
    }
  } catch (error) {
    console.error('Error toggling like:', error.response?.data || error.message);
    // Optionally fetch the latest state if the action fails
    const postResult = await axios.get(`${API_BASE_URL}/api/posts/${post.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    post.liked_by_user = postResult.data.data.liked_by_user;
    post.likes_count = postResult.data.data.likes_count;
  }
};

const addComment = async () => {
  if (!newComment.value.trim()) return;
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post(`${API_BASE_URL}/api/posts/${selectedPost.value.id}/comments`, {
      content: newComment.value
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    comments.value.push(response.data);
    newComment.value = '';
  } catch (error) {
    console.error('Error adding comment:', error);
  }
};

const togglePostMenu = (postId) => {
  postMenuVisible.value = postMenuVisible.value === postId ? null : postId;
};

const editPost = async (post) => {
  router.push({ name: 'EditPost', params: { id: post.id } });
};

const deletePost = async (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    try {
      isDeleting.value = true;
      const token = localStorage.getItem('token');
      await axios.delete(`${API_BASE_URL}/api/posts/${postId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      selectedPost.value = null;
      router.push(`/profile/${username}`);
    } catch (error) {
      console.error('Error deleting post:', error);
    } finally {
      isDeleting.value = false;
    }
  }
};

const reportPost = async (post) => {
  try {
    const token = localStorage.getItem('token');
    await axios.post(`${API_BASE_URL}/api/posts/${post.id}/report`, {
      reason: prompt('Please enter reason for reporting:')
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    alert('Post reported successfully');
    postMenuVisible.value = null;
  } catch (error) {
    console.error('Error reporting post:', error);
  }
};

const sharePost = (post) => {
  const shareUrl = `${window.location.origin}/post/${post.id}`;
  navigator.clipboard.writeText(shareUrl)
    .then(() => {
      shares.value++;
      alert('Post URL copied to clipboard!');
    })
    .catch(err => console.error('Error copying URL:', err));
};
</script>

<style scoped>
.user-posts-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  background-color: #f0f2f5;
  min-height: 100vh;
  color: black;
}

h1 {
  font-size: 24px;
  padding-bottom: 20px;
}

.posts-and-comments {
  display: flex;
}

.posts-column {
  flex: 2;
  margin-right: 20px;
}

.post-card {
  background: #ffffff;
  border: 1px solid #ffffff;
  border-radius: 24px;
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
  margin-bottom: 20px;
  width: 100%;
}

.post-card.highlight {
  animation: highlight 2s ease-out;
}

@keyframes highlight {
  0% { background-color: #ffff99; }
  100% { background-color: #ffffff; }
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

.post-actions {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  margin-bottom: 20px;
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

.no-posts {
  text-align: center;
  color: #666;
  padding: 20px;
}

.comments-section {
  margin-top: 20px;
  padding: 15px;
  background: #f9f9f9;
  border-radius: 10px;
}

.comments-section h3 {
  font-size: 18px;
  margin-bottom: 10px;
}

.comments-list {
  max-height: 300px;
  overflow-y: auto;
}

.comment {
  padding: 10px;
  border-bottom: 1px solid #eee;
}

.comment strong {
  font-size: 14px;
}

.comment p {
  margin: 5px 0 0;
  font-size: 14px;
}

.no-comments {
  text-align: center;
  color: #666;
}

.comment-input {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.comment-input input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.comment-input button {
  padding: 10px 20px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.comment-input button:hover {
  background: #0056b3;
}
</style>
