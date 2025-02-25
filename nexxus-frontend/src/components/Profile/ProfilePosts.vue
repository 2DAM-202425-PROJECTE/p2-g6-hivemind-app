<template>
  <div class="grid grid-cols-3 gap-3">
    <div v-for="(post, index) in userPosts" :key="index" class="h-36 flex items-center justify-center bg-gray-300 dark:bg-gray-600 rounded-lg">
      <p>{{ post.title }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps(['userPosts'])
</script>
