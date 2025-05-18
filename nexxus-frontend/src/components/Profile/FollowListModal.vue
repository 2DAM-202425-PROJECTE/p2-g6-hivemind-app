<!-- src/components/Profile/FollowListModal.vue -->
<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col">
      <!-- Header -->
      <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ isFollowers ? 'Followers' : 'Following' }} ({{ totalCount }})
        </h2>
        <button
          @click="close"
          class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
          aria-label="Close modal"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-gray-500 dark:text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Search -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <v-text-field
          v-model="localSearchQuery"
          :placeholder="isMobile ? 'Search...' : 'Search by username or name'"
          prepend-inner-icon="mdi-magnify"
          class="mb-0"
          clearable
          @input="debouncedSearch"
          variant="outlined"
          density="compact"
          single-line
          hide-details
        />
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="errorMessage" class="p-4 text-center">
          <div
            class="inline-flex items-center p-3 text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
            {{ errorMessage }}
          </div>
        </div>

        <div v-else-if="list.length" class="divide-y divide-gray-200 dark:divide-gray-700">
          <router-link
            v-for="(user, index) in list"
            :key="user.id"
            :to="`/profile/${user.username}`"
            @click="close"
            class="flex items-center gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
            :class="{
              'rounded-b-lg': index === list.length - 1 && !hasMore,
              'hover:rounded-b-lg': index === list.length - 1 && !hasMore,
            }"
          >
            <div class="relative flex-shrink-0">
              <img
                :src="getImageUrl(user.profile_photo_path)"
                alt="Profile"
                class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-gray-600"
                @error="handleImageError($event, user, index)"
              />
              <div
                v-if="user.is_online"
                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-600"
              ></div>
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-gray-900 dark:text-white truncate">{{ user.username }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ user.name || 'No name' }}</p>
            </div>
            <div class="ml-auto">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </router-link>
        </div>

        <div v-else class="p-8 text-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            {{ localSearchQuery ? 'No results found' : isFollowers ? 'No followers yet' : 'Not following anyone' }}
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="hasMore" class="p-4 border-t border-gray-200 dark:border-gray-700">
        <button
          @click="loadMore"
          class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors flex items-center justify-center"
          :disabled="isLoading"
          :class="{ 'opacity-75 cursor-not-allowed': isLoading }"
        >
          <svg
            v-if="isLoading"
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          {{ isLoading ? 'Loading...' : 'Load More' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { debounce } from 'lodash';
import {generateAvatar} from "@/utils/avatar.js";

const props = defineProps({
  isOpen: { type: Boolean, required: true },
  isFollowers: { type: Boolean, required: true },
  list: { type: Array, required: true },
  hasMore: { type: Boolean, required: true },
  totalCount: { type: Number, required: true },
  searchQuery: { type: String, required: true },
  fetchList: { type: Function, required: true },
});

const emit = defineEmits(['update:isOpen', 'update:searchQuery']);
const isLoading = ref(false);
const errorMessage = ref('');
const localSearchQuery = ref(props.searchQuery);
const isMobile = ref(false);
const defaultFallback = 'https://api.iconify.design/lucide/image-off.svg';

const checkMobile = () => {
  isMobile.value = window.innerWidth < 640;
};

const getImageUrl = (path) => {
  if (!path) return generateAvatar('User');
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  return `http://localhost:8000/storage/${path}`;
};

const handleImageError = (event, user, index) => {
  console.warn(`Image failed to load for user ${user.username} at index ${index}`);
  props.list[index].profile_photo_path = defaultFallback;
  event.target.src = defaultFallback;
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile);
});

const close = () => {
  console.log('Closing modal');
  emit('update:isOpen', false);
  errorMessage.value = '';
  localSearchQuery.value = '';
  props.fetchList(true);
};

const loadMore = async () => {
  console.log('Loading more', props.isFollowers ? 'followers' : 'following');
  isLoading.value = true;
  try {
    await props.fetchList();
  } catch (error) {
    errorMessage.value = 'Failed to load more users';
    console.error('Load more error:', error);
  } finally {
    isLoading.value = false;
  }
};

const debouncedSearch = debounce(() => {
  console.log('Searching with query:', localSearchQuery.value);
  emit('update:searchQuery', localSearchQuery.value);
  errorMessage.value = '';
  props.fetchList(true);
}, 300);

watch(() => props.isOpen, (newVal) => {
  console.log('Modal isOpen changed:', newVal);
  if (newVal) {
    errorMessage.value = '';
    localSearchQuery.value = props.searchQuery;
    props.fetchList(true);
  }
});

watch(() => props.searchQuery, (newVal) => {
  localSearchQuery.value = newVal;
});
</script>

<style scoped>
.router-link-active {
  @apply bg-gray-100 dark:bg-gray-700;
}

/* Smooth scrolling for iOS */
.overflow-y-auto {
  -webkit-overflow-scrolling: touch;
}

/* Mobile adjustments */
@media (max-width: 640px) {
  .max-w-md {
    max-width: 100%;
    margin-left: 0.5rem;
    margin-right: 0.5rem;
  }
  .p-5 {
    padding: 1rem;
  }
  .p-4 {
    padding: 0.75rem;
  }
  .text-xl {
    font-size: 1.125rem;
  }
  .text-sm {
    font-size: 0.875rem;
  }
  .w-12 {
    width: 2.5rem;
    height: 2.5rem;
  }
}
</style>
