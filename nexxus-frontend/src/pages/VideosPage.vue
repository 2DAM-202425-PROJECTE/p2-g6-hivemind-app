<template>
  <div class="videos-page-container" :style="equippedBackgroundStyle">
    <Navbar />
    <div class="videos-list">
      <div v-if="videos.length === 0" class="no-videos">
        <p>No videos available</p>
      </div>
      <div v-else class="video-grid">
        <div v-for="video in videos" :key="video.id" :id="`video-${video.id}`" class="video-card"
             @click="goToUserVideoPage(video)">
          <div class="video-header">
            <div class="profile-section">
              <div class="video-profile-link" @click.stop="goToUserProfile(getUsernameById(video.id_user))">
                <img :src="getProfilePhotoById(video.id_user)" class="profile-pic" alt="Profile" />
              </div>
              <div class="video-info">
                <strong class="video-username" @click.stop="goToUserProfile(getUsernameById(video.id_user))">
                  {{ getUserNameById(video.id_user) }}
                </strong>
                <span class="username-handle">{{ getUsernameById(video.id_user) }}</span>
              </div>
            </div>
            <div class="video-menu">
              <button @click.stop="toggleVideoMenu(video.id)">
                <i class="mdi mdi-dots-vertical"></i>
              </button>
              <div v-if="videoMenuVisible === video.id" class="dropdown-menu">
                <ul>
                  <template v-if="isVideoFromUser(video)">
                    <li @click.stop="editVideo(video)">Edit</li>
                    <li @click.stop="openDeleteVideoDialog(video.id)" :disabled="isDeleting">Delete</li>
                  </template>
                  <template v-else>
                    <li @click.stop="reportVideo(video)">Report</li>
                  </template>
                </ul>
              </div>
            </div>
          </div>
          <div class="video-description">
            <h5>{{ video.description || 'No description' }}</h5>
          </div>
          <video :src="getImageUrl(video.file_path)" alt="Video" class="video-content" controls @click.stop />
          <div class="video-actions">
            <div class="action-item" @click.stop="toggleLike(video)">
              <i class="mdi" :class="video.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
              <span>{{ video.likes_count }} Likes</span>
            </div>
            <div class="action-item" @click.stop="goToVideoComments(video.id)">
              <i class="mdi mdi-comment-outline"></i>
              <span>{{ video.comments_count }} Comments</span>
            </div>
            <div class="action-item" @click.stop="shareVideo(video)">
              <i class="mdi mdi-share-outline"></i>
              <span>{{ video.shares || 0 }} Shares</span>
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
          Are you sure you want to delete this video? This action cannot be undone.
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
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import { generateAvatar } from '@/utils/avatar';
import apiClient from "@/axios.js";

const router = useRouter();
const videos = ref([]);
const isDeleting = ref(false);
const videoMenuVisible = ref(null);
const editVideoPopup = ref(false);
const editVideoDescription = ref('');
const editVideoFile = ref(null);
const selectedVideo = ref(null);
const currentUser = ref({});
const users = ref({});
const deleteDialog = ref(false);
const itemToDelete = ref(null);

const API_BASE_URL = 'http://localhost:8000';
const VIDEO_EXTENSIONS = ['.mp4', '.mov'];

// Load cached background path from localStorage (if available)
const cachedBackgroundPath = ref(localStorage.getItem('equipped_background_path') || null);

// Computed property for background style
const equippedBackgroundStyle = computed(() => {
  const bgPath = currentUser.value.equipped_background_path || cachedBackgroundPath.value;
  return bgPath
    ? { backgroundImage: `url(${bgPath})`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed' }
    : { backgroundColor: '#f0f2f5' };
});

// Fetch user data including equipped background
const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token available');
      return;
    }

    const userResult = await apiClient.get('/api/user');
    currentUser.value = userResult.data;

    const equippedItemsResult = await apiClient.get(`/api/user/${currentUser.value.id}/equipped-items`);
    currentUser.value.equipped_background_path = equippedItemsResult.data.equipped_background_path;
    console.log('Equipped background path:', currentUser.value.equipped_background_path);

    if (currentUser.value.equipped_background_path) {
      localStorage.setItem('equipped_background_path', currentUser.value.equipped_background_path);
    } else {
      localStorage.removeItem('equipped_background_path');
    }
  } catch (error) {
    console.error('Error fetching user data:', error.response?.data || error.message);
  }
};

onMounted(async () => {
  await fetchUserData();
  await fetchVideos();
});

const fetchVideos = async () => {
  try {
    const usersResult = await apiClient.get(`/api/users`);
    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = { name: user.name, username: user.username, profile_photo_path: user.profile_photo_path };
      return acc;
    }, {});

    const postsResult = await apiClient.get(`/api/posts`);
    console.log('Raw API posts response:', postsResult.data.data);
    const allPosts = postsResult.data.data || [];
    const videoPosts = allPosts.filter(post =>
      post.file_path && VIDEO_EXTENSIONS.some(ext => post.file_path.toLowerCase().endsWith(ext))
    );

    const videosWithComments = await Promise.all(videoPosts.map(async (post) => {
      let commentsCount = post.comments_count ?? null;
      if (commentsCount === null) {
        console.log(`Comments_count missing for post ${post.id}, fetching comments...`);
        commentsCount = await fetchCommentCountFallback(post.id);
      }
      return { ...post, comments_count: commentsCount };
    }));

    videos.value = videosWithComments.sort(() => Math.random() - 0.5);
    console.log('Randomized video posts with comment counts:', videos.value);
  } catch (error) {
    console.error('Error fetching videos:', error.response?.data || error.message);
    videos.value = [];
  }
};

const fetchCommentCountFallback = async (videoId) => {
  try {
    console.log(`Fetching comments for video ${videoId} from /api/posts/${videoId}/comments`);
    const response = await apiClient.get(`/api/posts/${videoId}/comments`);
    console.log(`Comments response for video ${videoId}:`, response.data);
    const comments = response.data.data || response.data || [];
    return Array.isArray(comments) ? comments.length : 0;
  } catch (error) {
    console.error(`Error fetching comments for video ${videoId}:`, error.response?.data || error.message);
    return 0;
  }
};

const getImageUrl = (path) => path ? `${API_BASE_URL}/storage/${path}` : generateAvatar('User');
const getProfilePhotoById = (id) => {
  const user = users.value[id];
  return user?.profile_photo_path ? `${API_BASE_URL}/storage/${user.profile_photo_path}` : generateAvatar(user?.name || 'User');
};
const getUserNameById = (id) => users.value[id]?.name || 'Unknown User';
const getUsernameById = (id) => users.value[id]?.username || null;
const isVideoFromUser = (video) => video.id_user === currentUser.value.id;

const goToUserProfile = (username) => {
  if (username) router.push(`/profile/${username}`);
  else console.warn('No username available for profile navigation');
};

const goToUserVideoPage = (video) => {
  const username = getUsernameById(video.id_user);
  if (username) {
    console.log('Navigating to user media page with username:', username, 'postId:', video.id);
    router.push(`/users/${username}/media?postId=${video.id}`);
  } else {
    console.warn('No username found for userId:', video.id_user);
  }
};

const goToVideoComments = async (videoId) => {
  const username = getUsernameById(videos.value.find(v => v.id === videoId)?.id_user);
  if (username) {
    await router.push(`/users/${username}/media?postId=${videoId}`);
    const video = videos.value.find(v => v.id === videoId);
    if (video) {
      video.comments_count = await fetchCommentCountFallback(videoId);
      console.log(`Updated comments_count for video ${videoId}: ${video.comments_count}`);
    }
  }
};

const toggleLike = async (video) => {
  try {
    const token = localStorage.getItem('token');
    if (video.liked_by_user) {
      await apiClient.delete(`/api/posts/${video.id}/like`);
      video.liked_by_user = false;
      video.likes_count -= 1;
    } else {
      await apiClient.post(`/api/posts/${video.id}/like`);
      video.liked_by_user = true;
      video.likes_count += 1;
    }
  } catch (error) {
    console.error('Error toggling like:', error.response?.data || error.message);
    const postResult = await apiClient.get(`/api/posts/${video.id}`);
    video.liked_by_user = postResult.data.data.liked_by_user;
    video.likes_count = postResult.data.data.likes_count;
    video.comments_count = postResult.data.data.comments_count ?? await fetchCommentCountFallback(video.id);
  }
};

const toggleVideoMenu = (videoId) => {
  videoMenuVisible.value = videoMenuVisible.value === videoId ? null : videoId;
};

const editVideo = (video) => {
  selectedVideo.value = video;
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
  selectedVideo.value = null;
};

const saveEditVideo = async () => {
  if (!selectedVideo.value) return;

  try {
    const token = localStorage.getItem('token');
    const formData = new FormData();
    formData.append('description', editVideoDescription.value);
    formData.append('_method', 'PUT');
    if (editVideoFile.value) formData.append('file', editVideoFile.value);

    const response = await apiClient.post(`/api/posts/${selectedVideo.value.id}`, formData);

    const updatedVideo = response.data.post || response.data.data;
    updatedVideo.comments_count = updatedVideo.comments_count ?? await fetchCommentCountFallback(updatedVideo.id);
    const index = videos.value.findIndex(v => v.id === selectedVideo.value.id);
    if (index !== -1) videos.value[index] = updatedVideo;
    editVideoPopup.value = false;
    selectedVideo.value = null;
  } catch (error) {
    console.error('Error updating video:', error);
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const openDeleteVideoDialog = (videoId) => {
  itemToDelete.value = videoId;
  deleteDialog.value = true;
};

const cancelDelete = () => {
  deleteDialog.value = false;
  itemToDelete.value = null;
};

const confirmDelete = async () => {
  try {
    isDeleting.value = true;
    await apiClient.delete(`/api/posts/${itemToDelete.value}`);
    videos.value = videos.value.filter(video => video.id !== itemToDelete.value);
  } catch (error) {
    console.error('Error deleting video:', error.response?.data || error.message);
  } finally {
    isDeleting.value = false;
    deleteDialog.value = false;
    itemToDelete.value = null;
  }
};

const reportVideo = async (video) => {
  try {
    await apiClient.post(`/api/posts/${video.id}/report`, {
      reason: prompt('Please enter reason for reporting:')
    });
    alert('Video reported successfully');
    videoMenuVisible.value = null;
    video.comments_count = await fetchCommentCountFallback(video.id);
  } catch (error) {
    console.error('Error reporting video:', error);
  }
};

const shareVideo = (video) => {
  const username = getUsernameById(video.id_user);
  const shareUrl = `${window.location.origin}/users/${username}/media?postId=${video.id}`;
  navigator.clipboard.writeText(shareUrl)
    .then(() => {
      video.shares = (video.shares || 0) + 1;
      alert('Video URL copied to clipboard!');
    })
    .catch(err => console.error('Error copying URL:', err));
};
</script>

<style scoped>
.videos-page-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  padding-bottom: 80px;
  min-height: 100vh;
  color: black;
}

h1 {
  font-size: 24px;
  padding-bottom: 20px;
  text-align: center;
}

.videos-list {
  max-width: 800px;
  margin: 0 auto;
}

.video-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.video-card {
  background: #ffffff;
  border: 1px solid #ffffff;
  border-radius: 24px;
  padding: 20px;
  width: 100%;
  cursor: pointer;
}

.video-card:hover {
  background: #f9f9f9;
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

.username-handle {
  font-size: 14px;
  color: #666;
}

.video-description {
  margin-bottom: 15px;
}

.video-content {
  width: 100%;
  max-height: 400px;
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

.no-videos {
  text-align: center;
  color: #666;
  padding: 20px;
}
</style>
