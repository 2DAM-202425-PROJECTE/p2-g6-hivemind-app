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
          <textarea
            v-model="newPostContent"
            placeholder="What's happening?"
            class="post-input"
            rows="2"
            @input="adjustTextareaHeight"
          ></textarea>

          <!-- Preview for uploaded file -->
          <div v-if="newPostFile" class="file-preview">
            <div class="preview-container">
              <img
                v-if="!newPostFile.type.includes('video')"
                :src="previewUrl"
                alt="Image Preview"
                class="preview-media"
              />
              <video
                v-else
                :src="previewUrl"
                controls
                class="preview-media"
              ></video>
              <button class="remove-file-btn" @click="removeFile">X</button>
            </div>
          </div>

          <!-- Preview for location -->
          <div v-if="selectedLocation" class="location-preview">
            <i class="mdi mdi-map-marker"></i>
            <span>{{ selectedLocation }}</span>
            <button class="remove-btn" @click="removeLocation">Remove</button>
          </div>

          <div class="post-actions">
            <div class="action-buttons">
              <label for="media-upload" class="action-btn" title="Add Image/Video">
                <i class="mdi mdi-image"></i>
              </label>
              <input
                id="media-upload"
                type="file"
                accept=".png, .jpg, .jpeg, .mp4"
                @change="handleNewPostFileUpload"
                style="display: none"
              />
              <button class="action-btn" @click="toggleEmojiPicker" title="Add Emoji">
                <i class="mdi mdi-emoticon-outline"></i>
              </button>
              <button class="action-btn" @click="getLocation" title="Add Location">
                <i class="mdi mdi-map-marker-outline"></i>
              </button>
            </div>
            <button class="post-btn" :disabled="!newPostContent && !newPostFile && !selectedLocation" @click="submitPost">
              Post
            </button>
          </div>

          <!-- Emoji Picker -->
          <div v-if="showEmojiPicker" class="emoji-picker" ref="emojiPicker">
            <span
              v-for="emoji in emojis"
              :key="emoji"
              class="emoji-item"
              @click="addEmoji(emoji)"
              v-html="emoji"
            ></span>
          </div>
        </div>
      </div>
    </div>

    <div class="post-card" v-for="post in sortedPosts" :key="post.id" @click="navigateToPost(post)">
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
          <div v-if="postMenuVisible === post.id" class="dropdown-menu">
            <ul>
              <li v-show="isPostFromUser(post)" @click.stop="editPost(post)">Edit</li>
              <li v-show="isPostFromUser(post)" @click.stop="deletePost(post.id)" :disabled="isDeleting">Delete</li>
              <li @click.stop="reportPost(post)">Report</li>
            </ul>
          </div>
        </div>
      </div>

      <v-dialog v-model="editPostPopup" max-width="500">
        <v-card>
          <v-card-title>Edit Post</v-card-title>
          <v-card-text>
            <div v-if="selectedPost && selectedPost.file_path" class="current-image">
              <p>Current File:</p>
              <img
                v-if="!selectedPost.file_path.includes('.mp4')"
                :src="getImageUrl(selectedPost.file_path)"
                alt="Current post image"
                style="max-width: 100%; max-height: 200px; margin-bottom: 10px;"
              />
              <video
                v-else
                :src="getImageUrl(selectedPost.file_path)"
                controls
                style="max-width: 100%; max-height: 200px; margin-bottom: 10px;"
              ></video>
            </div>
            <v-file-input label="Replace Image/Video (.png, .jpg, .jpeg, .mp4)" accept=".png, .jpg, .jpeg, .mp4"
                          @update:modelValue="handleEditFileUpload" outlined></v-file-input>
            <v-text-field v-model="editPostDescription" label="Description" outlined></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="cancelEditPost">Cancel</v-btn>
            <v-btn color="primary" @click="saveEditPost">Update Post</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

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

    <UserRecommendation />
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import UserRecommendation from '@/components/UserRecommendation.vue';
import StoriesBar from '@/components/StoriesBar.vue';
import axios from 'axios';
import { generateAvatar } from '@/utils/avatar';

const loadTwemoji = () => {
  const script = document.createElement('script');
  script.src = 'https://twemoji.maxcdn.com/v/latest/twemoji.min.js';
  script.crossOrigin = 'anonymous';
  document.head.appendChild(script);
};

onMounted(() => {
  loadTwemoji();
  fetchPosts();
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const router = useRouter();
const posts = ref({ data: [] });
const users = ref({});
const selectedPostId = ref(null);
const selectedPost = ref(null);
const isDeleting = ref(false);
const postMenuVisible = ref(null);
const editPostPopup = ref(false);
const editPostDescription = ref('');
const editPostFile = ref(null);
const stories = ref({ data: [] });
const shares = ref(0);
const newPostContent = ref('');
const newPostFile = ref(null);
const previewUrl = ref(null);
const showEmojiPicker = ref(false);
const selectedLocation = ref(null);
const emojiPicker = ref(null); // Reference to the emoji picker DOM element

const emojis = ref([
  '😀', '😂', '😍', '😢', '😡', '👍', '👎', '❤️', '🔥', '🎉',
  '🤓', '😎', '🤗', '🙌', '💡', '🌟', '🍕', '🍔', '🍎', '🏈',
  '⚽', '🎸', '🎨', '🏆', '🚀', '💯', '🔞', '🆒', '🆕', '🆙',
  '🆓', '🆖', '🅿️', '🅰️', '🅱️', '🅾️', '🎵', '🎶', '🎼', '🎤',
  '🎧', '🎦', '🎥', '🎬', '📺', '📻', '🎮', '🎲', '🃏', '🎯',
  '🏹', '🎳', '🎰', '🚗', '🚕', '🚙', '🚌', '🚎', '🏎', '🚓',
  '🚒', '🚑', '🚚', '🚛', '🚜', '🏍', '🚲', '🛴', '🚏', '🛵',
  '🚀', '🚁', '🛩', '✈️', '🛫', '🛬', '🚦', '🚧', '⚓', '⛽',
  '🚢', '🚤', '🛳', '⛵', '🚡', '🚠', '🚟', '🚝', '🚄', '🚅',
  '🚈', '🚞', '🚋', '🚆', '🚇', '🚊', '🚉', '🚁', '🚂', '🚃',
  '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚋', '🚌', '🚍',
  '🚎', '🚏', '🚐', '🚑', '🚒', '🚓', '🚔', '🚕', '🚖', '🚗',
  '🚘', '🚙', '🚚', '🚛', '🚜', '🚝', '🚞', '🚟', '🚠', '🚡',
  '🚢', '🚣', '🚤', '🚥', '🚦', '🚧', '🚨', '🚩', '🚪', '🚫',
  '🚬', '🚭', '🚮', '🚯', '🚰', '🚱', '🚲', '🚳', '🚴', '🚵',
  '🚶', '🚷', '🚸', '🚹', '🚺', '🚻', '🚼', '🚽', '🚾', '🚿',
  '🛀', '🛁', '🛂', '🛃', '🛄', '🛅', '🛋', '🛌', '🛍', '🛎',
  '🛏', '🛐', '🛑', '🛒', '🛓', '🛔', '🛕', '🛖', '🛗', '🛘',
]);

const currentUser = ref({
  id: null,
  name: 'Current User',
  profile_photo_path: null,
});

// Computed property to sort posts by created_at in descending order
const sortedPosts = computed(() => {
  return [...posts.value.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const fetchPosts = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No hay token disponible');
      return;
    }

    const result = await axios.get('http://localhost:8000/api/posts', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    posts.value = result.data;

    const usersResult = await axios.get('http://localhost:8000/api/users', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });

    if (!usersResult.data || !usersResult.data.data) {
      console.error('Estructura inesperada en la respuesta de usuarios:', usersResult.data);
      return;
    }

    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = { name: user.name, username: user.username, profile_photo_path: user.profile_photo_path };
      return acc;
    }, {});

    const userResult = await axios.get('http://localhost:8000/api/user', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    currentUser.value = userResult.data;

    const storiesResult = await axios.get('http://localhost:8000/api/stories', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    });
    stories.value = storiesResult.data;
  } catch (error) {
    console.error('Error al obtener datos', error.response?.data || error.message);
  }
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
  event.stopPropagation(); // Prevent the click from immediately closing the picker
  showEmojiPicker.value = !showEmojiPicker.value;
};

const handleClickOutside = (event) => {
  if (showEmojiPicker.value && emojiPicker.value && !emojiPicker.value.contains(event.target)) {
    showEmojiPicker.value = false;
  }
};

const addEmoji = (emoji) => {
  newPostContent.value += emoji;
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
          selectedLocation.value = data.display_name || `Lat: ${latitude}, Lon: ${longitude}`;
        } catch (error) {
          console.error('Error fetching location name:', error);
          selectedLocation.value = `Lat: ${latitude}, Lon: ${longitude}`;
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

const submitPost = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token available');
      return;
    }

    if (!currentUser.value.id) {
      console.error('Current user ID is not available');
      alert('Error: User ID not found. Please log in again.');
      return;
    }

    const now = new Date();
    const publishDate = now.toISOString().slice(0, 19).replace('T', ' ');

    let finalDescription = newPostContent.value;
    if (selectedLocation.value) {
      finalDescription += `\n📍 ${selectedLocation.value}`;
    }

    const formData = new FormData();
    formData.append('description', finalDescription);
    formData.append('publish_date', publishDate);
    formData.append('id_user', currentUser.value.id);
    if (newPostFile.value) formData.append('file', newPostFile.value);

    const response = await axios.post(
      'http://localhost:8000/api/posts',
      formData,
      { headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' } }
    );

    await fetchPosts();
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
  const locationRegex = /📍 (.*?)(<br>|$)/g;
  html = html.replace(locationRegex, '<div class="post-location"><i class="mdi mdi-map-marker"></i><span>$1</span></div>');

  return html;
};

const getImageUrl = (path) => {
  if (!path) return generateAvatar('User');
  // Si ya es una URL completa, devolverla tal cual
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  // Si es una ruta local, añadir el prefijo de storage
  return `http://localhost:8000/storage/${path}`;
};
const getProfilePhotoById = (id) => {
  const user = users.value[id];
  if (user?.profile_photo_path) {
    // Si es una URL completa, devolverla sin cambios
    if (user.profile_photo_path.startsWith('http://') || user.profile_photo_path.startsWith('https://')) {
      return user.profile_photo_path;
    }
    // Si es una ruta local, añadir el prefijo
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

const navigateToPost = (post) => {
  const username = getUsernameById(post.id_user);
  if (!username) {
    console.warn('No username found for userId:', post.id_user);
    return;
  }

  if (post.file_path && post.file_path.includes('.mp4')) {
    router.push(`/users/username/${username}/videos?postId=${post.id}`);
  } else {
    router.push(`/users/username/${username}/posts?postId=${post.id}`);
  }
};

const toggleLike = async (post) => {
  const token = localStorage.getItem('token');
  if (!token) return console.error('No token available');

  const url = `http://localhost:8000/api/posts/${post.id}/like`;
  const method = post.liked_by_user ? 'delete' : 'post';

  try {
    await axios({ method, url, headers: { Authorization: `Bearer ${token}` } });
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
  selectedPost.value = post;
  editPostDescription.value = post.description || '';
  editPostFile.value = null;
  editPostPopup.value = true;
};

const handleEditFileUpload = (files) => {
  editPostFile.value = files ? files[0] : null;
};

const cancelEditPost = () => {
  editPostPopup.value = false;
  editPostDescription.value = '';
  editPostFile.value = null;
};

const saveEditPost = async () => {
  if (!selectedPost.value) return;

  const formData = new FormData();
  formData.append('description', editPostDescription.value);
  formData.append('_method', 'PUT');
  if (editPostFile.value) formData.append('file', editPostFile.value);

  try {
    const response = await axios.post(
      `http://localhost:8000/api/posts/${selectedPost.value.id}`,
      formData,
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}`, 'Content-Type': 'multipart/form-data' } }
    );

    const updatedPost = response.data.post;
    const index = posts.value.data.findIndex(p => p.id === selectedPost.value.id);
    if (index !== -1) posts.value.data.splice(index, 1, updatedPost);
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
    await axios.delete(`http://localhost:8000/api/posts/${postId}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    posts.value.data = posts.value.data.filter(post => post.id !== postId);
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

.location-preview span {
  font-size: 14px;
  color: #333;
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
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  padding: 10px;
  max-height: 200px;
  overflow-y: auto;
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.emoji-item {
  font-size: 24px;
  cursor: pointer;
}

.emoji-item:hover {
  background: #f0f0f0;
  border-radius: 5px;
}

.post-description {
  font-size: 16px;
}

.post-location {
  margin-top: 10px;
  display: flex;
  align-items: center;
  gap: 5px;
  color: #1da1f2;
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
</style>
