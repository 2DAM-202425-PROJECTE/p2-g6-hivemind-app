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
            <div class="post-profile-link" @click="goToUserProfile(getUsernameById(selectedPost.id_user))">
              <img :src="getProfilePhotoById(selectedPost.id_user)" class="profile-pic" alt="Profile" />
            </div>
            <div class="post-info">
              <strong class="post-username" @click="goToUserProfile(getUsernameById(selectedPost.id_user))">
                {{ getUserNameById(selectedPost.id_user) }}
              </strong>
              <p class="post-date">{{ formatDate(selectedPost.created_at) }}</p>
              <div class="post-description" v-html="renderPostDescription(selectedPost.description)"></div>
              <div v-if="selectedPost.location" class="post-location">
                <i class="mdi mdi-earth location-icon"></i>
                <a :href="`https://www.google.com/maps?q=${encodeURIComponent(selectedPost.location)}`" target="_blank" class="location-link">
                  {{ simplifyLocation(selectedPost.location) }}
                </a>
              </div>
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
                  <template v-if="isPostFromUser(selectedPost)">
                    <li @click.stop="editPost(selectedPost)">Edit</li>
                    <li @click.stop="openDeletePostDialog(selectedPost.id)" :disabled="isDeleting">Delete</li>
                  </template>
                  <template v-else>
                    <li @click.stop="reportPost(selectedPost)">Report</li>
                  </template>
                </ul>
              </div>
            </div>
          </div>

          <!-- Edit Post Dialog -->
          <v-dialog v-model="editPostPopup" max-width="500">
            <v-card>
              <v-card-title>Edit Post</v-card-title>
              <v-card-text>
                <div v-if="selectedPost && selectedPost.file_path" class="current-image">
                  <p>Current File:</p>
                  <img v-if="!selectedPost.file_path.includes('.mp4')" :src="getImageUrl(selectedPost.file_path)"
                       alt="Current post image" style="max-width: 100%; max-height: 200px; margin-bottom: 10px;" />
                  <video v-else :src="getImageUrl(selectedPost.file_path)" controls
                         style="max-width: 100%; max-height: 200px; margin-bottom: 10px;"></video>
                </div>
                <v-file-input label="Replace Image/Video (.png, .jpg, .jpeg, .mp4)" accept=".png, .jpg, .jpeg, .mp4"
                              @update:modelValue="handleEditFileUpload" outlined></v-file-input>
                <v-text-field v-model="editPostDescription" label="Description" outlined></v-text-field>
                <div v-if="editPostLocation" class="location-preview">
                  <i class="mdi mdi-map-marker"></i>
                  <a
                    :href="`https://www.google.com/maps?q=${editPostLocation.lat},${editPostLocation.lon}`"
                    target="_blank"
                    class="location-btn"
                  >
                    {{ editPostLocation.name }}
                  </a>
                  <button class="remove-btn" @click="removeEditLocation">Remove</button>
                </div>
                <button v-else class="action-btn" @click="getEditLocation" title="Add Location">
                  <i class="mdi mdi-map-marker-outline"></i> Add Location
                </button>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text @click="cancelEditPost">Cancel</v-btn>
                <v-btn color="primary" @click="saveEditPost">Update Post</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- Delete Confirmation Dialog -->
          <v-dialog v-model="deleteDialog" max-width="400">
            <v-card>
              <v-card-title class="headline">Confirm Deletion</v-card-title>
              <v-card-text>
                Are you sure you want to delete this {{ deleteType === 'post' ? 'post' : 'comment' }}? This action cannot be undone.
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text @click="cancelDelete">Cancel</v-btn>
                <v-btn color="error" @click="confirmDelete">Delete</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

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
            <div class="comment-input">
              <input v-model="newComment" placeholder="Add a comment..." @keyup.enter="addComment" />
              <button @click="addComment">Post</button>
            </div>
            <div v-if="comments.length === 0" class="no-comments">
              <p>No comments yet</p>
            </div>
            <div v-else class="comments-list">
              <div v-for="comment in comments" :key="comment.id" class="comment">
                <div class="comment-profile-link" @click="goToUserProfile(comment.user?.username)">
                  <img :src="getCommentUserPhoto(comment.user)" class="comment-profile-pic" alt="User Profile" />
                </div>
                <div class="comment-content">
                  <strong class="comment-username" @click="goToUserProfile(comment.user?.username)">
                    {{ comment.user?.name || 'Unknown User' }}
                  </strong>
                  <p>{{ comment.content }}</p>
                  <button v-if="isPostFromUser(selectedPost)" @click="openDeleteCommentDialog(comment.id)" class="delete-comment-btn">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </div>
              </div>
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
import { generateAvatar } from '@/utils/avatar';

const route = useRoute();
const router = useRouter();
const username = route.params.username;
const user = ref({});
const selectedPost = ref(null);
const comments = ref([]);
const newComment = ref('');
const isDeleting = ref(false);
const postMenuVisible = ref(null);
const editPostPopup = ref(false);
const editPostDescription = ref('');
const editPostLocation = ref(null); // For editing location
const editPostFile = ref(null);
const currentUser = ref({});
const users = ref({});
const shares = ref(0);
const deleteDialog = ref(false);
const deleteType = ref(''); // 'post' or 'comment'
const itemToDelete = ref(null); // Stores postId or commentId
const previousRoute = ref(''); // To store the previous route

const API_BASE_URL = 'http://localhost:8000';

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token found, redirecting to login');
      router.push('/login');
      return;
    }

    // Store the previous route from document.referrer or fallback to home
    previousRoute.value = document.referrer.includes(window.location.origin)
      ? new URL(document.referrer).pathname
      : '/'; // Default to home if referrer is unreliable

    const usersResult = await axios.get(`${API_BASE_URL}/api/users`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = {
        name: user.name,
        username: user.username,
        profile_photo_path: user.profile_photo_path,
      };
      return acc;
    }, {});

    console.log('Fetching user data for username:', username);
    const userResult = await axios.get(`${API_BASE_URL}/api/user/${username}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('User data:', userResult.data);
    user.value = userResult.data.user;

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
    selectedPost.value = postResult.data.data;

    console.log('Fetching comments for post ID:', postId);
    const commentsResult = await axios.get(`${API_BASE_URL}/api/posts/${postId}/comments`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Comments data:', commentsResult.data);
    comments.value = commentsResult.data || [];

    console.log('Fetching current user');
    const currentUserResult = await axios.get(`${API_BASE_URL}/api/user`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('Current user data:', currentUserResult.data);
    currentUser.value = currentUserResult.data;

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

const renderPostDescription = (description) => {
  if (!description) return 'No description';

  let html = description;
  html = html.replace(/\n/g, '<br>');
  return html;
};

const simplifyLocation = (location) => {
  if (!location) return '';
  const parts = location.split(',').map(part => part.trim());
  const country = parts[parts.length - 1];
  let city = parts[0];
  if (parts.length >= 3) {
    city = parts[parts.length - 3] || parts[parts.length - 2];
  }
  return `${city}, ${country}`;
};

const getImageUrl = (path) => path ? `${API_BASE_URL}/storage/${path}` : generateAvatar('User');
const getProfilePhotoById = (id) => {
  const user = users.value[id];
  return user?.profile_photo_path ? `${API_BASE_URL}/storage/${user.profile_photo_path}` : generateAvatar(user?.name || 'User');
};
const getCommentUserPhoto = (user) => user?.profile_photo_path ? `${API_BASE_URL}/storage/${user.profile_photo_path}` : 'https://via.placeholder.com/40';
const getUserNameById = (id) => users.value[id]?.name || 'Unknown User';
const getUsernameById = (id) => users.value[id]?.username || null;
const isPostFromUser = (post) => post.id_user === currentUser.value.id;

const goToUserProfile = (username) => {
  console.log('Navigating to profile with username:', username);
  if (username) {
    router.push(`/profile/${username}`);
  } else {
    console.warn('No username available for profile navigation');
  }
};

const toggleLike = async (post) => {
  try {
    const token = localStorage.getItem('token');
    if (post.liked_by_user) {
      await axios.delete(`${API_BASE_URL}/api/posts/${post.id}/like`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      post.liked_by_user = false;
      post.likes_count -= 1;
    } else {
      await axios.post(`${API_BASE_URL}/api/posts/${post.id}/like`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      post.liked_by_user = true;
      post.likes_count += 1;
    }
  } catch (error) {
    console.error('Error toggling like:', error.response?.data || error.message);
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

const openDeleteCommentDialog = (commentId) => {
  deleteType.value = 'comment';
  itemToDelete.value = commentId;
  deleteDialog.value = true;
};

const openDeletePostDialog = (postId) => {
  deleteType.value = 'post';
  itemToDelete.value = postId;
  deleteDialog.value = true;
};

const cancelDelete = () => {
  deleteDialog.value = false;
  itemToDelete.value = null;
  deleteType.value = '';
};

const confirmDelete = async () => {
  const token = localStorage.getItem('token');
  try {
    if (deleteType.value === 'post') {
      isDeleting.value = true;
      await axios.delete(`${API_BASE_URL}/api/posts/${itemToDelete.value}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      selectedPost.value = null;
      router.push(previousRoute.value || '/');
    } else if (deleteType.value === 'comment') {
      await axios.delete(`${API_BASE_URL}/api/comments/${itemToDelete.value}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      comments.value = comments.value.filter(comment => comment.id !== itemToDelete.value);
    }
  } catch (error) {
    console.error(`Error deleting ${deleteType.value}:`, error.response?.data || error.message);
  } finally {
    isDeleting.value = false;
    deleteDialog.value = false;
    itemToDelete.value = null;
    deleteType.value = '';
  }
};

const togglePostMenu = (postId) => {
  postMenuVisible.value = postMenuVisible.value === postId ? null : postId;
};

const editPost = (post) => {
  editPostDescription.value = post.description || '';
  editPostLocation.value = post.location ? { name: post.location, lat: null, lon: null } : null;
  editPostFile.value = null;
  editPostPopup.value = true;
};

const getEditLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const { latitude, longitude } = position.coords;
        try {
          const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`);
          const data = await response.json();
          editPostLocation.value = {
            name: data.display_name || `Lat: ${latitude}, Lon: ${longitude}`,
            lat: latitude,
            lon: longitude
          };
        } catch (error) {
          console.error('Error fetching location name:', error);
          editPostLocation.value = {
            name: `Lat: ${latitude}, Lon: ${longitude}`,
            lat: latitude,
            lon: longitude
          };
        }
      },
      (error) => {
        console.error('Error getting location:', error);
        alert('Unable to get location. Please allow location access.');
      }
    );
  } else {
    alert('Geolocation is not supported by your browser.');
  }
};

const removeEditLocation = () => {
  editPostLocation.value = null;
};

const handleEditFileUpload = (files) => {
  editPostFile.value = files ? files[0] : null;
};

const cancelEditPost = () => {
  editPostPopup.value = false;
  editPostDescription.value = '';
  editPostLocation.value = null;
  editPostFile.value = null;
};

const saveEditPost = async () => {
  if (!selectedPost.value) return;

  try {
    const token = localStorage.getItem('token');
    const formData = new FormData();
    formData.append('description', editPostDescription.value);
    formData.append('location', editPostLocation.value ? editPostLocation.value.name : '');
    formData.append('_method', 'PUT');
    if (editPostFile.value) {
      formData.append('file', editPostFile.value);
    }

    const response = await axios.post(
      `${API_BASE_URL}/api/posts/${selectedPost.value.id}`,
      formData,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        }
      }
    );

    selectedPost.value = response.data.post || response.data.data;
    editPostPopup.value = false;
  } catch (error) {
    console.error('Error updating post:', error);
    alert('Error: ' + (error.response?.data?.message || error.message));
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

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
.user-posts-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  padding-bottom: 60px;
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

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
  position: relative;
}

.post-profile-link {
  cursor: pointer;
  flex-shrink: 0;
}

.profile-pic {
  width: 50px;
  height: 50px;
  min-width: 50px;
  min-height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.post-info {
  flex-grow: 1;
  margin-left: 10px;
}

.post-username {
  font-size: 16px;
  cursor: pointer;
}

.post-username:hover {
  text-decoration: underline;
}

.post-date {
  font-size: 12px;
  color: #666;
}

.post-description {
  font-size: 16px;
}

.post-location {
  margin-top: 10px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.location-icon {
  color: #1da1f2;
  font-size: 16px;
}

.location-link {
  color: #1da1f2;
  text-decoration: none;
  font-size: 14px;
}

.location-link:hover {
  text-decoration: underline;
}

.location-preview {
  margin-top: 10px;
  padding: 10px;
  background: #e6f3ff;
  border-radius: 5px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.location-preview i {
  color: #1da1f2;
}

.location-btn {
  display: inline-block;
  padding: 5px 10px;
  background-color: #1da1f2;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-size: 14px;
}

.location-btn:hover {
  background-color: #0d91e2;
}

.remove-btn {
  background: #ff4444;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 5px 10px;
  cursor: pointer;
  font-size: 14px;
}

.remove-btn:hover {
  background: #cc0000;
}

.action-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  color: #1da1f2;
  display: flex;
  align-items: center;
  gap: 5px;
}

.action-btn i {
  font-size: 20px;
}

.action-btn:hover {
  color: #0d91e2;
}

.post-menu {
  flex-shrink: 0;
  position: relative;
}

.post-menu button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 5px);
  right: 0;
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  min-width: 120px;
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
  margin-bottom: 15px;
}

.comments-list {
  /* No max-height or overflow-y for natural flow */
}

.comment {
  display: flex;
  align-items: flex-start;
  padding: 10px;
  border-bottom: 1px solid #eee;
  position: relative;
}

.comment-profile-link {
  cursor: pointer;
}

.comment-profile-pic {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}

.comment-content {
  flex: 1;
}

.comment-username {
  font-size: 14px;
  cursor: pointer;
}

.comment-username:hover {
  text-decoration: underline;
}

.comment-content p {
  margin: 5px 0 0;
  font-size: 14px;
}

.delete-comment-btn {
  background: none;
  border: none;
  color: #ff4444;
  cursor: pointer;
  padding: 0;
  position: absolute;
  right: 10px;
  top: 10px;
}

.delete-comment-btn i {
  font-size: 18px;
}

.delete-comment-btn:hover {
  color: #cc0000;
}

.no-comments {
  text-align: center;
  color: #666;
  padding: 10px 0;
}

.comment-input {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
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
