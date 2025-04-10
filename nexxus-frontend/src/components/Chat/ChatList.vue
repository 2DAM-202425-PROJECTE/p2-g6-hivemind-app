<template>
  <div class="bg-white rounded-lg shadow-lg h-full flex flex-col border border-amber-200 overflow-hidden">
    <!-- Encabezado con buscador -->
    <div class="sticky top-0 bg-white p-4 z-10 border-b border-amber-100">
      <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span>Hive Conversations</span>
      </h2>

      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search in your hive..."
          class="w-full pl-10 pr-4 py-2 border border-amber-200 rounded-lg bg-amber-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-300 transition-all"
          @input="handleSearch"
        >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
    </div>

    <!-- Lista de chats con scroll personalizado -->
    <div
      class="flex-1 overflow-y-auto hover-scroll-container"
      @mouseenter="showScroll = true"
      @mouseleave="showScroll = false"
    >
      <div v-if="filteredChats.length === 0" class="p-4 text-center text-gray-500">
        No chats found in your hive
      </div>

      <div v-else class="divide-y divide-amber-100">
        <div
          v-for="(chat, index) in filteredChats"
          :key="index"
          @click="$emit('select-chat', chat)"
          class="flex items-center gap-3 p-4 hover:bg-amber-50 cursor-pointer transition-colors group"
          :class="{ 'bg-amber-50': isChatActive(chat) }"
        >
          <!-- Avatar con indicador de actividad -->
          <div class="relative flex-shrink-0">
            <img
              :src="chat.profile_photo_url"
              alt="Avatar"
              class="w-12 h-12 rounded-full border-2 border-amber-300 object-cover"
            >
            <span
              v-if="chat.unread_count"
              class="absolute -top-1 -right-1 bg-amber-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse"
            >
              {{ chat.unread_count }}
            </span>
          </div>

          <!-- Contenido del chat -->
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-baseline">
              <h3 class="text-sm font-semibold text-gray-800 truncate">{{ chat.name }}</h3>
              <span class="text-xs text-gray-500 ml-2 whitespace-nowrap">{{ formatTime(chat.last_message_time) }}</span>
            </div>
            <p class="text-xs text-gray-500 truncate">@{{ chat.username }}</p>

            <!-- Último mensaje -->
            <div class="flex items-center gap-1 mt-1">
              <p
                class="text-xs truncate"
                :class="chat.unread_count ? 'text-gray-800 font-medium' : 'text-gray-500'"
              >
                {{ getLastMessagePreview(chat) }}
              </p>
              <span
                v-if="chat.last_message?.is_edited"
                class="text-xs text-gray-400"
              >
                (edited)
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  chats: Array,
  selectedChat: Object
});

const emit = defineEmits(['select-chat']);

const searchQuery = ref('');
const showScroll = ref(false);

// Filtrar chats basado en la búsqueda
const filteredChats = computed(() => {
  if (!searchQuery.value) return props.chats;

  const query = searchQuery.value.toLowerCase();
  return props.chats.filter(chat =>
    chat.name.toLowerCase().includes(query) ||
    chat.username.toLowerCase().includes(query) ||
    (chat.last_message?.text?.toLowerCase().includes(query))
  );
});

// Manejar búsqueda
const handleSearch = () => {
  // La computed property filteredChats se actualiza automáticamente
};

// Formatear hora del último mensaje
const formatTime = (timestamp) => {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

// Obtener vista previa del último mensaje
const getLastMessagePreview = (chat) => {
  if (!chat.last_message) return 'No messages yet';

  const maxLength = 30;
  const text = chat.last_message.text || '';
  return text.length > maxLength
    ? `${text.substring(0, maxLength)}...`
    : text;
};

// Verificar si el chat está activo
const isChatActive = (chat) => {
  return props.selectedChat && props.selectedChat.id === chat.id;
};
</script>

<style scoped>
/* Animación para mensajes no leídos */
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.animate-pulse {
  animation: pulse 1.5s infinite;
}

/* Scrollbar personalizada que aparece solo al hacer hover */
.hover-scroll-container {
  scrollbar-width: thin;
  scrollbar-color: transparent transparent;
}

.hover-scroll-container:hover {
  scrollbar-color: #f59e0b #fef3c7;
}

.hover-scroll-container::-webkit-scrollbar {
  width: 6px;
}

.hover-scroll-container::-webkit-scrollbar-track {
  background: transparent;
}

.hover-scroll-container:hover::-webkit-scrollbar-track {
  background: #fef3c7;
}

.hover-scroll-container::-webkit-scrollbar-thumb {
  background-color: transparent;
  border-radius: 3px;
}

.hover-scroll-container:hover::-webkit-scrollbar-thumb {
  background-color: #f59e0b;
}

/* Transiciones suaves */
.transition-colors {
  transition: background-color 0.2s ease;
}
</style>
