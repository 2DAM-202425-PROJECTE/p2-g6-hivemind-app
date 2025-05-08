<template>
  <div class="user-media-container" :style="equippedBackgroundStyle">
    <Navbar />
    <div class="media-and-comments">
      <div class="media-column">
        <div v-if="!selectedMedia" class="no-media">
          <p>No media available</p>
        </div>
        <div v-else :id="`media-${selectedMedia.id}`" class="media-card">
          <div class="media-header">
            <div class="profile-section">
              <div class="media-profile-link" @click="goToUserProfile(getUsernameById(selectedMedia.id_user))">
                <img :src="getProfilePhotoById(selectedMedia.id_user)" class="profile-pic" alt="Profile" />
              </div>
              <div class="media-info">
                <strong class="media-username" :class="[
                  getNameEffectClass(selectedMedia.equipped_name_effect_path),
                  getProfileFontClass(selectedMedia.equipped_profile_font_path),
                  selectedMedia.equipped_name_effect_path ? 'effect-active' : ''
                ]" @click="goToUserProfile(getUsernameById(selectedMedia.id_user))">
                  {{ getUserNameById(selectedMedia.id_user) }}
                </strong>
                <p class="post-date">{{ formatDate(selectedMedia.created_at) }}</p>
                <span class="username-handle">@{{ getUsernameById(selectedMedia.id_user) }}</span>
              </div>
            </div>
            <div class="media-menu">
              <button @click.stop="toggleMediaMenu(selectedMedia.id)">
                <i class="mdi mdi-dots-vertical"></i>
              </button>
              <div v-if="mediaMenuVisible === selectedMedia.id" class="dropdown-menu">
                <ul>
                  <template v-if="isMediaFromUser(selectedMedia)">
                    <li @click.stop="editMedia(selectedMedia)">Edit</li>
                    <li @click.stop="openDeleteMediaDialog(selectedMedia.id)" :disabled="isDeleting">Delete</li>
                  </template>
                  <template v-else>
                    <li @click.stop="reportMedia(selectedMedia)">Report</li>
                  </template>
                </ul>
              </div>
            </div>
          </div>
          <div class="media-description">
            <h5 v-if="isVideo && selectedMedia.description">{{ selectedMedia.description }}</h5>
            <div v-else-if="selectedMedia.description" class="post-description" v-html="renderPostDescription(selectedMedia.description)"></div>
            <div v-if="selectedMedia.location" class="post-location">
              <i class="mdi mdi-earth location-icon"></i>
              <a :href="`https://www.google.com/maps?q=${encodeURIComponent(selectedMedia.location)}`" target="_blank"
                 class="location-link">
                {{ simplifyLocation(selectedMedia.location) }}
              </a>
            </div>
            <template v-if="selectedMedia.file_path && isVideo">
              <video :src="getImageUrl(selectedMedia.file_path)" alt="Media Video" class="media-content" controls />
            </template>
            <template v-else-if="selectedMedia.file_path">
              <img :src="getImageUrl(selectedMedia.file_path)" alt="Media Image" class="media-content" />
            </template>
          </div>
          <div class="media-actions">
            <div class="action-item" @click.stop="toggleLike(selectedMedia)">
              <i class="mdi" :class="selectedMedia.liked_by_user ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'"></i>
              <span>{{ selectedMedia.likes_count }} Likes</span>
            </div>
            <div class="action-item">
              <i class="mdi mdi-comment-outline"></i>
              <span>{{ comments.length }} Comments</span>
            </div>
            <div class="action-item" @click.stop="shareMedia(selectedMedia)">
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
                  <strong class="comment-username" :class="[
                    getNameEffectClass(comment.equipped_name_effect_path),
                    getProfileFontClass(comment.equipped_profile_font_path),
                    comment.equipped_name_effect_path ? 'effect-active' : ''
                  ]" @click="goToUserProfile(comment.user?.username)">
                    {{ comment.user?.name || 'Unknown User' }}
                  </strong>
                  <p>{{ comment.content }}</p>
                  <div class="comment-actions">
                    <button @click="toggleReplyInput(comment.id)" class="reply-btn">Reply</button>
                    <button v-if="comment.replies?.length" @click="toggleRepliesVisibility(comment.id)" class="view-replies-btn">
                      {{ areRepliesVisible(comment.id) ? 'Hide' : `View ${comment.replies.length} ${comment.replies.length === 1 ? 'Reply' : 'Replies'}` }}
                    </button>
                  </div>
                  <div v-if="replyInputVisible === comment.id" class="reply-input">
                    <input v-model="replies[comment.id]" placeholder="Write a reply..." @keyup.enter="addReply(comment.id)" />
                    <button @click="addReply(comment.id)">Post Reply</button>
                  </div>
                  <!-- Display Replies -->
                  <div v-if="areRepliesVisible(comment.id)" class="replies-list">
                    <div v-for="reply in comment.replies" :key="reply.id" class="reply">
                      <strong :class="[
                        getNameEffectClass(reply.equipped_name_effect_path),
                        getProfileFontClass(reply.equipped_profile_font_path),
                        reply.equipped_name_effect_path ? 'effect-active' : ''
                      ]">{{ reply.user?.name || 'Unknown User' }}</strong>
                      <p>{{ reply.content }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Media Dialog -->
    <v-dialog v-model="editMediaPopup" max-width="500">
      <v-card>
        <v-card-title>Edit {{ isVideo ? 'Video' : 'Post' }}</v-card-title>
        <v-card-text>
          <div v-if="selectedMedia && selectedMedia.file_path" class="current-media">
            <p>Current {{ isVideo ? 'Video' : 'Image' }}:</p>
            <img v-if="!isVideo" :src="getImageUrl(selectedMedia.file_path)" alt="Current media image"
                 style="max-width: 100%; max-height: 200px; margin-bottom: 10px;" />
            <video v-else :src="getImageUrl(selectedMedia.file_path)" controls
                   style="max-width: 100%; max-height: 200px; margin-bottom: 10px;"></video>
          </div>
          <v-file-input :label="`Replace ${isVideo ? 'Video (.mp4, .mov)' : 'Image/Video (.png, .jpg, .jpeg, .mp4)'}`"
                        :accept="isVideo ? '.mp4, .mov' : '.png, .jpg, .jpeg, .mp4'" @update:modelValue="handleEditFileUpload"
                        outlined></v-file-input>
          <v-text-field v-model="editMediaDescription" label="Description" outlined></v-text-field>
          <div v-if="editMediaLocation" class="location-preview">
            <i class="mdi mdi-map-marker"></i>
            <a :href="`https://www.google.com/maps?q=${editMediaLocation.lat},${editMediaLocation.lon}`" target="_blank"
               class="location-btn">
              {{ editMediaLocation.name }}
            </a>
            <button class="remove-btn" @click="removeEditLocation">Remove</button>
          </div>
          <button v-if="!editMediaLocation" class="action-btn" @click="getEditLocation" title="Add Location">
            <i class="mdi mdi-map-marker-outline"></i> Add Location
          </button>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="cancelEditMedia">Cancel</v-btn>
          <v-btn class="btn-primary" @click="saveEditMedia">Update {{ isVideo ? 'Video' : 'Post' }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title class="headline">Confirm Deletion</v-card-title>
        <v-card-text>
          Are you sure you want to delete this {{ deleteType === 'media' ? (isVideo ? 'video' : 'post') : 'comment' }}?
          This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="cancelDelete">Cancel</v-btn>
          <v-btn class="btn-primary" @click="confirmDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Replies Popup -->
    <v-dialog v-model="repliesPopupVisible" max-width="500">
      <v-card>
        <v-card-title>Replies</v-card-title>
        <v-card-text>
          <div v-if="selectedCommentReplies.length === 0" class="no-replies">
            <p>No replies yet</p>
          </div>
          <div v-else class="replies-list">
            <div v-for="reply in selectedCommentReplies" :key="reply.id" class="reply">
              <strong :class="[
                getNameEffectClass(reply.equipped_name_effect_path),
                getProfileFontClass(reply.equipped_profile_font_path),
                reply.equipped_name_effect_path ? 'effect-active' : ''
              ]">{{ reply.user?.name || 'Unknown User' }}</strong>
              <p>{{ reply.content }}</p>
            </div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="closeRepliesPopup">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import { generateAvatar } from '@/utils/avatar';
import apiClient from "@/axios.js";

const route = useRoute();
const router = useRouter();
const username = route.params.username;
const user = ref({});
const selectedMedia = ref(null);
const comments = ref([]);
const newComment = ref('');
const replyInputVisible = ref(null);
const replies = ref({});
const repliesPopupVisible = ref(false);
const selectedCommentReplies = ref([]);
const isDeleting = ref(false);
const mediaMenuVisible = ref(null);
const editMediaPopup = ref(false);
const editMediaDescription = ref('');
const editMediaLocation = ref(null);
const editMediaFile = ref(null);
const currentUser = ref({});
const users = ref({});
const shares = ref(0);
const deleteDialog = ref(false);
const deleteType = ref('');
const itemToDelete = ref(null);
const previousRoute = ref('');
const visibleReplies = ref({});

const API_BASE_URL = 'http://localhost:8000';
const VIDEO_EXTENSIONS = ['.mp4', '.mov'];

// Load cached background path from localStorage (if available)
const cachedBackgroundPath = ref(localStorage.getItem('equipped_background_path') || null);

// Computed property for background style
const equippedBackgroundStyle = computed(() => {
  const bgPath = currentUser.value.equipped_background_path || cachedBackgroundPath.value;
  return bgPath
    ? { backgroundImage: `url(${bgPath})`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed' }
    : { background: 'linear-gradient(to bottom right, #FEFCE8, #FDE68A)' };
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

const isVideo = computed(() => {
  return selectedMedia.value?.file_path && VIDEO_EXTENSIONS.some(ext => selectedMedia.value.file_path.toLowerCase().endsWith(ext));
});

const toggleReplyInput = (commentId) => {
  replyInputVisible.value = replyInputVisible.value === commentId ? null : commentId;
};

const addReply = async (parentId) => {
  if (!replies.value[parentId]?.trim()) return;

  try {
    const response = await apiClient.post(`/api/posts/${selectedMedia.value.id}/comments`, {
      content: replies.value[parentId],
      parent_id: parentId,
    });

    const parentComment = comments.value.find(comment => comment.id === parentId);
    if (parentComment) {
      parentComment.replies = parentComment.replies || [];
      parentComment.replies.push(response.data);
    }

    replies.value[parentId] = '';
    replyInputVisible.value = null;
  } catch (error) {
    console.error('Error adding reply:', error.response?.data || error.message);
  }
};

const toggleRepliesVisibility = (commentId) => {
  visibleReplies.value[commentId] = !visibleReplies.value[commentId];
};

const areRepliesVisible = (commentId) => {
  return visibleReplies.value[commentId] || false;
};

const openRepliesPopup = (comment) => {
  selectedCommentReplies.value = comment.replies || [];
  repliesPopupVisible.value = true;
};

const closeRepliesPopup = () => {
  repliesPopupVisible.value = false;
  selectedCommentReplies.value = [];
};

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.error('No token found, redirecting to login');
      router.push('/login');
      return;
    }

    await fetchUserData();

    previousRoute.value = document.referrer.includes(window.location.origin)
      ? new URL(document.referrer).pathname
      : '/';

    const usersResult = await apiClient.get(`/api/users`);
    users.value = usersResult.data.data.reduce((acc, user) => {
      acc[user.id] = { name: user.name, username: user.username, profile_photo_path: user.profile_photo_path };
      return acc;
    }, {});

    console.log('Fetching user data for username:', username);
    const userResult = await apiClient.get(`/api/user/${username}`);
    console.log('User data:', userResult.data);
    user.value = userResult.data.user;

    const postId = route.query.postId;
    if (!postId) {
      console.error('No postId provided in query');
      return;
    }
    console.log('Fetching media with ID:', postId);
    const postResult = await apiClient.get(`/api/posts/${postId}`);
    console.log('Media data:', postResult.data);
    const postData = postResult.data.data;

    // Fetch equipped items for the post's user
    const equippedItemsResult = await apiClient.get(`/api/user/${postData.id_user}/equipped-items`);
    selectedMedia.value = {
      ...postData,
      equipped_name_effect_path: equippedItemsResult.data.equipped_name_effect_path,
      equipped_profile_font_path: equippedItemsResult.data.equipped_profile_font_path,
    };

    console.log('Fetching comments for media ID:', postId);
    const commentsResult = await apiClient.get(`/api/posts/${postId}/comments`);
    console.log('Comments data:', commentsResult.data);
    comments.value = await Promise.all(
      commentsResult.data.map(async (comment) => {
        const equippedItems = await apiClient.get(`/api/user/${comment.user.id}/equipped-items`);
        const repliesWithEquippedItems = await Promise.all(
          (comment.replies || []).map(async (reply) => {
            const replyEquippedItems = await apiClient.get(`/api/user/${reply.user.id}/equipped-items`);
            return {
              ...reply,
              equipped_name_effect_path: replyEquippedItems.data.equipped_name_effect_path,
              equipped_profile_font_path: replyEquippedItems.data.equipped_profile_font_path,
            };
          })
        );
        return {
          ...comment,
          equipped_name_effect_path: equippedItems.data.equipped_name_effect_path,
          equipped_profile_font_path: equippedItems.data.equipped_profile_font_path,
          replies: repliesWithEquippedItems,
        };
      })
    );

    await nextTick();
    const mediaElement = document.getElementById(`media-${postId}`);
    if (mediaElement) {
      mediaElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      mediaElement.classList.add('highlight');
      setTimeout(() => mediaElement.classList.remove('highlight'), 2000);
    } else {
      console.warn(`Media element with ID media-${postId} not found`);
    }
  } catch (error) {
    console.error('Error fetching media or comments:', error.response?.data || error.message);
    console.error('Failed request:', error.config);
    selectedMedia.value = null;
  }
});

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

const getCommentUserPhoto = (user) => {
  if (!user || !user.id) {
    return generateAvatar('Unknown User');
  }
  const userData = users.value[user.id];
  if (userData?.profile_photo_path) {
    if (userData.profile_photo_path.startsWith('http://') || userData.profile_photo_path.startsWith('https://')) {
      return userData.profile_photo_path;
    }
    return `${API_BASE_URL}/storage/${userData.profile_photo_path}`;
  }
  return generateAvatar(userData?.name || user.name || 'Unknown User');
};

const getUserNameById = (id) => users.value[id]?.name || 'Unknown User';
const getUsernameById = (id) => users.value[id]?.username || null;
const isMediaFromUser = (media) => media.id_user === currentUser.value.id;

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

const goToUserProfile = (username) => {
  console.log('Navigating to profile with username:', username);
  if (username) {
    router.push(`/profile/${username}`);
  } else {
    console.warn('No username available for profile navigation');
  }
};

const toggleLike = async (media) => {
  try {
    if (media.liked_by_user) {
      await apiClient.delete(`/api/posts/${media.id}/like`);
      media.liked_by_user = false;
      media.likes_count -= 1;
    } else {
      await apiClient.post(`/api/posts/${media.id}/like`);
      media.liked_by_user = true;
      media.likes_count += 1;
    }
  } catch (error) {
    console.error('Error toggling like:', error.response?.data || error.message);
    const postResult = await apiClient.get(`/api/posts/${media.id}`);
    media.liked_by_user = postResult.data.data.liked_by_user;
    media.likes_count = postResult.data.data.likes_count;
  }
};

const addComment = async () => {
  if (!newComment.value.trim()) return;
  try {
    const response = await apiClient.post(`/api/posts/${selectedMedia.value.id}/comments`, {
      content: newComment.value
    });
    const equippedItems = await apiClient.get(`/api/user/${response.data.user.id}/equipped-items`);
    comments.value.push({
      ...response.data,
      equipped_name_effect_path: equippedItems.data.equipped_name_effect_path,
      equipped_profile_font_path: equippedItems.data.equipped_profile_font_path,
    });
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

const openDeleteMediaDialog = (mediaId) => {
  deleteType.value = 'media';
  itemToDelete.value = mediaId;
  deleteDialog.value = true;
};

const cancelDelete = () => {
  deleteDialog.value = false;
  itemToDelete.value = null;
  deleteType.value = '';
};

const confirmDelete = async () => {
  try {
    if (deleteType.value === 'media') {
      isDeleting.value = true;
      await apiClient.delete(`/api/posts/${itemToDelete.value}`);
      selectedMedia.value = null;
      router.push(previousRoute.value || '/');
    } else if (deleteType.value === 'comment') {
      await apiClient.delete(`/api/comments/${itemToDelete.value}`);
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

const toggleMediaMenu = (mediaId) => {
  mediaMenuVisible.value = mediaMenuVisible.value === mediaId ? null : mediaId;
};

const editMedia = (media) => {
  editMediaDescription.value = media.description || '';
  editMediaLocation.value = media.location ? { name: media.location, lat: null, lon: null } : null;
  editMediaFile.value = null;
  editMediaPopup.value = true;
};

const getEditLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const { latitude, longitude } = position.coords;
        try {
          const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`);
          const data = await response.json();
          editMediaLocation.value = {
            name: data.display_name || `Lat: ${latitude}, Lon: ${longitude}`,
            lat: latitude,
            lon: longitude
          };
        } catch (error) {
          console.error('Error fetching location name:', error);
          editMediaLocation.value = {
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
  editMediaLocation.value = null;
};

const handleEditFileUpload = (files) => {
  editMediaFile.value = files ? files[0] : null;
};

const cancelEditMedia = () => {
  editMediaPopup.value = false;
  editMediaDescription.value = '';
  editMediaLocation.value = null;
  editMediaFile.value = null;
};

const saveEditMedia = async () => {
  if (!selectedMedia.value) return;

  try {
    const formData = new FormData();
    formData.append('description', editMediaDescription.value);
    formData.append('location', editMediaLocation.value ? editMediaLocation.value.name : '');
    formData.append('_method', 'PUT');
    if (editMediaFile.value) {
      formData.append('file', editMediaFile.value);
    }

    const response = await apiClient.post(`/api/posts/${selectedMedia.value.id}`, formData);
    const updatedPost = response.data.post || response.data.data;

    // Fetch equipped items for the updated post
    const equippedItemsResult = await apiClient.get(`/api/user/${updatedPost.id_user}/equipped-items`);
    selectedMedia.value = {
      ...updatedPost,
      equipped_name_effect_path: equippedItemsResult.data.equipped_name_effect_path,
      equipped_profile_font_path: equippedItemsResult.data.equipped_profile_font_path,
    };
    editMediaPopup.value = false;
  } catch (error) {
    console.error('Error updating media:', error);
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const reportMedia = async (media) => {
  try {
    await apiClient.post(`/api/posts/${media.id}/report`, {
      reason: prompt('Please enter reason for reporting:')
    });
    alert(`${isVideo.value ? 'Video' : 'Post'} reported successfully`);
    mediaMenuVisible.value = null;
  } catch (error) {
    console.error('Error reporting media:', error);
  }
};

const shareMedia = (media) => {
  const shareUrl = `${window.location.origin}/users/${username}/media?postId=${media.id}`;
  navigator.clipboard.writeText(shareUrl)
    .then(() => {
      shares.value++;
      alert(`${isVideo.value ? 'Video' : 'Post'} URL copied to clipboard!`);
    })
    .catch(err => console.error('Error copying URL:', err));
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
@import '@/styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

.user-media-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  padding-bottom: 60px;
  background: linear-gradient(to bottom right, #FEFCE8, #FDE68A);
  min-height: 100vh;
  color: #000000;
}

h1 {
  font-size: 24px;
  padding-bottom: 20px;
  text-align: center;
}

.media-and-comments {
  display: flex;
  max-width: 800px;
  margin: 0 auto;
}

.media-column {
  flex: 2;
  margin-right: 20px;
}

.media-card {
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  padding: 20px;
  width: 100%;
}

.media-card.highlight {
  animation: highlight 2s ease-out;
}

.media-header {
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

.media-profile-link {
  cursor: pointer;
}

.profile-pic {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #FEFCE8;
}

.media-info {
  display: flex;
  flex-direction: column;
}

.media-username {
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  color: #000000;
}

.media-username:hover {
  text-decoration: underline;
}

.post-date {
  font-size: 12px;
  color: #000000;
}

.username-handle {
  font-size: 14px;
  color: #000000;
}

.media-description {
  margin-bottom: 15px;
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

.action-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  color: #000000;
  display: flex;
  align-items: center;
  gap: 5px;
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
  background: linear-gradient(to right, #555555, #333333);
  color: #000000;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(to right, #333333, #1a1a1a);
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(85, 85, 85, 0.3);
}

.media-content {
  width: 100%;
  max-height: 600px;
  border-radius: 20px;
  margin-bottom: 15px;
  object-fit: contain;
}

.media-menu {
  position: relative;
}

.media-menu button {
  background: none;
  border: none;
  cursor: pointer;
  color: #000000;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: #FEFCE8;
  border: 1px solid #555555;
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
  color: #000000;
}

.dropdown-menu li:hover {
  background: #FDE68A;
}

.media-actions {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  margin-bottom: 20px;
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

.comments-section {
  margin-top: 20px;
  padding: 15px;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.comments-section h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #000000;
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
  border: 2px solid #FEFCE8;
}

.comment-content {
  flex: 1;
}

.comment-username {
  font-size: 16px;
  cursor: pointer;
  color: #000000;
}

.comment-username:hover {
  text-decoration: underline;
}

.comment-content p {
  margin: 5px 0 0;
  font-size: 14px;
  color: #000000;
}

.comment-actions {
  display: flex;
  gap: 10px;
  margin-top: 5px;
}

.reply-btn,
.view-replies-btn {
  background: none;
  border: none;
  color: #000000;
  cursor: pointer;
  font-size: 14px;
}

.reply-btn:hover,
.view-replies-btn:hover {
  color: #333333;
}

.no-comments {
  text-align: center;
  color: #000000;
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
  color: #000000;
}

.comment-input button {
  padding: 10px 20px;
  background: linear-gradient(to right, #555555, #333333);
  color: #000000;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.comment-input button:hover {
  background: linear-gradient(to right, #333333, #1a1a1a);
}

.replies-list {
  margin-top: 10px;
  padding-left: 20px;
  border-left: 2px solid #ddd;
}

.reply {
  margin-top: 10px;
}

.reply-input {
  margin-top: 10px;
  display: flex;
  gap: 10px;
}

.reply-input input {
  flex: 1;
  padding: 5px;
  border: 1px solid #ddd;
  border-radius: 5px;
  color: #000000;
}

.reply-input button {
  padding: 5px 10px;
  background: linear-gradient(to right, #555555, #333333);
  color: #000000;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.reply-input button:hover {
  background: linear-gradient(to right, #333333, #1a1a1a);
}

.no-replies {
  text-align: center;
  color: #000000;
  padding: 10px 0;
}

.no-media {
  text-align: center;
  color: #000000;
  padding: 20px;
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
