<template>
  <div class="profile-container">
    <Navbar />
    <h1>Profile</h1>
    <div class="achievements">
      <div class="achievement" v-for="(achievement, index) in achievements" :key="index">
        <img :src="achievement.icon" :alt="achievement.name" />
        <p>{{ achievement.name }}</p>
      </div>
    </div>

    <div class="profile-card">
      <div class="profile-header">
        <img class="profile-pic" :src="user.profile_photo_path" alt="Profile Pic" />
        <div class="profile-info">
          <h3>{{ user.name }}</h3>
          <p>Level {{ user.level }}</p>
          <div class="profile-stats">
            <div class="posts">
              <span>Posts: {{ user.posts }}</span>
            </div>
            <div class="followers">
              <span>Followers: {{ user.followers }}</span>
            </div>
            <div class="following">
              <span>Following: {{ user.following }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="Description">
        <p>{{ user.description }}</p>
      </div>

      <!-- Buttons Section -->
      <div class="buttons-section">
        <button @click="editProfile" class="profile-btn">Edit Profile</button>
        <button @click="shareProfile" class="profile-btn">Share Profile</button>
      </div>

      <!-- Stories Section -->
      <div class="stories-section">
        <button @click="prevStory" class="arrow left-arrow">&lt;</button>
        <div class="stories">
          <div class="story" v-for="(story, index) in visibleStories" :key="index">
            <img :src="story.image" :alt="story.name" />
            <p>{{ story.name }}</p>
          </div>
        </div>
        <button @click="nextStory" class="arrow right-arrow">&gt;</button>
      </div>

      <div class="posts-grid">
        <div class="post blank-box" v-for="(post, index) in userPosts" :key="index">
          <p>{{ post.title }}</p>
        </div>
      </div>
    </div>
    <ShareModal v-if="isModalVisible" :shareUrl="shareUrl" @close="isModalVisible = false" />
    <Footer />
  </div>
</template>

<script setup>
import axios from '@/axios'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import ShareModal from '@/components/ShareModal.vue'

const router = useRouter()

const user = ref({
  profile_photo_path: '',
  name: '',
  level: 0,
  description: '',
  stories: [],
  posts: 0,
  followers: 0,
  following: 0,
})

const userPosts = ref([
  // { image: '', title: 'Post 1' },
  // { image: '', title: 'Post 2' },
  // { image: '', title: 'Post 3' },
  // { image: '', title: 'Post 4' },
  // { image: '', title: 'Post 5' },
  // { image: '', title: 'Post 6' },
  // { image: '', title: 'Post 7' },
  // { image: '', title: 'Post 8' },
  // { image: '', title: 'Post 9' },
])

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user data:', error)
  }
}

onMounted(() => {
  fetchUser()
})

// const currentIndex = ref(0)

// const visibleStories = computed(() => {
//   return user.value.stories.slice(currentIndex.value, currentIndex.value + 3)
// })

// const nextStory = () => {
//   if (currentIndex.value + 3 < user.value.stories.length) {
//     currentIndex.value++
//   }
// }

// const prevStory = () => {
//   if (currentIndex.value > 0) {
//     currentIndex.value--
//   }
// }

const editProfile = () => {
  router.push('/edit-profile')
}

const isModalVisible = ref(false)
const shareUrl = ref(window.location.href)

const shareProfile = () => {
  isModalVisible.value = true
}
</script>

<style scoped>
.profile-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: #f0f2f5;
  min-height: 100vh;
  color: #1c1e21;
  padding-bottom: 90px;
}

h1 {
  font-size: 24px;
  margin-bottom: 20px;
}

.achievements {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  overflow-x: auto;
  padding-bottom: 10px;
}

.achievement {
  text-align: center;
  flex-shrink: 0;
}

.achievement img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid #4267B2;
}

.achievement p {
  margin-top: 5px;
  font-size: 12px;
}

.profile-card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  margin: 0 auto;
  width: 100%;
}

.profile-header {
  display: flex;
  gap: 15px;
  align-items: center;
  margin-bottom: 20px;
}

.profile-pic {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid #706e6e;
}

.profile-info {
  display: flex;
  flex-direction: column;
}

.profile-info h3 {
  font-size: 20px;
  margin: 0;
}

.profile-info p {
  font-size: 14px;
  margin: 5px 0 0;
  color: #7f7f7f;
}

.profile-stats {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.profile-stats > div {
  display: flex;
  align-items: center;
  gap: 5px;
}

.cover-image {
  width: 100%;
  border-radius: 10px;
  margin-bottom: 20px;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 20px;
}

.post {
  text-align: center;
  background-color: #e4e6eb;
  border-radius: 10px;
  padding: 20px;
  height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.stories-section {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.stories {
  display: flex;
  gap: 10px;
}

.story {
  text-align: center;
}

.story img {
  width: 80px;
  height: 80px;
  border-radius: 10px; /* Change from 50% to 10px for square corners */
  border: 2px solid #7f7f7f;
}

.story p {
  margin-top: 5px;
  font-size: 12px;
}

.arrow {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.left-arrow {
  margin-right: 10px;
}

.right-arrow {
  margin-left: 10px;
}

.buttons-section {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 20px 0;
}

.profile-btn {
  background-color: #7f7f7f;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  flex: 1;
  cursor: pointer;
}

.profile-btn:hover {
  background-color: #7f7f7f;
}

@media (max-width: 768px) {
  .profile-card {
    max-width: 100%;
    padding: 10px;
  }

  .profile-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .profile-info {
    align-items: flex-start;
  }

  .profile-stats {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
