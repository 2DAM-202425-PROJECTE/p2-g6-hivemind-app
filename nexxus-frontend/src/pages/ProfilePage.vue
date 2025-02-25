<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white p-5 pb-24">
    <Navbar />
    <h1 class="text-2xl mb-5">Profile</h1>

    <div class="bg-white dark:bg-gray-700 rounded-lg p-5 shadow-md max-w-2xl mx-auto">
      <ProfileHeader :user="user" :editProfile="editProfile" :shareProfile="shareProfile" />
      <p class="text-sm mb-5">{{ user.description }}</p>
      <ProfileStories :visibleStories="visibleStories" :prevStory="prevStory" :nextStory="nextStory" />
      <ProfilePosts :userPosts="userPosts" />
    </div>

    <ShareModal v-if="isModalVisible" :shareUrl="shareUrl" @close="isModalVisible = false" />
    <Footer />
  </div>
</template>

<script setup>
import { useProfile } from '@/composables/profile/useProfile'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import ShareModal from '@/components/ShareModal.vue'
import ProfileHeader from '@/components/Profile/ProfileHeader.vue'
import ProfileStories from '@/components/Profile/ProfileStories.vue'
import ProfilePosts from '@/components/Profile/ProfilePosts.vue'

const {
  user,
  userPosts,
  visibleStories,
  nextStory,
  prevStory,
  editProfile,
  isModalVisible,
  shareUrl,
  shareProfile
} = useProfile()
</script>
