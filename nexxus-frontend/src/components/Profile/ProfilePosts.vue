<template>
  <div>
    <div v-for="post in userPosts" :key="post.id">
      <!-- Render post content here -->
      <p>{{ post.content }}</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  userPosts: {
    type: Array,
    required: true,
  },
});
</script>
