import { ref } from 'vue';
import apiClient from '@/axios.js';

export function useServer() {
  // State
  const servers = ref([
    {
      id: 1,
      name: 'Hive Hub',
      description: 'A buzzing community for all!',
      icon: 'https://api.iconify.design/mdi/bee.svg',
      category: 'Community',
      visibility: 'Public',
      channels: [
        { id: 1, name: 'general', isCategory: false, messages: [], parentCategoryId: 1 },
        { id: 2, name: 'TEXT CHANNELS', isCategory: true, channels: [] },
      ],
      users: [{ id: 1, name: 'QueenBee', online: true }],
    },
  ]);

  const currentUser = ref(null);
  const selectedServer = ref(null);
  const selectedChannel = ref(null);
  const searchQuery = ref('');
  const newServer = ref({ name: '', description: '', category: 'Community', visibility: 'Public', icon: '' });
  const newChannel = ref({ name: '', category: '' });
  const newCategory = ref({ name: '' });
  const newMessage = ref('');
  const showCreateServer = ref(false);
  const showChannelPopup = ref(false);
  const showCategoryPopup = ref(false);
  const showCreateOptions = ref(false);
  const showOptionsMenu = ref(false);
  const showServerDetailsPopup = ref(false);
  const showEditServer = ref(false);
  const showLeaveServerPopup = ref(false);
  const showReportPopup = ref(false);
  const reportReason = ref('');
  const customReportReason = ref('');
  const editServer = ref({ name: '', description: '', category: '', visibility: '', icon: '' });
  const onlineUsers = ref([{ id: 1, name: 'QueenBee', online: true }]);

  // Methods
  const fetchCurrentUser = async () => {
    try {
      const token = localStorage.getItem('token');
      if (!token) throw new Error('No access token found');

      const response = await apiClient.get('/api/user');
      currentUser.value = response.data;
    } catch (error) {
      console.error('Failed to fetch current user:', error);
    }
  };

  const selectServer = (server) => {
    selectedServer.value = server;
    selectedChannel.value = null;
    showOptionsMenu.value = false;
  };

  const selectChannel = (channel) => {
    selectedChannel.value = channel;
  };

  const createServer = () => {
    const server = {
      ...newServer.value,
      id: servers.value.length + 1,
      channels: [
        { id: 1, name: 'general', isCategory: false, messages: [], parentCategoryId: 1 },
        { id: 2, name: 'TEXT CHANNELS', isCategory: true, channels: [] },
      ],
      users: [{ id: 1, name: 'QueenBee', online: true }],
      icon: newServer.value.icon || 'https://api.iconify.design/mdi/bee.svg',
    };
    servers.value.push(server);
    newServer.value = { name: '', description: '', category: 'Community', visibility: 'Public', icon: '' };
    showCreateServer.value = false;
    selectServer(server);
  };

  const createChannel = () => {
    if (!selectedServer.value) return;
    const channel = {
      id: selectedServer.value.channels.length + 1,
      name: newChannel.value.name,
      isCategory: false,
      messages: [],
      parentCategoryId: newChannel.value.category ? selectedServer.value.channels.find(c => c.name === newChannel.value.category)?.id : 1,
    };
    selectedServer.value.channels.push(channel);
    newChannel.value = { name: '', category: '' };
    showChannelPopup.value = false;
    selectChannel(channel);
  };

  const createCategory = () => {
    if (!selectedServer.value) return;
    const category = {
      id: selectedServer.value.channels.length + 1,
      name: newCategory.value.name,
      isCategory: true,
      channels: [],
    };
    selectedServer.value.channels.push(category);
    newCategory.value = { name: '' };
    showCategoryPopup.value = false;
  };

  const sendMessage = (messageText) => {
    if (!selectedChannel.value || !messageText.trim()) return;
    selectedChannel.value.messages.push({
      id: selectedChannel.value.messages.length + 1,
      text: messageText,
      isMine: true,
      user: {
        id: currentUser.value?.id || 0,
        name: currentUser.value?.name || 'Guest',
        avatar: currentUser.value?.avatar,
      },
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    });
  };

  const deleteChannel = (channel) => {
    if (!selectedServer.value) return;
    selectedServer.value.channels = selectedServer.value.channels.filter(c => c.id !== channel.id);
    if (selectedChannel.value?.id === channel.id) {
      selectedChannel.value = null;
    }
  };

  const toggleCreateOptions = () => {
    showCreateOptions.value = !showCreateOptions.value;
  };

  const toggleOptionsMenu = () => {
    showOptionsMenu.value = !showOptionsMenu.value;
  };

  const showServerDetails = () => {
    showServerDetailsPopup.value = true;
  };

  const editServerConfig = () => {
    if (selectedServer.value) {
      editServer.value = { ...selectedServer.value };
      showEditServer.value = true;
    }
  };

  const saveServerChanges = () => {
    if (selectedServer.value) {
      const index = servers.value.findIndex(s => s.id === selectedServer.value.id);
      if (index !== -1) {
        servers.value[index] = { ...editServer.value, channels: servers.value[index].channels, users: servers.value[index].users };
        selectedServer.value = servers.value[index];
      }
      showEditServer.value = false;
    }
  };

  const handleEditFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        editServer.value.icon = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  };

  const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        newServer.value.icon = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  };

  const deleteServer = () => {
    showLeaveServerPopup.value = true;
  };

  const confirmLeaveServer = () => {
    if (selectedServer.value) {
      servers.value = servers.value.filter(s => s.id !== selectedServer.value.id);
      selectedServer.value = null;
      selectedChannel.value = null;
      showLeaveServerPopup.value = false;
    }
  };

  const reportServer = () => {
    showReportPopup.value = true;
  };

  const submitReport = () => {
    console.log('Reported server:', { reason: reportReason.value, customReason: customReportReason.value });
    reportReason.value = '';
    customReportReason.value = '';
    showReportPopup.value = false;
  };

  return {
    servers,
    currentUser,
    selectedServer,
    selectedChannel,
    searchQuery,
    newServer,
    newChannel,
    newCategory,
    newMessage,
    showCreateServer,
    showChannelPopup,
    showCategoryPopup,
    showCreateOptions,
    showOptionsMenu,
    showServerDetailsPopup,
    showEditServer,
    showLeaveServerPopup,
    showReportPopup,
    reportReason,
    customReportReason,
    editServer,
    onlineUsers,
    fetchCurrentUser,
    selectServer,
    selectChannel,
    createServer,
    createChannel,
    createCategory,
    sendMessage,
    deleteChannel,
    toggleCreateOptions,
    toggleOptionsMenu,
    showServerDetails,
    editServerConfig,
    saveServerChanges,
    handleEditFileUpload,
    deleteServer,
    confirmLeaveServer,
    reportServer,
    submitReport,
    handleFileUpload,
  };
}
