<template>
  <!-- Create Server Modal -->
  <div
    v-if="showCreateServer"
    class="modal-overlay"
    @click.self="$emit('update:show-create-server', false)"
  >
    <div class="modal">
      <h2>Create a New Server</h2>
      <label>Server Name</label>
      <input
        :value="newServer.name"
        @input="$emit('update:new-server', { ...newServer, name: $event.target.value })"
        placeholder="Enter server name"
        class="modal-input"
      />
      <label>Description</label>
      <textarea
        :value="newServer.description"
        @input="$emit('update:new-server', { ...newServer, description: $event.target.value })"
        placeholder="Describe your server..."
        class="modal-textarea"
      ></textarea>
      <label>Server Icon</label>
      <input type="file" @change="$emit('handle-file-upload', $event)" class="modal-input" />
      <label>Category</label>
      <select
        :value="newServer.category"
        @change="$emit('update:new-server', { ...newServer, category: $event.target.value })"
        class="modal-input"
      >
        <option value="Gaming">Gaming</option>
        <option value="Education">Education</option>
        <option value="Technology">Technology</option>
        <option value="Community">Community</option>
      </select>
      <label>Visibility</label>
      <div class="visibility-options">
        <label>
          <input
            type="radio"
            :checked="newServer.visibility === 'Public'"
            value="Public"
            @change="$emit('update:new-server', { ...newServer, visibility: $event.target.value })"
          /> Public
        </label>
        <label>
          <input
            type="radio"
            :checked="newServer.visibility === 'Private'"
            value="Private"
            @change="$emit('update:new-server', { ...newServer, visibility: $event.target.value })"
          /> Private
        </label>
      </div>
      <div class="modal-buttons">
        <button @click="$emit('create-server')" class="modal-button create-modal-button">
          Create
        </button>
        <button
          @click="$emit('update:show-create-server', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- Create Channel Modal -->
  <div
    v-if="showChannelPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-channel-popup', false)"
  >
    <div class="modal">
      <h2>Create a New Channel</h2>
      <label>Category Name</label>
      <input
        :value="newChannel.category"
        @input="$emit('update:new-channel', { ...newChannel, category: $event.target.value })"
        placeholder="Enter category name"
        class="modal-input"
      />
      <label>Channel Name</label>
      <input
        :value="newChannel.name"
        @input="$emit('update:new-channel', { ...newChannel, name: $event.target.value })"
        placeholder="Enter channel name"
        class="modal-input"
      />
      <div class="modal-buttons">
        <button @click="$emit('create-channel')" class="modal-button create-modal-button">
          Create
        </button>
        <button
          @click="$emit('update:show-channel-popup', false)"
          class="modal-button cancel-button"
        >
        </button>
      </div>
    </div>
  </div>

  <!-- Create Category Modal -->
  <div
    v-if="showCategoryPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-category-popup', false)"
  >
    <div class="modal">
      <h2>Create a New Category</h2>
      <label>Category Name</label>
      <input
        :value="newCategory.name"
        @input="$emit('update:new-category', { ...newCategory, name: $event.target.value })"
        placeholder="Enter category name"
        class="modal-input"
      />
      <div class="modal-buttons">
        <button @click="$emit('create-category')" class="modal-button create-modal-button">
          Create
        </button>
        <button
          @click="$emit('update:show-category-popup', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- Server Details Modal -->
  <div
    v-if="showServerDetailsPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-server-details-popup', false)"
  >
    <div class="modal">
      <h2>Server Details</h2>
      <label>Server Name</label>
      <input :value="selectedServer.name" disabled class="modal-input" />
      <label>Description</label>
      <textarea :value="selectedServer.description" disabled class="modal-textarea"></textarea>
      <label>Server Icon</label>
      <img :src="selectedServer.icon" alt="Server Icon" class="server-icon" />
      <label>Category</label>
      <input :value="selectedServer.category" disabled class="modal-input" />
      <label>Visibility</label>
      <input :value="selectedServer.visibility" disabled class="modal-input" />
      <div class="modal-buttons">
        <button
          @click="$emit('update:show-server-details-popup', false)"
          class="modal-button cancel-button"
        >
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Edit Server Modal -->
  <div
    v-if="showEditServer"
    class="modal-overlay"
    @click.self="$emit('update:show-edit-server', false)"
  >
    <div class="modal">
      <h2>Edit Server</h2>
      <label>Server Name</label>
      <input
        :value="editServer.name"
        @input="$emit('update:edit-server', { ...editServer, name: $event.target.value })"
        placeholder="Enter server name"
        class="modal-input"
      />
      <label>Description</label>
      <textarea
        :value="editServer.description"
        @input="$emit('update:edit-server', { ...editServer, description: $event.target.value })"
        placeholder="Describe your server..."
        class="modal-textarea"
      ></textarea>
      <label>Server Icon</label>
      <input type="file" @change="$emit('handle-edit-file-upload', $event)" class="modal-input" />
      <label>Category</label>
      <select
        :value="editServer.category"
        @change="$emit('update:edit-server', { ...editServer, category: $event.target.value })"
        class="modal-input"
      >
        <option value="Gaming">Gaming</option>
        <option value="Education">Education</option>
        <option value="Technology">Technology</option>
        <option value="Community">Community</option>
      </select>
      <label>Visibility</label>
      <div class="visibility-options">
        <label>
          <input
            type="radio"
            :checked="editServer.visibility === 'Public'"
            value="Public"
            @change="$emit('update:edit-server', { ...editServer, visibility: $event.target.value })"
          /> Public
        </label>
        <label>
          <input
            type="radio"
            :checked="editServer.visibility === 'Private'"
            value="Private"
            @change="$emit('update:edit-server', { ...editServer, visibility: $event.target.value })"
          /> Private
        </label>
      </div>
      <div class="modal-buttons">
        <button
          @click="$emit('save-server-changes')"
          class="modal-button create-modal-button"
        >
          Save Changes
        </button>
        <button
          @click="$emit('update:show-edit-server', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- Leave Server Confirmation Modal -->
  <div
    v-if="showLeaveServerPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-leave-server-popup', false)"
  >
    <div class="modal">
      <h2>Leave Server</h2>
      <p>Are you sure you want to leave the server?</p>
      <div class="modal-buttons">
        <button
          @click="$emit('confirm-leave-server')"
          class="modal-button create-modal-button"
        >
          Yes
        </button>
        <button
          @click="$emit('update:show-leave-server-popup', false)"
          class="modal-button cancel-button"
        >
          No
        </button>
      </div>
    </div>
  </div>

  <!-- Report Server Modal -->
  <div
    v-if="showReportPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-report-popup', false)"
  >
    <div class="modal">
      <h2>Report Server</h2>
      <label>Reason</label>
      <select
        :value="reportReason"
        @change="$emit('update:report-reason', $event.target.value)"
        class="modal-input"
      >
        <option value="Spam">Spam</option>
        <option value="Harassment">Harassment</option>
        <option value="Inappropriate Content">Inappropriate Content</option>
      </select>
      <label>Custom Reason</label>
      <textarea
        :value="customReportReason"
        @input="$emit('update:custom-report-reason', $event.target.value)"
        placeholder="Describe your reason..."
        class="modal-textarea"
      ></textarea>
      <div class="modal-buttons">
        <button @click="$emit('submit-report')" class="modal-button create-modal-button">
          Submit
        </button>
        <button
          @click="$emit('update:show-report-popup', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  showCreateServer: Boolean,
  newServer: Object,
  showChannelPopup: Boolean,
  newChannel: Object,
  showCategoryPopup: Boolean,
  newCategory: Object,
  showOptionsMenu: Boolean,
  showServerDetailsPopup: Boolean,
  selectedServer: Object,
  showEditServer: Boolean,
  editServer: Object,
  showLeaveServerPopup: Boolean,
  showReportPopup: Boolean,
  reportReason: String,
  customReportReason: String,
});

defineEmits([
  'update:show-create-server',
  'create-server',
  'update:show-channel-popup',
  'create-channel',
  'update:show-category-popup',
  'create-category',
  'update:show-options-menu',
  'update:show-server-details-popup',
  'update:show-edit-server',
  'save-server-changes',
  'handle-edit-file-upload',
  'update:show-leave-server-popup',
  'confirm-leave-server',
  'update:show-report-popup',
  'submit-report',
  'handle-file-upload',
  'update:new-server',
  'update:new-channel',
  'update:new-category',
  'update:edit-server',
  'update:report-reason',
  'update:custom-report-reason',
]);
</script>

<style scoped>
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

.modal-input,
.modal-textarea,
.modal-button,
.visibility-options label {
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

.create-modal-button {
  background-color: white;
  color: black;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
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

.server-icon {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  margin-bottom: 10px;
}
</style>
