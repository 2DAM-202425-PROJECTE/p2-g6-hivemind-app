import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/axios'

export function useProfile() {
  const router = useRouter()

  const user = ref({
    name: '',
    level: 0,
    description: '',
    profile_photo_url: '',
    banner_photo_url: '', // New field
    stories: [],
    posts: 0,
    followers: 0,
    following: 0,
  })

  const userPosts = ref([])

  const fetchUserProfile = async () => {
    try {
      const response = await apiClient.get('/api/user/profile')
      user.value = response.data.user
      userPosts.value = response.data.user.posts
    } catch (error) {
      console.error('Error fetching user profile:', error)
    }
  }

  onMounted(() => {
    fetchUserProfile()
  })

  return {
    user,
    userPosts,
  }
}
