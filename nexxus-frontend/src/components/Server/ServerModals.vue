<template>
  <!-- Create Server Modal -->
  <div
    v-if="showCreateServer"
    class="modal-overlay"
    @click.self="$emit('update:show-create-server', false)"
  >
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">C</span>
        <span class="animate-letter gradient-text" style="--order: 2">r</span>
        <span class="animate-letter gradient-text" style="--order: 3">e</span>
        <span class="animate-letter gradient-text" style="--order: 4">a</span>
        <span class="animate-letter gradient-text" style="--order: 5">t</span>
        <span class="animate-letter gradient-text" style="--order: 6">e</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">a</span>
        <span class="animate-letter gradient-text" style="--order: 9"> </span>
        <span class="animate-letter gradient-text" style="--order: 10">N</span>
        <span class="animate-letter gradient-text" style="--order: 11">e</span>
        <span class="animate-letter gradient-text" style="--order: 12">w</span>
        <span class="animate-letter gradient-text" style="--order: 13"> </span>
        <span class="animate-letter gradient-text" style="--order: 14">S</span>
        <span class="animate-letter gradient-text" style="--order: 15">e</span>
        <span class="animate-letter gradient-text" style="--order: 16">r</span>
        <span class="animate-letter gradient-text" style="--order: 17">v</span>
        <span class="animate-letter gradient-text" style="--order: 18">e</span>
        <span class="animate-letter gradient-text" style="--order: 19">r</span>
      </h2>
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
        <button @click="$emit('create-server')" class="modal-button confirm-button">
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
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">C</span>
        <span class="animate-letter gradient-text" style="--order: 2">r</span>
        <span class="animate-letter gradient-text" style="--order: 3">e</span>
        <span class="animate-letter gradient-text" style="--order: 4">a</span>
        <span class="animate-letter gradient-text" style="--order: 5">t</span>
        <span class="animate-letter gradient-text" style="--order: 6">e</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">a</span>
        <span class="animate-letter gradient-text" style="--order: 9"> </span>
        <span class="animate-letter gradient-text" style="--order: 10">N</span>
        <span class="animate-letter gradient-text" style="--order: 11">e</span>
        <span class="animate-letter gradient-text" style="--order: 12">w</span>
        <span class="animate-letter gradient-text" style="--order: 13"> </span>
        <span class="animate-letter gradient-text" style="--order: 14">C</span>
        <span class="animate-letter gradient-text" style="--order: 15">h</span>
        <span class="animate-letter gradient-text" style="--order: 16">a</span>
        <span class="animate-letter gradient-text" style="--order: 17">n</span>
        <span class="animate-letter gradient-text" style="--order: 18">n</span>
        <span class="animate-letter gradient-text" style="--order: 19">e</span>
        <span class="animate-letter gradient-text" style="--order: 20">l</span>
      </h2>
      <label>Category Name</label>
      <select
        :value="newChannel.category"
        @change="$emit('update:new-channel', { ...newChannel, category: $event.target.value })"
        class="modal-input"
      >
        <option value="">No Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.name">
          {{ category.name }}
        </option>
      </select>
      <label>Channel Name</label>
      <input
        :value="newChannel.name"
        @input="$emit('update:new-channel', { ...newChannel, name: $event.target.value })"
        placeholder="Enter channel name"
        class="modal-input"
      />
      <div class="modal-buttons">
        <button @click="$emit('create-channel')" class="modal-button confirm-button">
          Create
        </button>
        <button
          @click="$emit('update:show-channel-popup', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- Edit Channel Modal -->
  <div
    v-if="showEditChannelPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-edit-channel-popup', false)"
  >
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">E</span>
        <span class="animate-letter gradient-text" style="--order: 2">d</span>
        <span class="animate-letter gradient-text" style="--order: 3">i</span>
        <span class="animate-letter gradient-text" style="--order: 4">t</span>
        <span class="animate-letter gradient-text" style="--order: 5"> </span>
        <span class="animate-letter gradient-text" style="--order: 6">C</span>
        <span class="animate-letter gradient-text" style="--order: 7">h</span>
        <span class="animate-letter gradient-text" style="--order: 8">a</span>
        <span class="animate-letter gradient-text" style="--order: 9">n</span>
        <span class="animate-letter gradient-text" style="--order: 10">n</span>
        <span class="animate-letter gradient-text" style="--order: 11">e</span>
        <span class="animate-letter gradient-text" style="--order: 12">l</span>
      </h2>
      <label>Category Name</label>
      <select
        :value="editChannel.category"
        @change="$emit('update:edit-channel', { ...editChannel, category: $event.target.value })"
        class="modal-input"
      >
        <option value="">No Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.name">
          {{ category.name }}
        </option>
      </select>
      <label>Channel Name</label>
      <input
        :value="editChannel.name"
        @input="$emit('update:edit-channel', { ...editChannel, name: $event.target.value })"
        placeholder="Enter channel name"
        class="modal-input"
      />
      <div class="modal-buttons">
        <button @click="$emit('save-channel-changes')" class="modal-button confirm-button">
          Save
        </button>
        <button
          @click="$emit('update:show-edit-channel-popup', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- Delete Channel Modal -->
  <div
    v-if="showDeleteChannelPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-delete-channel-popup', false)"
  >
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">D</span>
        <span class="animate-letter gradient-text" style="--order: 2">e</span>
        <span class="animate-letter gradient-text" style="--order: 3">l</span>
        <span class="animate-letter gradient-text" style="--order: 4">e</span>
        <span class="animate-letter gradient-text" style="--order: 5">t</span>
        <span class="animate-letter gradient-text" style="--order: 6">e</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">C</span>
        <span class="animate-letter gradient-text" style="--order: 9">h</span>
        <span class="animate-letter gradient-text" style="--order: 10">a</span>
        <span class="animate-letter gradient-text" style="--order: 11">n</span>
        <span class="animate-letter gradient-text" style="--order: 12">n</span>
        <span class="animate-letter gradient-text" style="--order: 13">e</span>
        <span class="animate-letter gradient-text" style="--order: 14">l</span>
      </h2>
      <p>Are you sure you want to delete this channel?</p>
      <div class="modal-buttons">
        <button @click="$emit('confirm-delete-channel')" class="modal-button confirm-button">
          Yes
        </button>
        <button
          @click="$emit('update:show-delete-channel-popup', false)"
          class="modal-button cancel-button"
        >
          No
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
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">C</span>
        <span class="animate-letter gradient-text" style="--order: 2">r</span>
        <span class="animate-letter gradient-text" style="--order: 3">e</span>
        <span class="animate-letter gradient-text" style="--order: 4">a</span>
        <span class="animate-letter gradient-text" style="--order: 5">t</span>
        <span class="animate-letter gradient-text" style="--order: 6">e</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">a</span>
        <span class="animate-letter gradient-text" style="--order: 9"> </span>
        <span class="animate-letter gradient-text" style="--order: 10">N</span>
        <span class="animate-letter gradient-text" style="--order: 11">e</span>
        <span class="animate-letter gradient-text" style="--order: 12">w</span>
        <span class="animate-letter gradient-text" style="--order: 13"> </span>
        <span class="animate-letter gradient-text" style="--order: 14">C</span>
        <span class="animate-letter gradient-text" style="--order: 15">a</span>
        <span class="animate-letter gradient-text" style="--order: 16">t</span>
        <span class="animate-letter gradient-text" style="--order: 17">e</span>
        <span class="animate-letter gradient-text" style="--order: 18">g</span>
        <span class="animate-letter gradient-text" style="--order: 19">o</span>
        <span class="animate-letter gradient-text" style="--order: 20">r</span>
        <span class="animate-letter gradient-text" style="--order: 21">y</span>
      </h2>
      <label>Category Name</label>
      <input
        :value="newCategory.name"
        @input="$emit('update:new-category', { ...newCategory, name: $event.target.value })"
        placeholder="Enter category name"
        class="modal-input"
      />
      <div class="modal-buttons">
        <button @click="$emit('create-category')" class="modal-button confirm-button">
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

  <!-- Edit Category Modal -->
  <div
    v-if="showEditCategoryPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-edit-category-popup', false)"
  >
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">E</span>
        <span class="animate-letter gradient-text" style="--order: 2">d</span>
        <span class="animate-letter gradient-text" style="--order: 3">i</span>
        <span class="animate-letter gradient-text" style="--order: 4">t</span>
        <span class="animate-letter gradient-text" style="--order: 5"> </span>
        <span class="animate-letter gradient-text" style="--order: 6">C</span>
        <span class="animate-letter gradient-text" style="--order: 7">a</span>
        <span class="animate-letter gradient-text" style="--order: 8">t</span>
        <span class="animate-letter gradient-text" style="--order: 9">e</span>
        <span class="animate-letter gradient-text" style="--order: 10">g</span>
        <span class="animate-letter gradient-text" style="--order: 11">o</span>
        <span class="animate-letter gradient-text" style="--order: 12">r</span>
        <span class="animate-letter gradient-text" style="--order: 13">y</span>
      </h2>
      <label>Category Name</label>
      <input
        :value="editCategory.name"
        @input="$emit('update:edit-category', { ...editCategory, name: $event.target.value })"
        placeholder="Enter category name"
        class="modal-input"
      />
      <div class="modal-buttons">
        <button @click="$emit('save-category-changes')" class="modal-button confirm-button">
          Save
        </button>
        <button
          @click="$emit('update:show-edit-category-popup', false)"
          class="modal-button cancel-button"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- Delete Category Modal -->
  <div
    v-if="showDeleteCategoryPopup"
    class="modal-overlay"
    @click.self="$emit('update:show-delete-category-popup', false)"
  >
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">D</span>
        <span class="animate-letter gradient-text" style="--order: 2">e</span>
        <span class="animate-letter gradient-text" style="--order: 3">l</span>
        <span class="animate-letter gradient-text" style="--order: 4">e</span>
        <span class="animate-letter gradient-text" style="--order: 5">t</span>
        <span class="animate-letter gradient-text" style="--order: 6">e</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">C</span>
        <span class="animate-letter gradient-text" style="--order: 9">a</span>
        <span class="animate-letter gradient-text" style="--order: 10">t</span>
        <span class="animate-letter gradient-text" style="--order: 11">e</span>
        <span class="animate-letter gradient-text" style="--order: 12">g</span>
        <span class="animate-letter gradient-text" style="--order: 13">o</span>
        <span class="animate-letter gradient-text" style="--order: 14">r</span>
        <span class="animate-letter gradient-text" style="--order: 15">y</span>
      </h2>
      <p>Are you sure you want to delete this category? All channels within it will be moved to uncategorized.</p>
      <div class="modal-buttons">
        <button @click="$emit('confirm-delete-category')" class="modal-button confirm-button">
          Yes
        </button>
        <button
          @click="$emit('update:show-delete-category-popup', false)"
          class="modal-button cancel-button"
        >
          No
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
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">S</span>
        <span class="animate-letter gradient-text" style="--order: 2">e</span>
        <span class="animate-letter gradient-text" style="--order: 3">r</span>
        <span class="animate-letter gradient-text" style="--order: 4">v</span>
        <span class="animate-letter gradient-text" style="--order: 5">e</span>
        <span class="animate-letter gradient-text" style="--order: 6">r</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">D</span>
        <span class="animate-letter gradient-text" style="--order: 9">e</span>
        <span class="animate-letter gradient-text" style="--order: 10">t</span>
        <span class="animate-letter gradient-text" style="--order: 11">a</span>
        <span class="animate-letter gradient-text" style="--order: 12">i</span>
        <span class="animate-letter gradient-text" style="--order: 13">l</span>
        <span class="animate-letter gradient-text" style="--order: 14">s</span>
      </h2>
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
        <button @click="openEditServer" class="modal-button confirm-button">
          Edit
        </button>
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
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">E</span>
        <span class="animate-letter gradient-text" style="--order: 2">d</span>
        <span class="animate-letter gradient-text" style="--order: 3">i</span>
        <span class="animate-letter gradient-text" style="--order: 4">t</span>
        <span class="animate-letter gradient-text" style="--order: 5"> </span>
        <span class="animate-letter gradient-text" style="--order: 6">S</span>
        <span class="animate-letter gradient-text" style="--order: 7">e</span>
        <span class="animate-letter gradient-text" style="--order: 8">r</span>
        <span class="animate-letter gradient-text" style="--order: 9">v</span>
        <span class="animate-letter gradient-text" style="--order: 10">e</span>
        <span class="animate-letter gradient-text" style="--order: 11">r</span>
      </h2>
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
        <button @click="saveAndShowDetails" class="modal-button confirm-button">
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
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">L</span>
        <span class="animate-letter gradient-text" style="--order: 2">e</span>
        <span class="animate-letter gradient-text" style="--order: 3">a</span>
        <span class="animate-letter gradient-text" style="--order: 4">v</span>
        <span class="animate-letter gradient-text" style="--order: 5">e</span>
        <span class="animate-letter gradient-text" style="--order: 6"> </span>
        <span class="animate-letter gradient-text" style="--order: 7">S</span>
        <span class="animate-letter gradient-text" style="--order: 8">e</span>
        <span class="animate-letter gradient-text" style="--order: 9">r</span>
        <span class="animate-letter gradient-text" style="--order: 10">v</span>
        <span class="animate-letter gradient-text" style="--order: 11">e</span>
        <span class="animate-letter gradient-text" style="--order: 12">r</span>
      </h2>
      <p>Are you sure you want to leave the server?</p>
      <div class="modal-buttons">
        <button
          @click="$emit('confirm-leave-server')"
          class="modal-button confirm-button"
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
    <div class="modal animate-fade-in">
      <h2>
        <span class="animate-letter gradient-text" style="--order: 1">R</span>
        <span class="animate-letter gradient-text" style="--order: 2">e</span>
        <span class="animate-letter gradient-text" style="--order: 3">p</span>
        <span class="animate-letter gradient-text" style="--order: 4">o</span>
        <span class="animate-letter gradient-text" style="--order: 5">r</span>
        <span class="animate-letter gradient-text" style="--order: 6">t</span>
        <span class="animate-letter gradient-text" style="--order: 7"> </span>
        <span class="animate-letter gradient-text" style="--order: 8">S</span>
        <span class="animate-letter gradient-text" style="--order: 9">e</span>
        <span class="animate-letter gradient-text" style="--order: 10">r</span>
        <span class="animate-letter gradient-text" style="--order: 11">v</span>
        <span class="animate-letter gradient-text" style="--order: 12">e</span>
        <span class="animate-letter gradient-text" style="--order: 13">r</span>
      </h2>
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
        <button @click="$emit('submit-report')" class="modal-button confirm-button">
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
import { computed } from 'vue';
import { useServer } from '@/composables/Server/useServer';

const { selectedServer } = useServer();

defineProps({
  showCreateServer: Boolean,
  newServer: Object,
  showChannelPopup: Boolean,
  newChannel: Object,
  showCategoryPopup: Boolean,
  newCategory: Object,
  showServerDetailsPopup: Boolean,
  selectedServer: Object,
  showEditServer: Boolean,
  editServer: Object,
  showLeaveServerPopup: Boolean,
  showReportPopup: Boolean,
  reportReason: String,
  customReportReason: String,
  showEditChannelPopup: Boolean,
  editChannel: Object,
  showDeleteChannelPopup: Boolean,
  showEditCategoryPopup: Boolean,
  editCategory: Object,
  showDeleteCategoryPopup: Boolean,
});

defineEmits([
  'update:show-create-server',
  'create-server',
  'update:show-channel-popup',
  'create-channel',
  'update:show-category-popup',
  'create-category',
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
  'update:show-edit-channel-popup',
  'update:edit-channel',
  'save-channel-changes',
  'update:show-delete-channel-popup',
  'confirm-delete-channel',
  'update:show-edit-category-popup',
  'update:edit-category',
  'save-category-changes',
  'update:show-delete-category-popup',
  'confirm-delete-category',
]);

// Compute categories for channel creation/editing
const categories = computed(() => {
  return selectedServer.value?.channels.filter((c) => c.isCategory) || [];
});

const openEditServer = () => {
  emit('update:show-server-details-popup', false);
  emit('update:show-edit-server', true);
};

const saveAndShowDetails = () => {
  emit('save-server-changes');
  emit('update:show-edit-server', false);
  emit('update:show-server-details-popup', true);
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: #fff8e1;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-letter {
  display: inline-block;
  opacity: 0;
  animation: letter 0.5s ease-out forwards;
  animation-delay: calc(var(--order) * 0.1s);
}

@keyframes letter {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.gradient-text {
  background-image: linear-gradient(to right, #f59e0b, #d97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.modal h2 {
  margin-bottom: 8px;
}

.modal p {
  color: #78350f;
  font-size: 14px;
}

.modal label {
  font-size: 14px;
  color: #78350f;
  margin-bottom: 4px;
}

.modal-input,
.modal-textarea {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #e5d5b3;
  font-size: 14px;
  color: #78350f;
  background: #fef3c7;
  outline: none;
  transition: all 0.3s ease;
}

.modal-input:focus,
.modal-textarea:focus {
  border-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
}

.modal-textarea {
  height: 80px;
  resize: none;
}

.visibility-options {
  display: flex;
  gap: 20px;
  font-size: 14px;
  color: #78350f;
}

.visibility-options label {
  display: flex;
  align-items: center;
  gap: 8px;
}

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 16px;
}

.modal-button {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.confirm-button {
  background: linear-gradient(to right, #f59e0b, #d97706);
  color: #78350f;
}

.confirm-button:hover {
  background: linear-gradient(to right, #f59e0b, #b45309);
  transform: translateY(-0.5px);
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.cancel-button {
  background: #78350f;
  color: #fff8e1;
}

.cancel-button:hover {
  background: #7c2d12;
  transform: translateY(-0.5px);
}

.server-icon {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  margin-bottom: 10px;
}
</style>
