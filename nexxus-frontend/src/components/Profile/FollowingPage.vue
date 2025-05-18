<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <h1 class="text-2xl font-bold mb-4">{{ username }}'s Followers</h1>
    <div v-if="followers.length" class="grid gap-4">
      <div v-for="follower in followers" :key="follower.id" class="bg-white p-4 rounded-lg shadow">
        <router-link :to="`/profile/${follower.username}`" class="flex items-center gap-4">
          <img
            :src="follower.profile_photo_path || '/default-profile.jpg'"
            alt="Profile"
            class="w-12 h-12 rounded-full"
          />
          <div>
            <p class="font-semibold">{{ follower.username }}</p>
            <p class="text-gray-600">{{ follower.name }}</p>
          </div>
        </router-link>
      </div>
    </div>
    <p v-else>No followers yet.</p>
    <button
      v-if="hasMore"
      @click="loadMore"
      class="mt-4 px-4 py-2 bg-amber-500 text-black rounded-lg hover:bg-amber-600"
    >
      Load More
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import apiClient from '@/axios.js';

const route = useRoute();
const username = ref(route.params.username);
const followers = ref([]);
const page = ref(1);
const hasMore = ref(true);

const fetchFollowers = async (reset = false) => {
  if (reset) {
    page.value = 1;
    followers.value = [];
  }
  try {
    const response = await apiClient.get(`/api/users/{username}/following`, {
      params: { page: page.value },
    });
    followers.value = reset ? response.data.followers.data : [...followers.value, ...response.data.followers.data];
    hasMore.value = !!response.data.followers.next_page_url;
    page.value += 1;
  } catch (error) {
    console.error('Error fetching followers:', error);
  }
};

const loadMore = () => {
  fetchFollowers();
};

onMounted(() => {
  fetchFollowers(true);
});
</script>
