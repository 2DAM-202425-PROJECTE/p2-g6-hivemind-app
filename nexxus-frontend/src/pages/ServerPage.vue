<template>
  <div class="server-container" @click="handleClickOutside">
    <Navbar />
    <div class="server-content">
      <div class="sidebar">
        <h2>Servers</h2>
        <input v-model="searchQuery" placeholder="Search servers..." class="search-input" />
        <button @click="showCreateServer = true" class="create-server-button">Create Server</button>
        <ul class="server-list">
          <li v-for="server in filteredServers" :key="server.id" @click="selectServer(server)" class="server-item">
            {{ server.name }}
          </li>
        </ul>
      </div>
      <div class="main-content">
        <div class="server-header">
          <h2 id="server-name">{{ selectedServer ? selectedServer.name : 'Nom del servidor' }}</h2>
          <button id="server-options-btn" class="options-button" @click="toggleOptionsMenu">⋮</button>
          <div id="server-options-menu" class="options-menu" :class="{ hidden: !showOptionsMenu }">
            <ul>
              <li @click="editServerConfig">Server Settings</li>
              <li @click="deleteServer">Leave Server</li>
              <li @click="reportServer">Report Server</li>
            </ul>
          </div>
        </div>

        <div class="chat-and-users">
          <div class="channels">
            <ul v-if="selectedServer" class="channel-list">
              <li v-for="channel in selectedServer.channels" :key="channel.id" class="channel-item">
                <div class="channel-info">
                  <span>
                    <i v-if="channel.isCategory" class="fas fa-folder"></i>
                    <i v-else class="fas fa-comments"></i>
                    {{ channel.name }}
                  </span>
                  <button @click.stop="toggleCreateOptions" class="create-button">+</button>
                </div>
                <div v-if="showCreateOptions" class="dropdown-menu">
                  <ul>
                    <li @click="showChannelPopup = true">Create Channel</li>
                    <li @click="showCategoryPopup = true">Create Category</li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
          <div class="chatbox">
            <div v-if="selectedChannel">
              <h3>{{ selectedChannel.name }}</h3>
              <div class="channel-bar"></div>
              <div class="chat-messages">
                <div v-for="(message, index) in selectedChannel.messages" :key="index" class="message">
                  <div :class="{'my-message': message.isMine}">
                    {{ message.text }}
                  </div>
                </div>
              </div>
              <div class="chat-input">
                <v-text-field v-model="newMessage" label="Type a message" @keyup.enter="sendMessage" />
                <v-btn @click="sendMessage">Send</v-btn>
              </div>
            </div>
          </div>
          <div v-if="selectedServer" class="online-users">
            <h3>Online Users</h3>
            <ul>
              <li v-for="user in onlineUsers" :key="user.id">
                {{ user.name }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <Footer />

    <!-- Create Server Modal -->
    <div v-if="showCreateServer" class="modal-overlay" @click.self="showCreateServer = false">
      <div class="modal">
        <h2>Create a New Server</h2>
        <label>Server Name</label>
        <input v-model="newServer.name" placeholder="Enter server name" class="modal-input" />
        <label>Description</label>
        <textarea v-model="newServer.description" placeholder="Describe your server..." class="modal-textarea"></textarea>
        <label>Server Icon</label>
        <input type="file" @change="handleFileUpload" class="modal-input" />
        <label>Category</label>
        <select v-model="newServer.category" class="modal-input">
          <option value="Gaming">Gaming</option>
          <option value="Education">Education</option>
          <option value="Technology">Technology</option>
          <option value="Community">Community</option>
        </select>
        <label>Visibility</label>
        <div class="visibility-options">
          <label><input type="radio" v-model="newServer.visibility" value="Public" /> Public</label>
          <label><input type="radio" v-model="newServer.visibility" value="Private" /> Private</label>
        </div>
        <div class="modal-buttons">
          <button @click="createServer" class="modal-button create-modal-button">Create</button>
          <button @click="showCreateServer = false" class="modal-button cancel-button">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Create Channel Modal -->
    <div v-if="showChannelPopup" class="modal-overlay" @click.self="showChannelPopup = false">
      <div class="modal">
        <h2>Create a New Channel</h2>
        <label>Channel Name</label>
        <input v-model="newChannel.name" placeholder="Enter channel name" class="modal-input" />
        <div class="modal-buttons">
          <button @click="createChannel" class="modal-button create-modal-button">Create</button>
          <button @click="showChannelPopup = false" class="modal-button cancel-button">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Create Category Modal -->
    <div v-if="showCategoryPopup" class="modal-overlay" @click.self="showCategoryPopup = false">
      <div class="modal">
        <h2>Create a New Category</h2>
        <label>Category Name</label>
        <input v-model="newCategory.name" placeholder="Enter category name" class="modal-input" />
        <div class="modal-buttons">
          <button @click="createCategory" class="modal-button create-modal-button">Create</button>
          <button @click="showCategoryPopup = false" class="modal-button cancel-button">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Create Options Modal -->
    <div v-if="showCreateOptions" class="modal-overlay" @click.self="showCreateOptions = false">
      <div class="modal">
        <h2>Create Options</h2>
        <button @click="showChannelPopup = true; showCreateOptions = false" class="modal-button create-options-button">Create Channel</button>
        <button @click="showCategoryPopup = true; showCreateOptions = false" class="modal-button create-options-button">Create Category</button>
        <button @click="showCreateOptions = false" class="modal-button cancel-button">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Server Options Modal -->
  <div v-if="showOptionsMenu" class="modal-overlay" @click.self="toggleOptionsMenu">
    <div class="modal server-options-modal">
      <h2>Server Options</h2>
      <ul>
        <li @click="editServerConfig">Server Settings</li>
        <li @click="deleteServer">Leave Server</li>
        <li @click="reportServer">Report Server</li>
      </ul>
      <div class="modal-buttons">
        <button @click="toggleOptionsMenu" class="modal-button cancel-button">Cancel</button>
      </div>
    </div>
  </div>

  <div v-if="showEditServer" class="modal-overlay" @click.self="showEditServer = false">
    <div class="modal">
      <h2>Edit Server</h2>
      <label>Server Name</label>
      <input v-model="editServer.name" placeholder="Enter server name" class="modal-input" />
      <label>Description</label>
      <textarea v-model="editServer.description" placeholder="Describe your server..." class="modal-textarea"></textarea>
      <label>Server Icon</label>
      <input type="file" @change="handleEditFileUpload" class="modal-input" />
      <label>Category</label>
      <select v-model="editServer.category" class="modal-input">
        <option value="Gaming">Gaming</option>
        <option value="Education">Education</option>
        <option value="Technology">Technology</option>
        <option value="Community">Community</option>
      </select>
      <label>Visibility</label>
      <div class="visibility-options">
        <label><input type="radio" v-model="editServer.visibility" value="Public" /> Public</label>
        <label><input type="radio" v-model="editServer.visibility" value="Private" /> Private</label>
      </div>
      <div class="modal-buttons">
        <button @click="saveServerChanges" class="modal-button create-modal-button">Save Changes</button>
        <button @click="showEditServer = false" class="modal-button cancel-button">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Report Server Modal -->
  <div v-if="showReportPopup" class="modal-overlay" @click.self="showReportPopup = false">
    <div class="modal">
      <h2>Report Server</h2>
      <label>Reason</label>
      <select v-model="reportReason" class="modal-input">
        <option value="Spam">Spam</option>
        <option value="Harassment">Harassment</option>
        <option value="Inappropriate Content">Inappropriate Content</option>
      </select>
      <label>Custom Reason</label>
      <textarea v-model="customReportReason" placeholder="Describe your reason..." class="modal-textarea"></textarea>
      <div class="modal-buttons">
        <button @click="submitReport" class="modal-button create-modal-button">Submit</button>
        <button @click="showReportPopup = false" class="modal-button cancel-button">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'

const searchQuery = ref("")
const showCreateServer = ref(false)
const showChannelPopup = ref(false)
const showCategoryPopup = ref(false)
const showCreateOptions = ref(false)
const newServer = ref({
  name: "",
  description: "",
  category: "Gaming",
  visibility: "Public",
  icon: null,
})
const newChannel = ref({
  name: "",
})
const newCategory = ref({
  name: "",
})
const servers = ref([{ id: 1, name: "Test Server", description: "A test server", category: "Gaming", visibility: "Public", channels: [{ id: 1, name: "General", messages: [] }], users: [{ id: 1, name: "User1", online: true }, { id: 2, name: "User2", online: false }] }])
const selectedServer = ref(null)
const selectedChannel = ref(null)
const newMessage = ref("")

const filteredServers = computed(() => {
  return servers.value.filter(server => server.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
})

const selectServer = (server) => {
  selectedServer.value = server
  selectedChannel.value = server.channels.length > 0 ? server.channels[0] : null
}
const createServer = () => {
  if (newServer.value.name.trim()) {
    servers.value.push({ id: Date.now(), ...newServer.value, channels: [], users: [] })
    newServer.value = { name: "", description: "", category: "Gaming", visibility: "Public", icon: null }
    showCreateServer.value = false
  }
}

const createChannel = () => {
  if (newChannel.value.name.trim()) {
    selectedServer.value.channels.push({ id: Date.now(), name: newChannel.value.name, messages: [] })
    newChannel.value = { name: "" }
    showChannelPopup.value = false
    selectedChannel.value = selectedServer.value.channels[selectedServer.value.channels.length - 1]
  }
}

const createCategory = () => {
  if (newCategory.value.name.trim()) {
    selectedServer.value.channels.push({ id: Date.now(), name: newCategory.value.name, messages: [], isCategory: true })
    newCategory.value = { name: "" }
    showCategoryPopup.value = false
  }
}

const sendMessage = () => {
  if (newMessage.value.trim() !== '') {
    selectedChannel.value.messages.push({ text: newMessage.value, isMine: true })
    newMessage.value = ''
  }
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    newServer.value.icon = URL.createObjectURL(file)
  }
}

const toggleCreateOptions = () => {
  showCreateOptions.value = !showCreateOptions.value
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.dropdown-menu') && !event.target.closest('.create-button')) {
    showCreateOptions.value = false
  }
}

const onlineUsers = computed(() => {
  return selectedServer.value ? selectedServer.value.users.filter(user => user.online) : []
})

onMounted(() => {
  window.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside)
})

document.addEventListener("DOMContentLoaded", () => {
  const optionsBtn = document.getElementById("server-options-btn");
  const optionsMenu = document.getElementById("server-options-menu");

  optionsBtn.addEventListener("click", (event) => {
    event.stopPropagation();
    optionsMenu.classList.toggle("hidden");
  });

  document.addEventListener("click", () => {
    optionsMenu.classList.add("hidden");
  });

  optionsMenu.addEventListener("click", (event) => {
    event.stopPropagation();
  });
});

const showReportPopup = ref(false);
const reportReason = ref("");
const customReportReason = ref("");

const showEditServer = ref(false);
const editServer = ref({
  name: "",
  description: "",
  category: "Gaming",
  visibility: "Public",
  icon: null,
});



const editServerConfig = () => {
  editServer.value = { ...selectedServer.value };
  showEditServer.value = true;
};

const saveServerChanges = () => {
  if (editServer.value.name.trim()) {
    Object.assign(selectedServer.value, editServer.value);
    showEditServer.value = false;
  }
};

const handleEditFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    editServer.value.icon = URL.createObjectURL(file);
  }
};

const deleteServer = () => {
  servers.value = servers.value.filter(server => server.id !== selectedServer.value.id);
  selectedServer.value = null;
};

const reportServer = () => {
  showReportPopup.value = true;
};

const submitReport = () => {
  console.log(`Report reason: ${reportReason.value}`);
  console.log(`Custom report reason: ${customReportReason.value}`);
  showReportPopup.value = false;
  reportReason.value = "";
  customReportReason.value = "";
};

const showOptionsMenu = ref(false);

const toggleOptionsMenu = () => {
  showOptionsMenu.value = !showOptionsMenu.value;
};

</script>

<style scoped>
.server-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: #f0f2f5;
  min-height: 100vh;
  color: #1c1e21;
  padding-bottom: 90px;
  display: flex;
  flex-direction: column;
  padding-top: 70px; /* Space for Navbar */
}

.server-content {
  display: flex;
  gap: 20px;
  flex: 1;
}

.sidebar {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 300px;
  width: 100%;
  overflow-y: auto;
  flex: 1;
}

.search-input, .create-server-button, .server-item, .modal-input, .modal-textarea, .modal-button {
  background-color: #7f7f7f;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  margin-bottom: 10px;
}

.search-input:hover, .create-server-button:hover, .server-item:hover, .modal-button:hover {
  background-color: #0056b3;
}

.main-content {
  flex: 3;
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.server-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.spacer {
  width: 20px;
}

.create-options {
  position: relative;
}

.create-button {
  background-color: white;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.create-button:hover {
  background-color: #f0f0f0;
}

.create-options-button {
  background-color: #7f7f7f;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  margin-bottom: 10px;
}

.create-options-button:hover {
  background-color: #0056b3;
}

.create-modal-button {
  background-color: white;
  color: black;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  margin-bottom: 10px;
}

.create-modal-button:hover {
  background-color: #f0f0f0;
}

.dropdown-menu {
  position: absolute;
  top: 0;
  left: 100%;
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown-menu li {
  padding: 10px;
  cursor: pointer;
  background-color: #7f7f7f;
  color: white;
  border-radius: 5px;
}

.dropdown-menu li:hover {
  background-color: #0056b3;
}

.channel-list {
  margin-bottom: 20px;
}

.channel-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  cursor: pointer;
  border-bottom: 1px solid #e0e0e0;
}

.channel-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-and-users {
  display: flex;
  gap: 20px;
  flex: 1;
}

.channels {
  flex: 1;
  max-width: 200px;
  overflow-y: auto;
}

.channel-bar {
  margin-top: 1px;
  height: 1px;
  background-color: #7e7e7e; /* Adjust the color as needed */
  margin-bottom: 10px; /* Adjust the spacing as needed */
}

.chatbox {
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  margin-top: 20px;
  width: 100%;
  flex-direction: column;
  flex: 1;
  position: relative; /* Add this line */
  display: flex;
  height: 100%;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column-reverse;
  margin-bottom: 20px; /* Add this line to create a gap */
}

.message {
  margin-bottom: 10px;
  display: flex;
  justify-content: flex-start;
}

.my-message {
  background-color: #d1ffd1;
  padding: 15px;
  border-radius: 10px;
  display: inline-block;
  max-width: 70%;
  word-wrap: break-word;
}

.their-message {
  text-align: left;
  background-color: #f0f0f0;
  padding: 15px;
  border-radius: 10px;
  display: inline-block;
  max-width: 70%;
  word-wrap: break-word;
}

.chat-input {
  display: flex;
  gap: 10px;
  align-items: center;
  padding: 10px;
  background-color: #fff;
  border-top: 1px solid #ddd;
  position: absolute;
  bottom: 0;
  width: calc(100% - 40px); /* Adjust width to account for padding */
}

.online-users {
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  margin-top: 20px;
  max-width: 200px;
  width: 100%;
  overflow-y: auto;
  flex: 1;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.modal-input, .modal-textarea, .modal-button, .visibility-options label {
  width: 100%;
  padding: 12px;
  border-radius: 6px;
  border: 1px solid #ddd;
  font-size: 16px;
}

.modal-textarea {
  height: 80px;
  resize: none;
}

.visibility-options {
  display: flex;
  gap: 20px;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 15px;
}

.create-button {
  background-color: white;
}

.create-button:hover {
  background-color: #f0f0f0;
}

.create-options-button {
  background-color: #7f7f7f;
}

.create-options-button:hover {
  background-color: #0056b3;
}

.create-modal-button {
  background-color: white;
}

.create-modal-button:hover {
  background-color: #f0f0f0;
}

.cancel-button {
  background-color: #ff0000;
}

.cancel-button:hover {
  background-color: #cc0000;
}

.options-button {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  margin-left: 10px;
}

.options-menu {
  position: absolute;
  right: 10px;
  top: 40px;
  background: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
  display: none;
  z-index: 100;
}

.options-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.options-menu li {
  padding: 10px;
  cursor: pointer;
  background-color: #7f7f7f; /* Set background color to grey */
  color: white; /* Ensure text color is white for contrast */
  border-radius: 5px;
}

.options-menu li:hover {
  background-color: #0056b3; /* Change hover color to a darker shade */
}

.hidden {
  display: none;
}

.server-options-modal {
  background-color: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.server-options-modal h2 {
  color: black; /* Change the title text color to black */
}

.server-options-modal ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px; /* Add gap between the list items */
}

.server-options-modal li {
  padding: 10px;
  cursor: pointer;
  background-color: #7f7f7f;
  color: white; /* Ensure button text color is white */
  border-radius: 5px;
}

.server-options-modal li:hover {
  background-color: #0056b3;
}

.server-options-modal .modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 15px;
}

.server-options-modal .modal-button {
  background-color: #7f7f7f;
  color: white; /* Ensure button text color is white */
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
}

.server-options-modal .modal-button:hover {
  background-color: #0056b3;
}

.server-options-modal .cancel-button {
  background-color: #ff0000;
}

.server-options-modal .cancel-button:hover {
  background-color: #cc0000;
}
</style>
