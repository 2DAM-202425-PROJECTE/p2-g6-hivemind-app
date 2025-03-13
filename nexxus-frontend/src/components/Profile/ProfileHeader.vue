<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden max-w-5xl mx-auto">
    <!-- Banner -->
    <div class="relative w-full h-48 bg-gray-200 dark:bg-gray-700">
      <img
        v-if="user.banner_photo_url"
        :src="getImageUrl(user.banner_photo_url)"
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
          :src="user.profile_photo_url || '/default-profile.jpg'"
          alt="Profile Pic"
          class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover"
        />
      </div>

      <!-- Información y botones -->
      <div class="pt-20 flex flex-col md:flex-row md:items-start md:justify-between">
        <!-- Información del usuario -->
        <div class="ml-0 md:ml-40">
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

        <!-- Botones -->
        <div class="mt-6 md:mt-0 flex flex-col sm:flex-row gap-3">
          <button
            v-if="isCurrentUser"
            @click="editProfile"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200"
          >
            Edit Profile
          </button>
          <button
            v-if="isCurrentUser"
            @click="connectSocial"
            class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200"
          >
            Connect Social Accounts
          </button>
          <button
            @click="shareProfile"
            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 flex items-center justify-center"
          >
            <i class="fas fa-share-alt mr-2"></i> Share Profile
          </button>
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

// Reutilizamos getImageUrl (ajústalo si está en otro archivo)
const getImageUrl = (filePath) => {
  const baseUrl = 'http://localhost:8000'; // Cambia según tu servidor
  if (filePath && filePath.startsWith('/')) return `${baseUrl}${filePath}`;
  return filePath ? `${baseUrl}/storage/${filePath}` : '/default-profile.jpg';
};

onMounted(() => {
  console.log('isCurrentUser en ProfileHeader:', props.isCurrentUser);
});
</script>
