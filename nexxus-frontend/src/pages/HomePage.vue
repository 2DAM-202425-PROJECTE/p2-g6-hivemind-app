<template>
  <div class="home-container">
    <Navbar />
    <h1>Home</h1>
    <StoriesBar :stories="stories.data" />

    <!-- Post Input Section -->
    <div class="post-section">
      <div class="post-input-container">
        <img :src="getProfilePhotoById(currentUser.id)" class="profile-pic" alt="Profile" />
        <div class="post-input-wrapper">
          <textarea v-model="newPostContent" placeholder="What's happening?" class="post-input" rows="2"
                    @input="adjustTextareaHeight"></textarea>

          <!-- Preview for uploaded file -->
          <div v-if="newPostFile" class="file-preview">
            <div class="preview-container">
              <img v-if="!newPostFile.type.includes('video')" :src="previewUrl" alt="Image Preview"
                   class="preview-media" />
              <video v-else :src="previewUrl" controls class="preview-media"></video>
              <button class="remove-file-btn" @click="removeFile">X</button>
            </div>
          </div>

          <!-- Preview for location -->
          <div v-if="selectedLocation" class="location-preview">
            <i class="mdi mdi-map-marker"></i>
            <a :href="`https://www.google.com/maps?q=${selectedLocation.lat},${selectedLocation.lon}`" target="_blank"
               class="location-btn">
              {{ selectedLocation.name }}
            </a>
            <button class="remove-btn" @click="removeLocation">Remove</button>
          </div>

          <div class="post-actions">
            <div class="action-buttons">
              <label for="media-upload" class="action-btn" title="Add Image/Video">
                <i class="mdi mdi-image"></i>
              </label>
              <input id="media-upload" type="file" accept=".png, .jpg, .jpeg, .mp4" @change="handleNewPostFileUpload"
                     style="display: none" />
              <button class="action-btn" @click="toggleEmojiPicker" title="Add Emoji">
                <i class="mdi mdi-emoticon-outline"></i>
              </button>
              <button class="action-btn" @click="getLocation" title="Add Location">
                <i class="mdi mdi-map-marker-outline"></i>
              </button>
            </div>
            <button class="post-btn" :disabled="!newPostContent && !newPostFile && !selectedLocation"
                    @click="submitPost">
              Post
            </button>
          </div>

          <!-- New Emoji Picker -->
          <VEmojiPicker v-if="showEmojiPicker" class="emoji-picker" @select="addEmoji" />
        </div>
      </div>
    </div>

    <!-- Post Cards -->
    <div class="post-card" v-for="post in posts" :key="post.id" @click="navigateToPost(post, $event)">
      <div class="post-header">
        <div class="post-profile-link" @click.stop="goToUserProfile(post.id_user)">
          <img :src="getProfilePhotoById(post.id_user)" class="profile-pic" alt="Profile" />
        </div>
        <div class="post-info">
          <ul>
            <li>
              <strong class="post-username" @click.stop="goToUserProfile(post.id_user)">
                {{ getUserNameById(post.id_user) }}
              </strong>
              <p class="post-date">{{ formatDate(post.created_at) }}</p>
              <div class="post-description" v-html="renderPostDescription(post.description)"></div>
              <div v-if="post.location" class="post-location">
                <i class="mdi mdi-earth location-icon"></i>
                <a :href="`https://www.google.com/maps?q=${encodeURIComponent(post.location)}`" target="_blank"
                   class="location-link">
                  {{ simplifyLocation(post.location) }}
                </a>
              </div>
              <template v-if="post.file_path && post.file_path.includes('.mp4')">
                <video :src="getImageUrl(post.file_path)" alt="file Video" class="post-content" controls />
              </template>
              <template v-else-if="post.file_path">
                <img :src="getImageUrl(post.file_path)" alt="file Image" class="post-content" />
              </template>
            </li>
          </ul>
        </div>
        <div class="post-menu">
          <button @click.stop="togglePostMenu(post.id)">
            <i class="mdi mdi-dots-vertical"></i>
          </button>
          <div v-if="postMenuVisible === post.id" class="dropdown-menu" @click.stop>
            <ul>
              <li v-show="isPostFromUser(post)" @click.stop="editPost(post)" data-edit>Edit</li>
              <li v-show="isPostFromUser(post)" @click.stop="deletePost(post.id)" :disabled="isDeleting">Delete</li>
              <li @click.stop="reportPost(post)">Report</li>
            </ul>
          </div>
        </div>
      </div>

      <div v-if="editPostPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
           @click="cancelEditPost">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md" @click.stop>
          <h3 class="text-lg font-bold mb-4">Edit Post</h3>
          <div v-if="selectedPost && selectedPost.file_path" class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-300">Current File:</p>
            <img v-if="!selectedPost.file_path.includes('.mp4')" :src="getImageUrl(selectedPost.file_path)"
                 alt="Current post image" class="max-w-full h-auto max-h-48 mb-2" />
            <video v-else :src="getImageUrl(selectedPost.file_path)" controls
                   class="max-w-full h-auto max-h-48 mb-2"></video>
          </div>
          <label class="block mb-4">
            <span class="sr-only">Choose file</span>
            <input type="file" accept=".png, .jpg, .jpeg, .mp4" @change="handleEditFileUpload" @click.stop class="block w-full text-sm text-gray-500
          file:mr-4 file:py-2 file:px-4
          file:rounded file:border-0
          file:text-sm file:font-semibold
          file:bg-blue-500 file:text-white
          hover:file:bg-blue-600" />
          </label>
          <input v-model="editPostDescription" type="text" placeholder="Description"
                 class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:text-white" />
          <div class="flex justify-end gap-2">
            <button @click="cancelEditPost"
                    class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white">
              Cancel
            </button>
            <button @click="saveEditPost" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
              Update Post
            </button>
          </div>
        </div>
      </div>

      <div class="post-actions">
        <div class="action-item" @click.stop="toggleLike(post)">
          <i class="mdi" :class="post.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
          <span>{{ post.likes_count }} Likes</span>
        </div>
        <div class="action-item" @click.stop="navigateToPost(post)">
          <i class="mdi mdi-comment-outline"></i>
          <span>{{ post.comments ? post.comments.length : 0 }} Comments</span>
        </div>
        <div class="action-item" @click.stop="sharePost(post)">
          <i class="mdi mdi-share-outline"></i>
          <span>{{ shares }} Shares</span>
        </div>
      </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="loading" class="loading-indicator">
      <div class="spinner"></div>
      Loading posts...
    </div>

    <!-- End of posts message -->
    <div v-if="noMorePosts" class="end-message">
      No posts to load
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import StoriesBar from '@/components/StoriesBar.vue';
import axios from 'axios';
import { generateAvatar } from '@/utils/avatar';
import VEmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';
import apiClient from "@/axios.js";

onMounted(() => {
  fetchPosts(1, true);
  window.addEventListener('scroll', handleScroll);
  window.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  window.removeEventListener('click', handleClickOutside);
  if (scrollDebounce.value) {
    clearTimeout(scrollDebounce.value);
  }
});

const router = useRouter();
const posts = ref([]);
const loading = ref(false);
const noMorePosts = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const scrollDebounce = ref(null);
const users = ref({});
const selectedPostId = ref(null);
const selectedPost = ref(null);
const isDeleting = ref(false);
const postMenuVisible = ref(null);
const editPostPopup = ref(false);
const editPostDescription = ref('');
const editPostLocation = ref(null);
const editPostFile = ref(null);
const stories = ref({ data: [] });
const shares = ref(0);
const newPostContent = ref('');
const newPostFile = ref(null);
const previewUrl = ref(null);
const showEmojiPicker = ref(false);
const selectedLocation = ref(null);

const currentUser = ref({
  id: null,
  name: 'Current User',
  profile_photo_path: null,
});

const fetchPosts = async (page = 1, initialLoad = false) => {
  if (loading.value || (noMorePosts.value && !initialLoad)) return;

  loading.value = true;

  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No hay token disponible');
      return;
    }

    const result = await apiClient.get(`/api/posts?page=${page}`);

    if (initialLoad) {
      posts.value = result.data.data;
    } else {
      posts.value = [...posts.value, ...result.data.data];
    }

    currentPage.value = result.data.current_page;
    lastPage.value = result.data.last_page;

    if (currentPage.value >= lastPage.value) {
      noMorePosts.value = true;
    }

    const usersResult = await apiClient.get('/api/users');

    if (!usersResult.data || !usersResult.data.data) {
      console.error('Estructura inesperada en la respuesta de usuarios:', usersResult.data);
      return;
    }

    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = { name: user.name, username: user.username, profile_photo_path: user.profile_photo_path };
      return acc;
    }, {});

    const userResult = await apiClient.get('/api/user');
    currentUser.value = userResult.data;

    const storiesResult = await apiClient.get('/api/stories');
    stories.value = storiesResult.data;
  } catch (error) {
    console.error('Error al obtener datos', error.response?.data || error.message);
  } finally {
    loading.value = false;
  }
};

const handleScroll = () => {
  if (scrollDebounce.value) {
    clearTimeout(scrollDebounce.value);
  }

  scrollDebounce.value = setTimeout(() => {
    const { scrollTop, clientHeight, scrollHeight } = document.documentElement;
    const isNearBottom = scrollTop + clientHeight >= scrollHeight - 300;

    if (isNearBottom && !loading.value && currentPage.value < lastPage.value) {
      fetchPosts(currentPage.value + 1);
    }
  }, 100);
};

const adjustTextareaHeight = (event) => {
  const textarea = event.target;
  textarea.style.height = 'auto';
  textarea.style.height = `${textarea.scrollHeight}px`;
};

const handleNewPostFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    newPostFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
  }
};

const removeFile = () => {
  newPostFile.value = null;
  previewUrl.value = null;
  document.getElementById('media-upload').value = '';
};

const toggleEmojiPicker = (event) => {
  event.stopPropagation();
  showEmojiPicker.value = !showEmojiPicker.value;
};

const handleClickOutside = (event) => {
  if (showEmojiPicker.value && !event.target.closest('.emoji-picker') && !event.target.closest('.action-btn')) {
    showEmojiPicker.value = false;
  }
  if (postMenuVisible.value && !event.target.closest('.post-menu')) {
    postMenuVisible.value = null;
  }
};

const addEmoji = (emoji) => {
  console.log('Selected emoji data:', emoji); // Debug the emitted data
  if (typeof emoji === 'string') {
    newPostContent.value += emoji; // Direct string
  } else if (emoji && emoji.i) {
    newPostContent.value += emoji.i; // Likely property from vue3-emoji-picker
  } else if (emoji && emoji.emoji) {
    newPostContent.value += emoji.emoji; // Alternative property
  } else if (emoji && emoji.code) {
    newPostContent.value += emoji.code; // Another possible property
  } else {
    newPostContent.value += '❓'; // Fallback
  }
  showEmojiPicker.value = false;
};

const getLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const { latitude, longitude } = position.coords;
        try {
          const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`);
          const data = await response.json();
          selectedLocation.value = {
            name: data.display_name || `Lat: ${latitude}, Lon: ${longitude}`,
            lat: latitude,
            lon: longitude,
          };
        } catch (error) {
          console.error('Error fetching location name:', error);
          selectedLocation.value = {
            name: `Lat: ${latitude}, Lon: ${longitude}`,
            lat: latitude,
            lon: longitude,
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

const removeLocation = () => {
  selectedLocation.value = null;
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
            lon: longitude,
          };
        } catch (error) {
          console.error('Error fetching location name:', error);
          editPostLocation.value = {
            name: `Lat: ${latitude}, Lon: ${longitude}`,
            lat: latitude,
            lon: longitude,
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

const submitPost = async () => {
  try {
    const token = localStorage.getItem('token');

    if (!token) {
      console.error('No token available');
      return;
    }

    // Check if currentUser is defined
    if (!currentUser.value || !currentUser.value.id) {
      console.error('Current user is not defined');
      alert('Error: User not found. Please log in again.');
      return;
    }
    const now = new Date();
    const publishDate = now.toISOString().slice(0, 19).replace('T', ' ');

    const formData = new FormData();
    formData.append('description', newPostContent.value);
    formData.append('publish_date', publishDate);
    formData.append('id_user', currentUser.value.id);
    if (newPostFile.value) formData.append('file', newPostFile.value);
    if (selectedLocation.value) formData.append('location', selectedLocation.value.name);

    await apiClient.post('/api/posts', formData);

    await fetchPosts(1, true);
    newPostContent.value = '';
    newPostFile.value = null;
    previewUrl.value = null;
    selectedLocation.value = null;
    document.getElementById('media-upload').value = '';
  } catch (error) {
    console.error('Error creating post:', error.response?.data || error.message);
    alert('Failed to create post: ' + (error.response?.data?.message || error.message));
  }
};

const renderPostDescription = (description) => {
  if (!description) return '';
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

const getImageUrl = (path) => {
  if (!path) return generateAvatar('User');
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  return `http://localhost:8000/storage/${path}`;
};

const getProfilePhotoById = (id) => {
  const user = users.value[id];
  if (user?.profile_photo_path) {
    if (user.profile_photo_path.startsWith('http://') || user.profile_photo_path.startsWith('https://')) {
      return user.profile_photo_path;
    }
    return `http://localhost:8000/storage/${user.profile_photo_path}`;
  }
  return generateAvatar(user?.name || 'User');
};

const getUserNameById = (id) => users.value[id]?.name || 'Usuario desconocido';
const getUsernameById = (id) => users.value[id]?.username || null;

const goToUserProfile = (userId) => {
  const username = getUsernameById(userId);
  if (username) router.push(`/profile/${username}`);
  else console.warn('No username found for userId:', userId);
};

const navigateToPost = (post, event) => {
  if (event.target.closest('.post-menu, .dropdown-menu, [data-edit]')) return;
  const username = getUsernameById(post.id_user);
  if (!username) {
    console.warn('No username found for userId:', post.id_user);
    return;
  }
  router.push(`/users/${username}/media?postId=${post.id}`);
};

const toggleLike = async (post) => {
  const token = localStorage.getItem('token');
  if (!token) return console.error('No token available');

  const method = post.liked_by_user ? 'delete' : 'post';

  try {
    await apiClient[method](`/api/posts/${post.id}/like`);
    post.liked_by_user = !post.liked_by_user;
    post.likes_count += post.liked_by_user ? 1 : -1;
  } catch (error) {
    console.error('Error toggling like:', error.response?.data || error.message);
  }
};

const togglePostMenu = (postId) => {
  postMenuVisible.value = postMenuVisible.value === postId ? null : postId;
};

const editPost = (post) => {
  postMenuVisible.value = null;
  selectedPost.value = post;
  editPostDescription.value = post.description || '';
  editPostLocation.value = post.location ? { name: post.location, lat: null, lon: null } : null;
  editPostFile.value = null;
  editPostPopup.value = true;
};

const handleEditFileUpload = (event) => {
  editPostFile.value = event.target.files ? event.target.files[0] : null;
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
    const formData = new FormData();
    formData.append('description', editPostDescription.value);
    formData.append('_method', 'PUT');

    if (editPostFile.value) {
      formData.append('file', editPostFile.value);
    }

    const response = await apiClient.put(`/api/posts/${selectedPost.value.id}`, formData);

    const updatedPost = response.data.post;
    const postIndex = posts.value.findIndex(p => p.id === selectedPost.value.id);
    if (postIndex !== -1) {
      posts.value[postIndex] = {
        ...posts.value[postIndex],
        description: updatedPost.description,
        file_path: updatedPost.file_path || posts.value[postIndex].file_path,
        updated_at: updatedPost.updated_at,
      };
    }

    editPostPopup.value = false;
  } catch (error) {
    console.error('Error updating post:', error);
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const isPostFromUser = (post) => post.id_user === currentUser.value.id;

const deletePost = async (postId) => {
  isDeleting.value = true;
  try {
    await apiClient.delete(`/api/posts/${postId}`);
    posts.value = posts.value.filter(post => post.id !== postId);
  } catch (error) {
    console.error('Error deleting post:', error.response?.data || error.message);
    alert('Failed to delete post: ' + (error.response?.data?.message || error.message));
  } finally {
    isDeleting.value = false;
  }
};

const reportPost = (post) => alert(`Reported post with ID: ${post.id}`);
const sharePost = (post) => {};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
.home-container {
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

.post-section {
  max-width: 800px;
  margin: 0 auto 20px auto;
  background: #ffffff;
  border: 1px solid #d3d3d3;
  border-radius: 10px;
  padding: 15px;
}

.post-input-container {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.post-input-wrapper {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
  position: relative;
}

.post-input {
  width: 100%;
  border: none;
  outline: none;
  resize: none;
  font-size: 16px;
  font-family: Arial, sans-serif;
  padding: 5px;
  line-height: 1.5;
  background: transparent;
}

.post-input::placeholder {
  color: #666;
}

.file-preview {
  margin-top: 10px;
}

.preview-container {
  position: relative;
  display: inline-block;
}

.preview-media {
  max-width: 100%;
  max-height: 200px;
  border-radius: 10px;
  object-fit: contain;
}

.remove-file-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background: #ff4444;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  cursor: pointer;
  font-size: 12px;
  line-height: 20px;
  text-align: center;
}

.remove-file-btn:hover {
  background: #cc0000;
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

.post-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.action-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  color: #1da1f2;
}

.action-btn i {
  font-size: 20px;
}

.action-btn:hover {
  color: #0d91e2;
}

.post-btn {
  background-color: #1da1f2;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 5px 15px;
  font-size: 14px;
  cursor: pointer;
}

.post-btn:disabled {
  background-color: #a1d2f7;
  cursor: not-allowed;
}

.post-btn:hover:not(:disabled) {
  background-color: #0d91e2;
}

.emoji-picker {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

.post-card {
  background: #ffffff;
  border: 1px solid #ffffff;
  border-radius: 24px;
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
  margin-bottom: 20px;
  width: 100%;
  cursor: pointer;
}

.post-card:hover {
  background: #f9f9f9;
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

.post-info h3 {
  font-size: 16px;
  margin: 0;
}

.post-info p {
  font-size: 12px;
  margin: 0;
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

.post-menu {
  position: relative;
  flex-shrink: 0;
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
}

.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown-menu li {
  padding: 10px 15px;
  cursor: pointer;
  white-space: nowrap;
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
  height: 400px;
  max-width: 760px;
  max-height: 400px;
  object-fit: contain;
  border-radius: 20px;
  margin-bottom: 15px;
}

.loading-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: #666;
}

.spinner {
  border: 3px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 3px solid #3498db;
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.end-message {
  text-align: center;
  padding: 20px;
  color: #999;
  font-style: italic;
}
</style>
