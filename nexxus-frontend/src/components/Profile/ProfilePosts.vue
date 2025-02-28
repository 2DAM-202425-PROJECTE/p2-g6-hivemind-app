<template>
  <div>
    <div v-if="userPosts === 'No posts available'" class="text-center text-gray-500 dark:text-gray-300">
      No posts available
    </div>
    <div v-else class="grid grid-cols-3 gap-3">
      <div v-for="(post, index) in userPosts" :key="index" class="h-36 flex items-center justify-center bg-gray-300 dark:bg-gray-600 rounded-lg">
        <p>{{ post.description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['userPosts'])
</script>
