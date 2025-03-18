<template>
  <div class="user-posts-container">
    <Navbar />
    <h1>{{ user.name || 'User' }}'s Posts</h1>
    <div class="posts-and-comments">
      <div class="posts-column">
        <div v-if="userPosts.length === 0" class="no-posts">
          <p>No posts available</p>
        </div>
        <div v-else v-for="post in userPosts" :key="post.id" :id="`post-${post.id}`" class="post-card">
          <div class="post-header">
            <img :src="getProfilePhotoByUsername(post.username)" class="profile-pic" alt="Profile" />
            <div class="post-info">
              <strong>{{ getUserNameByUsername(post.username) }}</strong>
              <h5>{{ post.description || 'No description' }}</h5>
              <template v-if="post.file_path && post.file_path.includes('.mp4')">
                <video :src="getImageUrl(post.file_path)" alt="Post Video" class="post-content" controls />
              </template>
              <template v-else-if="post.file_path">
                <img :src="getImageUrl(post.file_path)" alt="Post Image" class="post-content" />
              </template>
              <template v-else>
                <p>No media available</p>
              </template>
            </div>
            <div class="post-menu">
              <button @click.stop="togglePostMenu(post.id)">
                <i class="mdi mdi-dots-vertical"></i>
              </button>
              <div v-if="postMenuVisible === post.id" class="dropdown-menu">
                <ul>
                  <li v-show="isPostFromUser(post)" @click.stop="editPost(post)">Edit</li>
                  <li v-show="isPostFromUser(post)" @click.stop="deletePost(post.id)" :disabled="isDeleting">Delete</li>
                  <li @click.stop="reportPost(post)">Report</li>
                </ul>
              </div>
            </div>
          </div>
          <div class="post-actions">
            <div class="action-item" @click.stop="toggleLike(post)">
              <i class="mdi" :class="post.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
              <span>{{ post.likes_count || 0 }} Likes</span>
            </div>
            <div class="action-item" @click.stop="openCommentModal(post)">
              <i class="mdi mdi-comment-outline"></i>
              <span>{{ post.comments ? post.comments.length : 0 }} Comments</span>
            </div>
            <div class="action-item" @click.stop="sharePost(post)">
              <i class="mdi mdi-share-outline"></i>
              <span>{{ shares }} Shares</span>
            </div>
          </div>
        </div>
      </div>
      <div class="comments-column" v-if="selectedPost">
        <CommentModal :visible="isCommentModalVisible" :comments="selectedPostComments" @close="closeCommentModal"
                      :currentUser="currentUser" @add-comment="addComment" :post="selectedPost" />
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
import CommentModal from '@/components/CommentModal.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const username = route.params.username;
const user = ref({});
const userPosts = ref([]);
const isCommentModalVisible = ref(false);
const selectedPostComments = ref([]);
const selectedPost = ref(null);
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

    // Fetch user data by username to get the user ID
    console.log('Fetching user data for username:', username);
    const userResult = await axios.get(`${API_BASE_URL}/api/user/${username}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('User data:', userResult.data);
    user.value = userResult.data;

    // Fetch posts using the user’s ID
    const userId = userResult.data.id;
    console.log('Fetching posts for user ID:', userId);
    const postsResult = await axios.get(`${API_BASE_URL}/api/users/${userId}/posts`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Posts data:', postsResult.data);
    userPosts.value = postsResult.data || [];

    // Fetch current authenticated user
    console.log('Fetching current user');
    const currentUserResult = await axios.get(`${API_BASE_URL}/api/user`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Current user data:', currentUserResult.data);
    currentUser.value = currentUserResult.data;

    // Scroll to specific post if postId is in query
    const postId = route.query.postId;
    if (postId) {
      await nextTick();
      const postElement = document.getElementById(`post-${postId}`);
      if (postElement) {
        postElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        postElement.classList.add('highlight');
        setTimeout(() => postElement.classList.remove('highlight'), 2000);
      } else {
        console.warn(`Post element with ID post-${postId} not found`);
      }
    }
  } catch (error) {
    console.error('Error fetching user posts:', error.response?.data || error.message);
    console.error('Failed request:', error.config);
    userPosts.value = [];
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
      await axios.delete(`${API_BASE_URL}/api/posts/${post.id}/like`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      post.liked_by_user = false;
      post.likes_count = (post.likes_count || 0) - 1;
    } else {
      await axios.post(`${API_BASE_URL}/api/posts/${post.id}/like`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      post.liked_by_user = true;
      post.likes_count = (post.likes_count || 0) + 1;
    }
  } catch (error) {
    console.error('Error toggling like:', error);
  }
};

const openCommentModal = async (post) => {
  try {
    const token = localStorage.getItem('token');
    // Use the new /api/posts/{id} endpoint to fetch detailed post data
    const response = await axios.get(`${API_BASE_URL}/api/posts/${post.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    const detailedPost = response.data.data; // Extract from 'data' key based on your response structure
    selectedPostComments.value = detailedPost.comments || [];
    selectedPost.value = detailedPost; // Update with full post data including likes and comments
    isCommentModalVisible.value = true;
  } catch (error) {
    console.error('Error fetching post details:', error.response?.data || error.message);
  }
};

const closeCommentModal = () => {
  isCommentModalVisible.value = false;
  selectedPostComments.value = [];
  selectedPost.value = null;
};

const addComment = async (comment) => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post(`${API_BASE_URL}/api/posts/${selectedPost.value.id}/comments`, {
      content: comment
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    selectedPostComments.value.push(response.data);
    selectedPost.value.comments = selectedPost.value.comments || [];
    selectedPost.value.comments.push(response.data);
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
      userPosts.value = userPosts.value.filter(post => post.id !== postId);
      postMenuVisible.value = null;
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
    await axios.post(`${API_BASE_URL}/api/posts/${post.id}/report`, { // Note: This route doesn’t exist yet
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

.comments-column {
  flex: 1;
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
</style>
