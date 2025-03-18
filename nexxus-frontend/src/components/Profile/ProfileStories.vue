<template>
  <div class="flex items-center justify-center mb-5 bg-white text-black p-6 rounded-lg shadow-lg">
    <button @click="prevStory" class="text-2xl">&lt;</button>
    <div class="flex gap-3">
      <div v-for="(story, index) in visibleStories" :key="index" class="text-center">
        <img :src="story.image" :alt="story.name" class="w-20 h-20 rounded-lg border-2 border-gray-500" />
        <p class="text-xs mt-1">{{ story.name }}</p>
      </div>
    </div>
    <button @click="nextStory" class="text-2xl">&gt;</button>
  </div>
</template>

<script setup>
defineProps(['visibleStories', 'prevStory', 'nextStory'])
</script>
