<template>
  <div v-if="visible" class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Comments</h3>
        <button @click="close">X</button>
      </div>
      <div class="modal-body">
        <div v-for="comment in comments" :key="comment.id" class="comment">
          <strong>{{ comment.user.name }}</strong>
          <p>{{ comment.text }}</p>
        </div>
        <div class="add-comment">
          <input v-model="newComment" placeholder="Add a comment..." />
          <button @click="addComment">Post</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  visible: Boolean,
  comments: Array,
});

const emit = defineEmits(['close', 'add-comment']);

const newComment = ref('');

const close = () => {
  emit('close');
};

const addComment = () => {
  if (newComment.value.trim()) {
    emit('add-comment', newComment.value);
    newComment.value = '';
  }
};
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
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 500px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  margin-top: 20px;
}

.comment {
  margin-bottom: 10px;
}

.add-comment {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.add-comment input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.add-comment button {
  padding: 10px 20px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
</style>
