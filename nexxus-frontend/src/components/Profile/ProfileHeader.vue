<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden max-w-5xl mx-auto mb-5">
    <!-- Banner -->
    <div class="relative w-full h-48 bg-gray-200 dark:bg-gray-700">
      <img
        v-if="user.banner_photo_path"
        :src="user.banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
      />
      <div
        v-else
        class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400"
      >
        No banner set
      </div>
    </div>

    <!-- Contenido -->
    <div class="relative px-6 pb-6">
      <!-- Foto de perfil -->
      <div class="absolute -top-16 left-6">
        <img
          :src="user.profile_photo_url"
          alt="Profile Pic"
          class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover"
        />
      </div>

      <!-- Información y botones -->
      <div class="pt-20 flex flex-col md:flex-row md:items-start">
        <!-- Información del usuario -->
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h3>
          <div class="text-sm text-gray-500 dark:text-gray-400">@{{ user.username }}</div>
          <div v-if="user.description" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            {{ user.description }}
          </div>
          <div v-else-if="isCurrentUser" class="mt-2 text-sm text-gray-400 dark:text-gray-500 italic">
            What would you like others to know about you? Add a description to your profile
          </div>
          <div class="flex gap-6 mt-3 text-sm text-gray-600 dark:text-gray-400">
            <span>{{ user.posts?.length || 0 }} posts</span>
            <span>{{ user.followers || 0 }} followers</span>
            <span>{{ user.following || 0 }} following</span>
          </div>
        </div>

        <!-- Botones verticales -->
        <div class="absolute right-6 h-32 flex flex-col justify-between">
          <div class="flex flex-col">
            <button
              v-if="isCurrentUser"
              @click="editProfile"
              class="p-2 text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition duration-200 transform hover:scale-110"
              title="Edit Profile"
            >
              <i class="fas fa-edit"></i>
            </button>
            <button
              v-if="isCurrentUser"
              @click="connectSocial"
              class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition duration-200 transform hover:scale-110"
              title="Connect Social Accounts"
            >
              <i class="fas fa-link"></i>
            </button>
            <button
              @click="shareProfile"
              class="p-2 text-gray-600 dark:text-gray-300 hover:text-green-500 dark:hover:text-green-400 transition duration-200 transform hover:scale-110"
              title="Share Profile"
            >
              <i class="fas fa-share-alt"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  isCurrentUser: {
    type: Boolean,
    required: true,
  },
  editProfile: {
    type: Function,
    required: true,
  },
  connectSocial: {
    type: Function,
    default: () => console.log('Connect Social clicked'),
  },
  shareProfile: {
    type: Function,
    required: true,
  },
});

const getImageUrl = (filePath) => {
  const baseUrl = 'http://localhost:8000'; // Adjust according to your server
  if (filePath && filePath.startsWith('/')) return `${baseUrl}${filePath}`;
  return filePath ? `${baseUrl}/storage/${filePath}` : '/default-profile.jpg';
};

onMounted(() => {
  console.log('isCurrentUser in ProfileHeader:', props.isCurrentUser);
});
</script>
