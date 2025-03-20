<template>
  <div class="user-videos-container">
    <Navbar />
    <h1>{{ user.name || 'User' }}'s Video</h1>
    <div class="videos-and-comments">
      <div class="videos-column">
        <div v-if="!selectedVideo" class="no-videos">
          <p>No video available</p>
        </div>
        <div v-else :id="`video-${selectedVideo.id}`" class="video-card">
          <div class="video-header">
            <div class="profile-section">
              <div class="video-profile-link" @click="goToUserProfile(getUsernameById(selectedVideo.id_user))">
                <img :src="getProfilePhotoById(selectedVideo.id_user)" class="profile-pic" alt="Profile" />
              </div>
              <div class="video-info">
                <strong class="video-username" @click="goToUserProfile(getUsernameById(selectedVideo.id_user))">
                  {{ getUserNameById(selectedVideo.id_user) }}
                </strong>
                <p class="post-date">{{ formatDate(selectedVideo.created_at) }}</p>
                <span class="username-handle">{{ getUsernameById(selectedVideo.id_user) }}</span>
              </div>
            </div>
            <div class="video-menu">
              <button @click.stop="toggleVideoMenu(selectedVideo.id)">
                <i class="mdi mdi-dots-vertical"></i>
              </button>
              <div v-if="videoMenuVisible === selectedVideo.id" class="dropdown-menu">
                <ul>
                  <template v-if="isVideoFromUser(selectedVideo)">
                    <li @click.stop="editVideo(selectedVideo)">Edit</li>
                    <li @click.stop="openDeleteVideoDialog(selectedVideo.id)" :disabled="isDeleting">Delete</li>
                  </template>
                  <template v-else>
                    <li @click.stop="reportVideo(selectedVideo)">Report</li>
                  </template>
                </ul>
              </div>
            </div>
          </div>
          <div class="video-description">
            <h5>{{ selectedVideo.description || 'No description' }}</h5>
          </div>
          <video :src="getImageUrl(selectedVideo.file_path)" alt="Post Video" class="video-content" controls />
          <div class="video-actions">
            <div class="action-item" @click.stop="toggleLike(selectedVideo)">
              <i class="mdi" :class="selectedVideo.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
              <span>{{ selectedVideo.likes_count }} Likes</span>
            </div>
            <div class="action-item">
              <i class="mdi mdi-comment-outline"></i>
              <span>{{ comments.length }} Comments</span>
            </div>
            <div class="action-item" @click.stop="shareVideo(selectedVideo)">
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
                  <button v-if="isVideoFromUser(selectedVideo)" @click="openDeleteCommentDialog(comment.id)" class="delete-comment-btn">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Video Dialog -->
    <v-dialog v-model="editVideoPopup" max-width="500">
      <v-card>
        <v-card-title>Edit Video</v-card-title>
        <v-card-text>
          <div v-if="selectedVideo && selectedVideo.file_path" class="current-video">
            <p>Current Video:</p>
            <video :src="getImageUrl(selectedVideo.file_path)" controls
                   style="max-width: 100%; max-height: 200px; margin-bottom: 10px;"></video>
          </div>
          <v-file-input label="Replace Video (.mp4, .mov)" accept=".mp4, .mov"
                        @update:modelValue="handleEditFileUpload" outlined></v-file-input>
          <v-text-field v-model="editVideoDescription" label="Description" outlined></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="cancelEditVideo">Cancel</v-btn>
          <v-btn color="primary" @click="saveEditVideo">Update Video</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title class="headline">Confirm Deletion</v-card-title>
        <v-card-text>
          Are you sure you want to delete this {{ deleteType === 'video' ? 'video' : 'comment' }}? This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="cancelDelete">Cancel</v-btn>
          <v-btn color="error" @click="confirmDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import axios from 'axios';
import { generateAvatar } from '@/utils/avatar.js';

const route = useRoute();
const router = useRouter();
const username = route.params.username;
const user = ref({});
const selectedVideo = ref(null);
const comments = ref([]);
const newComment = ref('');
const isDeleting = ref(false);
const videoMenuVisible = ref(null);
const editVideoPopup = ref(false);
const editVideoDescription = ref('');
const editVideoFile = ref(null);
const currentUser = ref({});
const users = ref({});
const shares = ref(0);
const deleteDialog = ref(false);
const deleteType = ref('');
const itemToDelete = ref(null);

const API_BASE_URL = 'http://localhost:8000';
const VIDEO_EXTENSIONS = ['.mp4', '.mov'];

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token found, redirecting to login');
      router.push('/login');
      return;
    }

    const usersResult = await axios.get(`${API_BASE_URL}/api/users`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = { name: user.name, username: user.username, profile_photo_path: user.profile_photo_path };
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
    console.log('Fetching video post with ID:', postId);
    const postResult = await axios.get(`${API_BASE_URL}/api/posts/${postId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    const post = postResult.data.data;
    console.log('Post data:', post);

    if (post.file_path && VIDEO_EXTENSIONS.some(ext => post.file_path.toLowerCase().endsWith(ext))) {
      selectedVideo.value = post;
    } else {
      console.warn('Selected post is not a video');
      selectedVideo.value = null;
      return;
    }

    console.log('Fetching comments for video ID:', postId);
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
    const videoElement = document.getElementById(`video-${postId}`);
    if (videoElement) {
      videoElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      videoElement.classList.add('highlight');
      setTimeout(() => videoElement.classList.remove('highlight'), 2000);
    } else {
      console.warn(`Video element with ID video-${postId} not found`);
    }
  } catch (error) {
    console.error('Error fetching video or comments:', error.response?.data || error.message);
    console.error('Failed request:', error.config);
    selectedVideo.value = null;
  }
});

const getImageUrl = (path) => path ? `${API_BASE_URL}/storage/${path}` : generateAvatar('User');
const getProfilePhotoById = (id) => {
  const user = users.value[id];
  return user?.profile_photo_path ? `${API_BASE_URL}/storage/${user.profile_photo_path}` : generateAvatar(user?.name || 'User');
};
const getCommentUserPhoto = (user) => user?.profile_photo_path ? `${API_BASE_URL}/storage/${user.profile_photo_path}` : 'https://via.placeholder.com/40';
const getUserNameById = (id) => users.value[id]?.name || 'Unknown User';
const getUsernameById = (id) => users.value[id]?.username || null;
const isVideoFromUser = (video) => video.id_user === currentUser.value.id;

const goToUserProfile = (username) => {
  console.log('Navigating to profile with username:', username);
  if (username) {
    router.push(`/profile/${username}`);
  } else {
    console.warn('No username available for profile navigation');
  }
};

const toggleLike = async (video) => {
  try {
    const token = localStorage.getItem('token');
    if (video.liked_by_user) {
      await axios.delete(`${API_BASE_URL}/api/posts/${video.id}/like`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      video.liked_by_user = false;
      video.likes_count -= 1;
    } else {
      await axios.post(`${API_BASE_URL}/api/posts/${video.id}/like`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      video.liked_by_user = true;
      video.likes_count += 1;
    }
  } catch (error) {
    console.error('Error toggling like:', error.response?.data || error.message);
    const postResult = await axios.get(`${API_BASE_URL}/api/posts/${video.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    video.liked_by_user = postResult.data.data.liked_by_user;
    video.likes_count = postResult.data.data.likes_count;
  }
};

const addComment = async () => {
  if (!newComment.value.trim()) return;
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post(`${API_BASE_URL}/api/posts/${selectedVideo.value.id}/comments`, {
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

const openDeleteVideoDialog = (videoId) => {
  deleteType.value = 'video';
  itemToDelete.value = videoId;
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
    if (deleteType.value === 'video') {
      isDeleting.value = true;
      await axios.delete(`${API_BASE_URL}/api/posts/${itemToDelete.value}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      selectedVideo.value = null;
      router.push(`/profile/${username}`);
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

const toggleVideoMenu = (videoId) => {
  videoMenuVisible.value = videoMenuVisible.value === videoId ? null : videoId;
};

const editVideo = (video) => {
  editVideoDescription.value = video.description || '';
  editVideoFile.value = null;
  editVideoPopup.value = true;
};

const handleEditFileUpload = (files) => {
  editVideoFile.value = files ? files[0] : null;
};

const cancelEditVideo = () => {
  editVideoPopup.value = false;
  editVideoDescription.value = '';
  editVideoFile.value = null;
};

const saveEditVideo = async () => {
  if (!selectedVideo.value) return;

  try {
    const token = localStorage.getItem('token');
    const formData = new FormData();
    formData.append('description', editVideoDescription.value);
    formData.append('_method', 'PUT');
    if (editVideoFile.value) {
      formData.append('file', editVideoFile.value);
    }

    const response = await axios.post(
      `${API_BASE_URL}/api/posts/${selectedVideo.value.id}`,
      formData,
      { headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' } }
    );

    selectedVideo.value = response.data.post || response.data.data;
    editVideoPopup.value = false;
  } catch (error) {
    console.error('Error updating video:', error);
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const reportVideo = async (video) => {
  try {
    const token = localStorage.getItem('token');
    await axios.post(`${API_BASE_URL}/api/posts/${video.id}/report`, {
      reason: prompt('Please enter reason for reporting:')
    }, { headers: { Authorization: `Bearer ${token}` } });
    alert('Video reported successfully');
    videoMenuVisible.value = null;
  } catch (error) {
    console.error('Error reporting video:', error);
  }
};

const shareVideo = (video) => {
  const shareUrl = `${window.location.origin}/users/username/${username}/videos?postId=${video.id}`;
  navigator.clipboard.writeText(shareUrl)
    .then(() => {
      shares.value++;
      alert('Video URL copied to clipboard!');
    })
    .catch(err => console.error('Error copying URL:', err));
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
.user-videos-container {
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
  text-align: center;
}

.videos-and-comments {
  display: flex;
  max-width: 800px;
  margin: 0 auto;
}

.videos-column {
  flex: 2;
  margin-right: 20px;
}

.video-card {
  background: #ffffff;
  border: 1px solid #ffffff;
  border-radius: 24px;
  padding: 20px;
  width: 100%;
}

.video-card.highlight {
  animation: highlight 2s ease-out;
}

.video-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.profile-section {
  display: flex;
  align-items: center;
  gap: 10px;
}

.video-profile-link {
  cursor: pointer;
}

.profile-pic {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.video-info {
  display: flex;
  flex-direction: column;
}

.video-username {
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
}

.video-username:hover {
  text-decoration: underline;
}

.post-date {
  font-size: 12px;
  color: #666;
}

.username-handle {
  font-size: 14px;
  color: #666;
}

.video-description {
  margin-bottom: 15px;
}

.video-content {
  width: 100%;
  max-height: 600px;
  border-radius: 20px;
  margin-bottom: 15px;
}

.video-menu {
  position: relative;
}

.video-menu button {
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

.video-actions {
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
  /* No max-height or overflow-y, flows naturally */
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

.no-videos {
  text-align: center;
  color: #666;
  padding: 20px;
}
</style>
