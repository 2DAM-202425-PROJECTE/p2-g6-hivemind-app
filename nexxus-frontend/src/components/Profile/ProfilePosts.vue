<template>
  <div>
    <div v-if="userPosts === 'No posts available'" class="text-center text-gray-500 dark:text-gray-300">
      No posts available
    </div>
    <div v-else class="grid grid-cols-3 gap-3">
      <div v-for="(post, index) in userPosts" :key="index" class="h-36 flex items-center justify-center bg-gray-300 dark:bg-gray-600 rounded-lg overflow-hidden">
        <img v-if="post.file_path" :src="getImageUrl(post.file_path)" alt="Post Image" class="w-full h-full object-cover" />
        <span v-else class="text-gray-500">No Image Available</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue';

defineProps(['userPosts']);

const getImageUrl = (path) => {
  if (!path) return 'https://via.placeholder.com/150';
  return `http://localhost:8000/storage/${path}`;
};
</script>

<style scoped>
img {
  object-fit: cover;
  width: 100%;
  height: 100%;
}
</style>
