<template>
  <div class="flex items-center gap-4 mb-5">
    <img class="w-20 h-20 rounded-full border-4 border-gray-500" :src="user.profilePic" alt="Profile Pic" />
    <div>
      <h3 class="text-lg font-bold">{{ user.name }}</h3>
      <p class="text-sm text-gray-500 dark:text-gray-300">Level {{ user.level }}</p>
      <div class="flex gap-3 mt-2 text-sm">
        <span>Posts: {{ user.posts }}</span>
        <span>Followers: {{ user.followers }}</span>
        <span>Following: {{ user.following }}</span>
      </div>
    </div>
    <div class="flex justify-center gap-3 mb-5">
      <button @click="editProfile" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Edit Profile</button>
      <button @click="shareProfile" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Share Profile</button>
    </div>
  </div>
</template>

<script setup>
defineProps(['user', 'editProfile', 'shareProfile'])
</script>
