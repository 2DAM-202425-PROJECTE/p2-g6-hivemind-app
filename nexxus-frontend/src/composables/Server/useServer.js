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
        { id: 1, name: 'general', isCategory: false, messages: [], parentCategoryId: null },
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
  const showCreateServer = ref(false);
  const showChannelPopup = ref(false);
  const showCategoryPopup = ref(false);
  const showOptionsMenu = ref(false);
  const showServerDetailsPopup = ref(false);
  const showEditServer = ref(false);
  const showLeaveServerPopup = ref(false);
  const showReportPopup = ref(false);
  const reportReason = ref('');
  const customReportReason = ref('');
  const editServer = ref({ name: '', description: '', category: '', visibility: '', icon: '' });
  const channelEdit = ref({ name: '', category: '' });
  const categoryEdit = ref({ name: '' });
  const onlineUsers = ref([{ id: 1, name: 'QueenBee', online: true }]);
  const lastUsedChannels = ref({});

  // Load last used channels from localStorage
  const loadLastUsedChannels = () => {
    const stored = localStorage.getItem('lastUsedChannels');
    if (stored) {
      lastUsedChannels.value = JSON.parse(stored);
    }
  };

  // Save last used channel to localStorage
  const saveLastUsedChannel = (serverId, channelId) => {
    lastUsedChannels.value[serverId] = channelId;
    localStorage.setItem('lastUsedChannels', JSON.stringify(lastUsedChannels.value));
  };

  // Fetch current user
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

  // Select a server and automatically select a channel
  const selectServer = (server) => {
    selectedServer.value = server;
    selectedChannel.value = null;
    showOptionsMenu.value = false;

    if (server) {
      loadLastUsedChannels();
      const channels = server.channels.filter((channel) => !channel.isCategory);
      const lastChannelId = lastUsedChannels.value[server.id];
      const lastChannel = channels.find((channel) => channel.id === lastChannelId);

      if (lastChannel) {
        selectChannel(lastChannel);
      } else {
        const generalChannel = channels.find((channel) => channel.name.toLowerCase() === 'general');
        if (generalChannel) {
          selectChannel(generalChannel);
        } else if (channels.length > 0) {
          selectChannel(channels[0]);
        }
      }
    }
  };

  // Select a channel and save it as the last used
  const selectChannel = (channel) => {
    if (!channel.messages && !channel.isCategory) {
      channel.messages = [];
    }
    selectedChannel.value = channel;
    showOptionsMenu.value = false;

    if (selectedServer.value && channel && !channel.isCategory) {
      saveLastUsedChannel(selectedServer.value.id, channel.id);
    }
  };

  // Create a new server
  const createServer = () => {
    const server = {
      ...newServer.value,
      id: servers.value.length + 1,
      channels: [
        { id: 1, name: 'general', isCategory: false, messages: [], parentCategoryId: null },
      ],
      users: [{ id: 1, name: 'QueenBee', online: true }],
      icon: newServer.value.icon || 'https://api.iconify.design/mdi/bee.svg',
    };
    servers.value.push(server);
    newServer.value = { name: '', description: '', category: 'Community', visibility: 'Public', icon: '' };
    showCreateServer.value = false;
    selectServer(server);
  };

  // Create a new channel
  const createChannel = () => {
    if (!selectedServer.value) return;
    const channel = {
      id: selectedServer.value.channels.length + 1,
      name: newChannel.value.name,
      isCategory: false,
      messages: [],
      parentCategoryId: newChannel.value.category
        ? selectedServer.value.channels.find((c) => c.name === newChannel.value.category)?.id
        : null,
    };
    selectedServer.value.channels.push(channel);
    newChannel.value = { name: '', category: '' };
    showChannelPopup.value = false;
    selectChannel(channel);
  };

  // Create a new category
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

  // Generic method to edit a channel or category
  const editItem = (item, updates) => {
    if (!selectedServer.value) return;
    const index = selectedServer.value.channels.findIndex((c) => c.id === item.id);
    if (index !== -1) {
      selectedServer.value.channels[index] = {
        ...selectedServer.value.channels[index],
        ...updates,
      };
      if (selectedChannel.value?.id === item.id) {
        selectedChannel.value = selectedServer.value.channels[index];
      }
    }
  };

  // Edit a channel
  const editChannel = (channel) => {
    editItem(channel, {
      name: channel.name,
      parentCategoryId: channel.parentCategoryId,
    });
  };

  // Edit a category
  const editCategory = (category) => {
    editItem(category, {
      name: category.name,
    });
  };

  // Delete a channel
  const deleteChannel = (channel) => {
    if (!selectedServer.value) return;
    selectedServer.value.channels = selectedServer.value.channels.filter((c) => c.id !== channel.id);
    if (selectedChannel.value?.id === channel.id) {
      selectedChannel.value = null;
      const channels = selectedServer.value.channels.filter((c) => !c.isCategory);
      const generalChannel = channels.find((c) => c.name.toLowerCase() === 'general');
      selectChannel(generalChannel || channels[0] || null);
    }
  };

  // Delete a category
  const deleteCategory = (category) => {
    if (!selectedServer.value) return;
    selectedServer.value.channels = selectedServer.value.channels.map((c) =>
      c.parentCategoryId === category.id ? { ...c, parentCategoryId: null } : c
    );
    selectedServer.value.channels = selectedServer.value.channels.filter((c) => c.id !== category.id);
    if (selectedChannel.value?.id === category.id) {
      selectedChannel.value = null;
      const channels = selectedServer.value.channels.filter((c) => !c.isCategory);
      const generalChannel = channels.find((c) => c.name.toLowerCase() === 'general');
      selectChannel(generalChannel || channels[0] || null);
    }
  };

  // Send a message
  const sendMessage = (messageText) => {
    if (!selectedChannel.value || !messageText.trim() || selectedChannel.value.isCategory) return;
    if (!selectedChannel.value.messages) {
      selectedChannel.value.messages = [];
    }
    selectedChannel.value.messages.push({
      id: selectedChannel.value.messages.length + 1,
      text: messageText,
      isMine: true,
      isEdited: false,
      user: {
        id: currentUser.value?.id || 0,
        name: currentUser.value?.name || 'Guest',
        avatar: currentUser.value?.avatar,
      },
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    });
  };

  // Edit a message
  const editMessage = (message) => {
    if (!selectedChannel.value) return;
    const index = selectedChannel.value.messages.findIndex((m) => m.id === message.id);
    if (index !== -1) {
      selectedChannel.value.messages[index] = { ...message, isEdited: true };
    }
  };

  // Delete a message
  const deleteMessage = (message) => {
    if (!selectedChannel.value) return;
    selectedChannel.value.messages = selectedChannel.value.messages.filter((m) => m.id !== message.id);
  };

  // Report a message
  const reportMessage = (message) => {
    console.log('Reported message:', { messageId: message.id, text: message.text });
  };

  // Toggle options menu
  const toggleOptionsMenu = () => {
    showOptionsMenu.value = !showOptionsMenu.value;
  };

  // Show server details
  const showServerDetails = () => {
    console.log('Opening Server Details modal, current showServerDetailsPopup:', showServerDetailsPopup.value);
    showServerDetailsPopup.value = true;
    console.log('Set showServerDetailsPopup to:', showServerDetailsPopup.value);
    showOptionsMenu.value = false;
  };

  // Edit server configuration
  const editServerConfig = () => {
    if (selectedServer.value) {
      editServer.value = { ...selectedServer.value };
      showEditServer.value = true;
      showOptionsMenu.value = false;
    }
  };

  // Save server changes
  const saveServerChanges = () => {
    if (selectedServer.value) {
      const index = servers.value.findIndex((s) => s.id === selectedServer.value.id);
      if (index !== -1) {
        servers.value[index] = {
          ...editServer.value,
          channels: servers.value[index].channels,
          users: servers.value[index].users,
        };
        selectedServer.value = servers.value[index];
      }
      showEditServer.value = false;
    }
  };

  // Handle file upload for editing server
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

  // Handle file upload for new server
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

  // Delete/leave server
  const deleteServer = () => {
    console.log('Opening Leave Server modal');
    showLeaveServerPopup.value = true;
    showOptionsMenu.value = false;
  };

  // Confirm leaving server
  const confirmLeaveServer = () => {
    console.log('Confirming leave server');
    if (selectedServer.value) {
      servers.value = servers.value.filter((s) => s.id !== selectedServer.value.id);
      selectedServer.value = null;
      selectedChannel.value = null;
      showLeaveServerPopup.value = false;
    }
  };

  // Report server
  const reportServer = () => {
    console.log('Opening Report Server modal');
    showReportPopup.value = true;
    showOptionsMenu.value = false;
  };

  // Submit server report
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
    showCreateServer,
    showChannelPopup,
    showCategoryPopup,
    showOptionsMenu,
    showServerDetailsPopup,
    showEditServer,
    showLeaveServerPopup,
    showReportPopup,
    reportReason,
    customReportReason,
    editServer,
    channelEdit,
    categoryEdit,
    onlineUsers,
    lastUsedChannels,
    fetchCurrentUser,
    selectServer,
    selectChannel,
    createServer,
    createChannel,
    createCategory,
    editChannel,
    editCategory,
    deleteChannel,
    deleteCategory,
    sendMessage,
    editMessage,
    deleteMessage,
    reportMessage,
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
