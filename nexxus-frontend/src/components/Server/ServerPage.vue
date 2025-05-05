<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800">
    <NavBar />
    <div class="server-container animate-fade-in">
      <ServerList
        :servers="servers"
        :selected-server="selectedServer"
        @select-server="selectServer"
        @show-create-server="showCreateServer = true"
      />

      <ChannelList
        v-if="selectedServer"
        :selected-server="selectedServer"
        :selected-channel="selectedChannel"
        :current-user="currentUser"
        @select-channel="selectChannel"
      />

      <div class="main-content">
        <ChatArea
          v-if="selectedChannel"
          :selected-channel="selectedChannel"
          @send-message="sendMessage"
        />
        <div v-else class="empty-state">
          <div class="empty-content">
            <img src="@/assets/logo.png" alt="Nexxus" class="logo" />
            <h2 class="text-3xl font-bold text-amber-900 mb-4">
              <span class="animate-letter gradient-text" style="--order: 1">W</span>
              <span class="animate-letter gradient-text" style="--order: 2">e</span>
              <span class="animate-letter gradient-text" style="--order: 3">l</span>
              <span class="animate-letter gradient-text" style="--order: 4">c</span>
              <span class="animate-letter gradient-text" style="--order: 5">o</span>
              <span class="animate-letter gradient-text" style="--order: 6">m</span>
              <span class="animate-letter gradient-text" style="--order: 7">e</span>
              <span class="animate-letter gradient-text" style="--order: 8"> </span>
              <span class="animate-letter gradient-text" style="--order: 9">t</span>
              <span class="animate-letter gradient-text" style="--order: 10">o</span>
              <span class="animate-letter gradient-text" style="--order: 11"> </span>
              <span class="animate-letter gradient-text" style="--order: 12">N</span>
              <span class="animate-letter gradient-text" style="--order: 13">e</span>
              <span class="animate-letter gradient-text" style="--order: 14">x</span>
              <span class="animate-letter gradient-text" style="--order: 15">x</span>
              <span class="animate-letter gradient-text" style="--order: 16">u</span>
              <span class="animate-letter gradient-text" style="--order: 17">s</span>
              <span class="animate-letter gradient-text" style="--order: 18">!</span>
            </h2>
            <p class="text-amber-700">Select a channel to start chatting</p>
          </div>
        </div>

        <OnlineUsers :online-users="onlineUsers" />
      </div>
    </div>
    <AppFooter />

    <ServerModals
      :show-create-server="showCreateServer"
      :new-server="newServer"
      :show-channel-popup="showChannelPopup"
      :new-channel="newChannel"
      :show-category-popup="showCategoryPopup"
      :new-category="newCategory"
      :show-server-details-popup="showServerDetailsPopup"
      :selected-server="selectedServer"
      :show-edit-server="showEditServer"
      :edit-server="editServer"
      :show-leave-server-popup="showLeaveServerPopup"
      :show-report-popup="showReportPopup"
      :report-reason="reportReason"
      :custom-report-reason="customReportReason"
      @update:show-create-server="showCreateServer = $event"
      @update:new-server="newServer = $event"
      @create-server="createServer"
      @update:show-channel-popup="showChannelPopup = $event"
      @update:new-channel="newChannel = $event"
      @create-channel="createChannel"
      @update:show-category-popup="showCategoryPopup = $event"
      @update:new-category="newCategory = $event"
      @create-category="createCategory"
      @update:show-server-details-popup="showServerDetailsPopup = $event"
      @update:show-edit-server="showEditServer = $event"
      @update:edit-server="editServer = $event"
      @save-server-changes="saveServerChanges"
      @handle-edit-file-upload="handleEditFileUpload"
      @update:show-leave-server-popup="showLeaveServerPopup = $event"
      @confirm-leave-server="confirmLeaveServer"
      @update:show-report-popup="showReportPopup = $event"
      @update:report-reason="reportReason = $event"
      @update:custom-report-reason="customReportReason = $event"
      @submit-report="submitReport"
      @handle-file-upload="handleFileUpload"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import NavBar from '@/components/NavBar.vue';
import AppFooter from '@/components/AppFooter.vue';
import ServerList from '@/components/Server/ServerList.vue';
import ChannelList from '@/components/Server/ChannelList.vue';
import ChatArea from '@/components/Server/ChatArea.vue';
import OnlineUsers from '@/components/Server/OnlineUsers.vue';
import ServerModals from '@/components/Server/ServerModals.vue';
import { useServer } from '@/composables/Server/useServer';
import apiClient from '@/axios.js';

const router = useRouter();
const {
  servers,
  selectedServer,
  selectedChannel,
  onlineUsers,
  newServer,
  newChannel,
  newCategory,
  showCreateServer,
  showChannelPopup,
  showCategoryPopup,
  showServerDetailsPopup,
  showEditServer,
  editServer,
  showLeaveServerPopup,
  showReportPopup,
  reportReason,
  customReportReason,
  selectServer,
  selectChannel,
  createServer,
  createChannel,
  createCategory,
  sendMessage,
  saveServerChanges,
  handleEditFileUpload,
  confirmLeaveServer,
  submitReport,
  handleFileUpload,
} = useServer();

const currentUser = ref({ username: 'Guest', id: 0 });

const fetchCurrentUser = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No access token found. Please log in.');
    const response = await apiClient.get('/api/user');
    currentUser.value = {
      username: response.data.username,
      id: response.data.id,
    };
  } catch (error) {
    console.error('Failed to fetch current user:', error);
    currentUser.value = { username: 'Guest', id: 0 };
  }
};

fetchCurrentUser();
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes letter {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-letter {
  display: inline-block;
  opacity: 0;
  animation: letter 0.5s ease-out forwards;
  animation-delay: calc(var(--order) * 0.1s);
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.server-container {
  display: flex;
  height: calc(100vh - 128px);
  margin-top: 64px;
}

.main-content {
  flex: 1;
  display: flex;
  background: #f3e8d3;
}

.empty-state {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3e8d3;
}

.empty-content {
  text-align: center;
  max-width: 400px;
  animation: fade-in 1s ease-out;
}

.empty-content .logo {
  width: 100px;
  height: 100px;
  margin-bottom: 20px;
}

.btn-primary {
  @apply px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center;
  @apply bg-gradient-to-r from-amber-500 to-amber-400 text-amber-900;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  @apply bg-gradient-to-r from-amber-600 to-amber-500 transform -translate-y-0.5;
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.btn-primary:disabled {
  @apply bg-gray-400 cursor-not-allowed transform-none;
  box-shadow: none;
}

@media (max-width: 768px) {
  .server-container {
    flex-direction: column;
    height: auto;
  }

  .empty-content .logo {
    width: 80px;
    height: 80px;
  }
}
</style>
