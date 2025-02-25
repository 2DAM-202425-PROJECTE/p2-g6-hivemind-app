import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export function useProfile() {
  const router = useRouter()

  const user = ref({
    name: 'name',
    level: 0,
    description: 'description',
    profilePic: 'https://via.placeholder.com/80',
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

  return {
    user,
    userPosts,
    visibleStories,
    nextStory,
    prevStory,
    editProfile,
    isModalVisible,
    shareUrl,
    shareProfile
  }
}
