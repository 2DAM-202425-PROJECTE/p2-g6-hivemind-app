<template>
  <div v-if="visible" class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <div class="modal-header">
        <span>Comments</span>
        <button @click="close">X</button>
      </div>
      <div class="modal-body">
        <div v-for="comment in comments" :key="comment.id" class="comment">
          <img :src="comment.user.profile_photo_path" alt="Profile" class="comment-profile-pic" />
          <div class="comment-text">
            <strong>{{ comment.user.name }}</strong>
            <p class="date_created_at">{{ comment.created_at}}</p>
            <p>{{ comment.content }}</p>
          </div>

        </div>
        <div class="add-comment">
          <input v-model="newComment" placeholder="Add a comment..." />
          <button @click="addComment(post)">Post</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../axios'

const props = defineProps({
  visible: Boolean,
  comments: Array,
  post: Object,
  currentUser: Object

});

const emit = defineEmits(['close', 'add-comment']);

const newComment = ref('');

const close = () => {
  emit('close');
};

const addComment = async (post) => {

  localStorage.setItem('newComment', newComment.value);

  if (!newComment.value) {
    alert('Please enter a comment!');
    return;
  }

  const formData = new FormData();
  formData.append('content', newComment.value);

  try {
    const response = await axios.post(`http://localhost:8000/api/posts/${post.id}/comments`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        Accept: 'application/json',
        'Content-Type': 'multipart/form-data',
      },
    });
    alert('Comment created successfully!');
    newComment.value = '';
    window.location.href = '/home';
  } catch (error) {
    console.error(error);
    alert('Failed to create comment.');
  }
}
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
  text-align: center;
}

.comment {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  justify-content: center;
}

.comment-profile-pic {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-right: 10px;
}

.comment-text {
  flex: 1;
  text-align: left;
}

.add-comment {
  display: flex;
  gap: 10px;
  margin-top: 20px;
  justify-content: center;
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

.date_created_at{
  font-size: smaller;
  margin-bottom: 5px;
}
</style>
