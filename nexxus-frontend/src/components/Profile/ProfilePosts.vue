<template>
  <div>
    <p v-if="sortedPosts.length === 0" class="text-gray-500 text-center">No posts yet</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="post in sortedPosts" :key="post.id" @click="viewPost(post.id)"
           class="relative bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer">
        <!-- Fixed height container for consistency -->
        <div class="w-full h-48 flex items-center justify-center">
          <img
            v-if="post.thumbnail_path"
            :src="getThumbnailUrl(post.thumbnail_path)"
            alt="thumbnail"
            class="object-cover w-full h-full"
          />
          <img
            v-else-if="post.file_path"
            :src="getImageUrl(post.file_path)"
            alt="post image"
            class="object-cover w-full h-full"
          />
          <p
            v-else
            class="text-gray-800 dark:text-gray-200 text-center px-4"
          >
            {{ post.description || 'No description' }}
          </p>
        </div>
        <!-- Botón de tres puntos -->
        <div class="absolute top-2 right-2" @click.stop>
          <button @click="toggleMenu(post.id)"
                  class="p-1 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white">
            <i class="mdi mdi-dots-vertical"></i>
          </button>
          <!-- Menú desplegable -->
          <div v-if="activeMenu === post.id"
               class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
              <li v-if="isCurrentUser" @click.stop="editPost(post)"
                  class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                Edit
              </li>
              <li v-if="isCurrentUser" @click.stop="deletePost(post.id)"
                  class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                Delete
              </li>
              <li v-if="!isCurrentUser" @click.stop="reportPost(post)"
                  class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                Report
              </li>
            </ul>
          </div>
        </div>
        <!-- Descripción del post (shown below image if image exists) -->
        <div v-if="post.file_path" class="p-4">
          <p class="text-gray-800 dark:text-gray-200">{{ post.description || 'No description' }}</p>
        </div>
      </div>
    </div>

    <!-- Popup de edición -->
    <div v-if="showEditPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Edit Post</h3>
        <!-- Imagen actual (only if it exists) -->
        <div v-if="selectedPost && selectedPost.file_path" class="mb-4">
          <p class="text-sm text-gray-600 dark:text-gray-300">Current Image:</p>
          <img :src="getImageUrl(selectedPost.file_path)" alt="Current post image"
               class="max-w-full h-auto max-h-48 mb-2" />
        </div>
        <!-- Input para reemplazar imagen -->
        <input type="file" accept=".png, .jpg, .jpeg, .mp4" @change="handleEditFileUpload"
               class="mb-4 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600" />
        <!-- Campo de descripción -->
        <input v-model="editPostDescription" type="text" placeholder="Description"
               class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:text-white" />
        <!-- Botones de acción -->
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
  </div>
</template>

<script setup>
import { ref, defineProps, watch, onMounted, onBeforeUnmount, computed } from 'vue';
import { useRouter } from 'vue-router';
import { generateAvatar } from "@/utils/avatar.js";
import apiClient from "@/axios.js";

const router = useRouter();
const props = defineProps({
  userPosts: {
    type: Array,
    required: true,
  },
  isCurrentUser: {
    type: Boolean,
    required: true,
  },
});

// Computed property to sort posts by created_at or id (newest first)
const sortedPosts = computed(() => {
  return [...props.userPosts].sort((a, b) => {
    // Prefer created_at if available
    if (a.created_at && b.created_at) {
      return new Date(b.created_at) - new Date(a.created_at);
    }
    // Fallback to id (assuming higher ID means newer post)
    return b.id - a.id;
  });
});

const activeMenu = ref(null);
const isDeleting = ref(false);
const showEditPopup = ref(false);
const selectedPost = ref(null);
const editPostDescription = ref('');
const editPostFile = ref(null);

const toggleMenu = (postId) => {
  console.log('Toggle menu for post:', postId);
  activeMenu.value = activeMenu.value === postId ? null : postId;
};

const handleClickOutside = (event) => {
  if (!event.target.closest('.mdi-dots-vertical') && !event.target.closest('.absolute.right-0.mt-2')) {
    activeMenu.value = null;
  }
};

onMounted(() => {
  console.log('ProfilePosts mounted with userPosts:', props.userPosts);
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

const getImageUrl = (path) => {
  if (!path) return generateAvatar('User');
  // Si ya es una URL completa, devolverla tal cual
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  // Si es una ruta local, añadir el prefijo de storage
  return `http://localhost:8000/storage/${path}`;
};

const getThumbnailUrl = (thumbnailPath) => {
  if (!thumbnailPath) return null; // Return null if no thumbnail exists
  return `http://localhost:8000/storage/${thumbnailPath}`;
};

const viewPost = async (postId) => {
  console.log('viewPost triggered for postId:', postId);
  try {
    const token = localStorage.getItem('token');
    const post = props.userPosts.find(p => p.id === postId);
    if (!post) {
      console.error('Post not found:', postId);
      return;
    }

    let username = post.username;
    console.log('Post data:', post);

    if (!username) {
      const currentUsername = router.currentRoute.value.params.username;
      if (currentUsername) {
        username = currentUsername;
      } else {
        const userResponse = await apiClient.get(`/api/users/id/${post.id_user}`);
        username = userResponse.data.username;
      }
    }

    console.log('Navigating to UserMediaPage with:', { username, postId });
    if (username) {
      router.push({
        path: `/users/${username}/media`,
        query: { postId: postId }
      });
    } else {
      console.error('Username not found for post:', postId);
    }
  } catch (error) {
    console.error('Error in viewPost:', error.response?.data || error.message);
  }
};

watch(() => props.userPosts, (newPosts) => {
  console.log('userPosts updated in ProfilePosts.vue:', newPosts);
}, { immediate: true });

const deletePost = async (postId) => {
  isDeleting.value = true;
  try {
    await apiClient.delete(`/api/posts/${postId}`);

    if (Array.isArray(props.userPosts)) {
      const index = props.userPosts.findIndex(post => post.id === postId);
      if (index !== -1) {
        props.userPosts.splice(index, 1);
      }
    }
  } catch (error) {
    console.error('Error deleting post:', error.response?.data || error.message);
    alert('Failed to delete post. ' + (error.response?.data?.message || error.message));
  } finally {
    isDeleting.value = false;
  }
};

const editPost = (post) => {
  selectedPost.value = post;
  editPostDescription.value = post.description || '';
  editPostFile.value = null;
  showEditPopup.value = true;
};

const handleEditFileUpload = (event) => {
  editPostFile.value = event.target.files ? event.target.files[0] : null;
};

const cancelEditPost = () => {
  showEditPopup.value = false;
  editPostDescription.value = '';
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

    const response = await apiClient.post(`/api/posts/${selectedPost.value.id}`, formData);

    const updatedPost = response.data.post;
    const index = props.userPosts.findIndex(p => p.id === selectedPost.value.id);
    if (index !== -1) {
      props.userPosts.splice(index, 1, updatedPost);
    }

    showEditPopup.value = false;
  } catch (error) {
    console.error('Error updating post:', error);
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const reportPost = async (post) => {
  try {
    const token = localStorage.getItem('token');
    await apiClient.post(`/api/posts/${post.id}/report`, {
      reason: prompt('Please enter reason for reporting:'),
    });
    alert('Post reported successfully');
  } catch (error) {
    console.error('Error reporting post:', error);
  }
};
</script>

<style scoped>
.h-48 {
  height: 12rem;
  /* Ensure consistent height */
}

.text-gray-800 {
  word-break: break-word;
  /* Prevent text overflow */
}
</style>
