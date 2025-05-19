<template>
  <div class="home-container" :style="equippedBackgroundStyle">
    <Navbar />
    <StoriesBar :stories="stories.data" />

    <!-- Post Input Section -->
    <div class="post-section">
      <div class="post-input-container">
        <img :src="getProfilePhotoById(currentUser.id)" class="profile-pic" alt="Profile" />
        <div class="post-input-wrapper">
          <textarea v-model="newPostContent" :placeholder="placeholderText" class="post-input" rows="2"
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
            <button class="btn-primary" :disabled="!newPostContent && !newPostFile && !selectedLocation"
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
              <strong class="post-username" :class="[
                getNameEffectClass(post.equipped_name_effect_path),
                getProfileFontClass(post.equipped_profile_font_path),
                post.equipped_name_effect_path ? 'effect-active' : ''
              ]" @click.stop="goToUserProfile(post.id_user)">
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
        <div class="bg-white rounded-lg p-6 w-full max-w-md" @click.stop>
          <h3 class="text-lg font-bold mb-4">Edit Post</h3>
          <div v-if="selectedPost && selectedPost.file_path" class="mb-4">
            <p class="text-sm">Current File:</p>
            <img v-if="!selectedPost.file_path.includes('.mp4')" :src="getImageUrl(selectedPost.file_path)"
                 alt="Current post image" class="max-w-full h-auto max-h-48 mb-2" />
            <video v-else :src="getImageUrl(selectedPost.file_path)" controls
                   class="max-w-full h-auto max-h-48 mb-2"></video>
          </div>
          <label class="block mb-4">
            <span class="sr-only">Choose file</span>
            <input type="file" accept=".png, .jpg, .jpeg, .mp4" @change="handleEditFileUpload" @click.stop class="block w-full text-sm
          file:mr-4 file:py-2 file:px-4
          file:rounded file:border-0
          file:text-sm file:font-semibold
          file:bg-gray-500 file:text-white
          hover:file:bg-gray-600" />
          </label>
          <input v-model="editPostDescription" type="text" placeholder="Description"
                 class="w-full p-2 border rounded mb-4" />
          <div class="flex justify-end gap-2">
            <button @click="cancelEditPost"
                    class="px-4 py-2 hover:text-gray-800">
              Cancel
            </button>
            <button @click="saveEditPost" class="btn-primary">
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
        <div class="action-item" @click.stop="navigateToPost(post, $event)">
          <i class="mdi mdi-comment-outline"></i>
          <span>{{ post.comments ? post.comments.length : 0 }} Comments</span>
        </div>
        <div class="action-item" @click.stop="sharePost(post)">
          <i class="mdi mdi-share-outline"></i>
          <span>Share</span>
        </div>
      </div>
    </div>

    <!-- Share Popup -->
    <v-dialog v-model="sharePopup" max-width="400">
      <v-card>
        <v-card-title class="headline">Share Post</v-card-title>
        <v-card-text>
          <p>Copy the link below to share this post:</p>
          <div class="share-url-container">
            <input type="text" :value="shareUrl" readonly class="share-url-input" />
            <button class="btn-white" @click="copyToClipboard">
              <i class="mdi mdi-content-copy"></i> Copy
            </button>
          </div>
          <p v-if="copySuccess" class="copy-success">Link copied to clipboard!</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-white" @click="closeSharePopup">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Report Popup -->
    <v-dialog v-model="reportPopup" max-width="400">
      <v-card>
        <v-card-title class="headline">Post Reported</v-card-title>
        <v-card-text>
          <p>{{ reportMessage }}</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-white" @click="closeReportPopup">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

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
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import StoriesBar from '@/components/StoriesBar.vue';
import { generateAvatar } from '@/utils/avatar';
import VEmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';
import apiClient from "@/axios.js";

// Initialize refs
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
const newPostContent = ref('');
const newPostFile = ref(null);
const previewUrl = ref(null);
const showEmojiPicker = ref(false);
const selectedLocation = ref(null);
const sharePopup = ref(false);
const shareUrl = ref('');
const copySuccess = ref(false);
const reportPopup = ref(false); // New ref for report popup
const reportedPost = ref(null); // New ref for reported post

// Random placeholder messages
const placeholderMessages = [
  "What's happening?",
  "How's it going?",
  "What's on your mind?",
  "Share something cool!",
  "What's up?",
  "Got any updates?",
  "Tell us something new!",
  "What's the vibe today?"
];
const placeholderText = ref('');

// Select random placeholder on mount
onMounted(() => {
  placeholderText.value = placeholderMessages[Math.floor(Math.random() * placeholderMessages.length)];
});

const currentUser = ref({
  id: null,
  name: 'Current User',
  profile_photo_path: null,
  equipped_background_path: null,
});

// Load cached background path from localStorage (if available)
const cachedBackgroundPath = ref(localStorage.getItem('equipped_background_path') || null);

// Fetch user data immediately on mount
const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token available');
      return;
    }

    // Fetch current user data
    const userResult = await apiClient.get('/api/user');
    currentUser.value = userResult.data;

    // Fetch equipped items to get equipped_background_path
    const equippedItemsResult = await apiClient.get(`/api/user/${currentUser.value.id}/equipped-items`);
    currentUser.value.equipped_background_path = equippedItemsResult.data.equipped_background_path;
    console.log('Equipped background path:', currentUser.value.equipped_background_path);

    // Cache the background path in localStorage
    if (currentUser.value.equipped_background_path) {
      localStorage.setItem('equipped_background_path', currentUser.value.equipped_background_path);
    } else {
      localStorage.removeItem('equipped_background_path');
    }
  } catch (error) {
    console.error('Error fetching user data:', error.response?.data || error.message);
  }
};

// Fetch posts and other data
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

    const postsData = result.data.data.map(async (post) => {
      try {
        const equippedItemsResult = await apiClient.get(`/api/user/${post.id_user}/equipped-items`);
        return {
          ...post,
          equipped_name_effect_path: equippedItemsResult.data.equipped_name_effect_path,
          equipped_profile_font_path: equippedItemsResult.data.equipped_profile_font_path,
          shares: post.shares || 0,
        };
      } catch (error) {
        console.error(`Error fetching equipped items for user ${post.id_user}:`, error);
        return { ...post, shares: post.shares || 0 };
      }
    });

    const resolvedPosts = await Promise.all(postsData);

    if (initialLoad) {
      posts.value = resolvedPosts;
    } else {
      posts.value = [...posts.value, ...resolvedPosts];
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

    const storiesResult = await apiClient.get('/api/stories');
    stories.value = storiesResult.data;
  } catch (error) {
    console.error('Error al obtener datos', error.response?.data || error.message);
  } finally {
    loading.value = false;
  }
};

// Computed property for background style
const equippedBackgroundStyle = computed(() => {
  const bgPath = currentUser.value.equipped_background_path || cachedBackgroundPath.value;
  return bgPath
    ? { backgroundImage: `url(${bgPath})`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed' }
    : {};
});

// Computed property for report message
const reportMessage = computed(() => {
  if (!reportedPost.value) return '';
  const username = getUserNameById(reportedPost.value.id_user);
  return `${username}'s post has been reported to HiveMind's admins.`;
});

// Mount and unmount lifecycle hooks
onMounted(async () => {
  await fetchUserData();
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
  if (typeof emoji === 'string') {
    newPostContent.value += emoji;
  } else if (emoji && emoji.i) {
    newPostContent.value += emoji.i;
  } else if (emoji && emoji.emoji) {
    newPostContent.value += emoji.emoji;
  } else if (emoji && emoji.code) {
    newPostContent.value += emoji.code;
  } else {
    newPostContent.value += '❓';
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

const submitPost = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token available');
      return;
    }

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

    await apiClient.post('/api/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

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

const getNameEffectClass = (nameEffectPath) => {
  if (!nameEffectPath) return '';
  const effectMap = {
    'Gradient Fade': 'gradient-fade',
    'Golden Outline': 'gradient-fade',
    'Dark Pulse': 'dark-pulse',
    'Cosmic Shine': 'cosmic-shine',
    'Neon Edge': 'neon-edge',
    'Frost Glow': 'frost-glow',
    'Fire Flicker': 'fire-flicker',
    'Emerald Sheen': 'emerald-sheen',
    'Phantom Haze': 'phantom-haze',
    'Electric Glow': 'electric-glow',
    'Solar Flare': 'solar-flare',
    'Wave Shimmer': 'wave-shimmer',
    'Crystal Pulse': 'crystal-pulse',
    'Mystic Aura': 'mystic-aura',
    'Shadow Veil': 'shadow-veil',
    'Digital Pulse': 'digital-pulse',
  };
  return effectMap[nameEffectPath] || '';
};

const getProfileFontClass = (fontPath) => {
  if (!fontPath) return '';
  switch (fontPath) {
    case 'Pixel Art': return 'font-pixel-art';
    case 'Comic Sans': return 'font-comic-sans';
    case 'Gothic': return 'font-gothic';
    case 'Cursive': return 'font-cursive';
    case 'Typewriter': return 'font-typewriter';
    case 'Bubble': return 'font-bubble';
    case 'Neon': return 'font-neon';
    case 'Graffiti': return 'font-graffiti';
    case 'Retro': return 'font-retro';
    case 'Cyberpunk': return 'font-cyberpunk';
    case 'Western': return 'font-western';
    case 'Chalkboard': return 'font-chalkboard';
    case 'Horror': return 'font-horror';
    case 'Futuristic': return 'font-futuristic';
    case 'Handwritten': return 'font-handwritten';
    case 'Bold Script': return 'font-bold-script';
    default: return '';
  }
};

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

    await apiClient.post(`/api/posts/${selectedPost.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    await fetchPosts(1, true);

    editPostPopup.value = false;
    editPostDescription.value = '';
    editPostFile.value = null;
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

const reportPost = (post) => {
  reportedPost.value = post;
  reportPopup.value = true;
};

const closeReportPopup = () => {
  reportPopup.value = false;
  reportedPost.value = null;
};

const sharePost = (post) => {
  const username = getUsernameById(post.id_user);
  shareUrl.value = `${window.location.origin}/users/${username}/media?postId=${post.id}`;
  sharePopup.value = true;
  copySuccess.value = false;
  post.shares = (post.shares || 0) + 1;
};

const copyToClipboard = () => {
  navigator.clipboard.writeText(shareUrl.value)
    .then(() => {
      copySuccess.value = true;
      setTimeout(() => {
        closeSharePopup();
      }, 2000);
    })
    .catch(err => {
      console.error('Error copying URL:', err);
      alert('Failed to copy URL');
    });
};

const closeSharePopup = () => {
  sharePopup.value = false;
  shareUrl.value = '';
  copySuccess.value = false;
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
@import '../styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

.home-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  background: linear-gradient(to bottom right, #FEFCE8, #FDE68A);
  min-height: 100vh;
  color: #000000;
  width: 100%;
  position: relative;
}

h1 {
  font-size: 24px;
  padding-bottom: 20px;
}

.post-section {
  max-width: 800px;
  margin: 0 auto 20px auto;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
  color: #000000;
}

.post-input::placeholder {
  color: #555555;
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
  background: #FEFCE8;
  border-radius: 5px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.location-preview i {
  color: #000000;
}

.location-btn {
  display: inline-block;
  padding: 5px 10px;
  background-color: #555555;
  color: #000000;
  text-decoration: none;
  border-radius: 5px;
  font-size: 14px;
}

.location-btn:hover {
  background-color: #333333;
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
  color: #000000;
}

.action-btn i {
  font-size: 20px;
}

.action-btn:hover {
  color: #333333;
}

.btn-primary {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
  background: #ffffff;
  color: #000000;
  border: 1px solid #555555;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover:not(:disabled) {
  background: #f5f5f5;
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
}

.btn-primary:disabled {
  background: #D1D5DB;
  color: #999999;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-white {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
  background: #ffffff;
  color: #000000;
  border: 1px solid #555555;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-white:hover:not(:disabled) {
  background: #f5f5f5;
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
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
  color: #000000;
}

.post-location {
  margin-top: 10px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.location-icon {
  color: #000000;
  font-size: 16px;
}

.location-link {
  color: #000000;
  text-decoration: none;
  font-size: 14px;
}

.location-link:hover {
  text-decoration: underline;
}

.post-card {
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
  margin-bottom: 20px;
  width: 100%;
  cursor: pointer;
}

.post-card:hover {
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
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
  border: 2px solid #FEFCE8;
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
  color: #000000;
}

.post-username:hover {
  text-decoration: underline;
}

.post-date {
  font-size: 12px;
  color: #000000;
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
  color: #000000;
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 5px);
  right: 0;
  background: #FEFCE8;
  border: 1px solid #555555;
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
  color: #000000;
}

.dropdown-menu li:hover {
  background: #FDE68A;
}

.post-actions {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: #000000;
}

.action-item {
  display: flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
}

.action-item i {
  font-size: 18px;
  color: #000000;
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
  color: #000000;
}

.spinner {
  border: 3px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 3px solid #555555;
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
  color: #000000;
  font-style: italic;
}

.share-url-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
}

.share-url-input {
  flex-grow: 1;
  padding: 8px;
  border: 1px solid #555555;
  border-radius: 5px;
  font-size: 14px;
  background: #FEFCE8;
  color: #000000;
}

.copy-success {
  color: #4caf50;
  font-size: 14px;
  margin-top: 10px;
}

.effect-active {
  display: inline-block;
  padding: 0 0.25rem;
}

.font-pixel-art { font-family: 'Press Start 2P', cursive; font-size: 16px; }
.font-comic-sans { font-family: 'Comic Neue', cursive; font-size: 16px; }
.font-gothic { font-family: 'Black Ops One', cursive; font-size: 16px; }
.font-cursive { font-family: 'Dancing Script', cursive; font-size: 16px; }
.font-typewriter { font-family: 'Courier Prime', monospace; font-size: 16px; }
.font-bubble { font-family: 'Bungee', cursive; font-size: 16px; }
.font-neon { font-family: 'Orbitron', sans-serif; font-size: 16px; }
.font-graffiti { font-family: 'Wallpoet', cursive; font-size: 16px; }
.font-retro { font-family: 'VT323', monospace; font-size: 16px; }
.font-cyberpunk { font-family: 'Monoton', cursive; font-size: 16px; }
.font-western { font-family: 'Special Elite', cursive; font-size: 16px; }
.font-chalkboard { font-family: 'Creepster', cursive; font-size: 16px; }
.font-horror { font-family: 'Creepster', cursive; font-size: 16px; }
.font-futuristic { font-family: 'Audiowide', cursive; font-size: 16px; }
.font-handwritten { font-family: 'Caveat', cursive; font-size: 16px; }
.font-bold-script { font-family: 'Permanent Marker', cursive; font-size: 16px; }
</style>
