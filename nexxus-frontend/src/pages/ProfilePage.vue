<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white p-5 pb-24">
    <Navbar />
    <h1 class="text-2xl mb-5">Profile</h1>

    <div class="flex gap-3 overflow-x-auto pb-3">
      <div v-for="(achievement, index) in achievements" :key="index" class="text-center flex-shrink-0">
        <img :src="achievement.icon" :alt="achievement.name" class="w-16 h-16 rounded-full border-2 border-blue-600" />
        <p class="text-xs mt-1">{{ achievement.name }}</p>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 shadow-md max-w-2xl mx-auto">
      <div class="flex items-center gap-4 mb-5">
        <img class="w-20 h-20 rounded-full border-4 border-gray-500" src="https://via.placeholder.com/80" alt="Profile Pic" />
        <div>
          <h3 class="text-lg font-bold">{{ user.name }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-300">Level {{ user.level }}</p>
          <div class="flex gap-3 mt-2 text-sm">
            <span>Posts: {{ user.posts }}</span>
            <span>Followers: {{ user.followers }}</span>
            <span>Following: {{ user.following }}</span>
          </div>
        </div>
      </div>

      <p class="text-sm mb-5">{{ user.description }}</p>

      <div class="flex justify-center gap-3 mb-5">
        <button @click="editProfile" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Edit Profile</button>
        <button @click="shareProfile" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Share Profile</button>
      </div>

      <div class="flex items-center justify-center mb-5">
        <button @click="prevStory" class="text-2xl">&lt;</button>
        <div class="flex gap-3">
          <div v-for="(story, index) in visibleStories" :key="index" class="text-center">
            <img :src="story.image" :alt="story.name" class="w-20 h-20 rounded-lg border-2 border-gray-500" />
            <p class="text-xs mt-1">{{ story.name }}</p>
          </div>
        </div>
        <button @click="nextStory" class="text-2xl">&gt;</button>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div v-for="(post, index) in userPosts" :key="index" class="h-36 flex items-center justify-center bg-gray-300 dark:bg-gray-600 rounded-lg">
          <p>{{ post.title }}</p>
        </div>
      </div>
    </div>

    <ShareModal v-if="isModalVisible" :shareUrl="shareUrl" @close="isModalVisible = false" />
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import ShareModal from '@/components/ShareModal.vue'

const router = useRouter()

const user = ref({
  name: 'name',
  level: 0,
  description: 'description',
  stories: [
    { name: "Story 1", image: "https://via.placeholder.com/80" },
    { name: "Story 2", image: "https://via.placeholder.com/80" },
    { name: "Story 3", image: "https://via.placeholder.com/80" },
    { name: "Story 4", image: "https://via.placeholder.com/80" },
    { name: "Story 5", image: "https://via.placeholder.com/80" },
    { name: "Story 6", image: "https://via.placeholder.com/80" },
    { name: "Story 7", image: "https://via.placeholder.com/80" },
    { name: "Story 8", image: "https://via.placeholder.com/80" },
    { name: "Story 9", image: "https://via.placeholder.com/80" },
  ],
  posts: 0,
  followers: 0,
  following: 0,
})

const userPosts = ref([
  { image: '', title: 'Post 1' },
  { image: '', title: 'Post 2' },
  { image: '', title: 'Post 3' },
  { image: '', title: 'Post 4' },
  { image: '', title: 'Post 5' },
  { image: '', title: 'Post 6' },
  { image: '', title: 'Post 7' },
  { image: '', title: 'Post 8' },
  { image: '', title: 'Post 9' },
])

const currentIndex = ref(0)
const visibleStories = computed(() => {
  return user.value.stories.slice(currentIndex.value, currentIndex.value + 3)
})

const nextStory = () => {
  if (currentIndex.value + 3 < user.value.stories.length) {
    currentIndex.value++
  }
}

const prevStory = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  }
}

const editProfile = () => {
  router.push('/edit-profile')
}

const isModalVisible = ref(false)
const shareUrl = ref(window.location.href)

const shareProfile = () => {
  isModalVisible.value = true
}
</script>
