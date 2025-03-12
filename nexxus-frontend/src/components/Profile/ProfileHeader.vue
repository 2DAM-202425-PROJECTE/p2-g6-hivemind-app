<template>
  <div class="bg-white text-black p-6 rounded-lg shadow-lg">
    <div class="relative w-full h-40 bg-gray-200 flex justify-center items-center border border-gray-300 rounded-lg">
      <span class="text-gray-500">Upload a profile banner</span>
    </div>

    <div class="relative flex items-center mt-[-60px] ml-6">
      <img class="w-28 h-28 rounded-full border-4 border-gray-300 shadow-md" :src="user.profile_photo_url"
           alt="Profile Pic"/>
    </div>

    <div class="mt-4 ml-6">
      <h3 class="text-xl font-bold">{{ user.name }}</h3>
      <div class="text-sm text-gray-500">@{{ user.username }}</div>
      <div v-if="user.about" class="mt-2 text-sm text-gray-600">{{ user.about }}</div>
      <div v-else-if="isCurrentUser" class="mt-2 text-sm text-gray-400 italic">What would you like others to know about
        you? Add description to your profile
      </div>
      <div class="flex gap-4 mt-2 text-sm text-gray-600">
        <span>{{ user.posts?.length || 0 }} posts</span>
        <span>{{ user.followers || 0 }} followers</span>
        <span>{{ user.following || 0 }} following</span>
      </div>
    </div>

    <div class="flex items-center justify-between mt-5 mx-6">
      <button v-if="isCurrentUser" @click="editProfile"
              class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-600">Edit Profile
      </button>
      <button v-if="isCurrentUser" @click="connectSocial"
              class="border border-gray-400 text-gray-600 px-4 py-2 rounded hover:bg-gray-100">Connect Social Accounts
      </button>
      <button @click="shareProfile"
              class="flex items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-share-alt mr-2"></i> Share Profile
      </button>
    </div>
  </div>
</template>

<script setup>
import {onMounted} from 'vue';

const props = defineProps({
  user: Object,
  isCurrentUser: Boolean, // Recibe isCurrentUser como prop
  editProfile: Function,
  connectSocial: Function,
  shareProfile: Function,
});

onMounted(() => {
  console.log('isCurrentUser en ProfileHeader:', props.isCurrentUser); // Debug
});
</script>
