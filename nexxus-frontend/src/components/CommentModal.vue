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
            <p class="date_created_at">{{ comment.created_at }}</p>
            <p>{{ comment.content }}</p>
          </div>
          <div class="comment-menu">
            <button @click="toggleCommentMenu(comment.id)">
              <i class="mdi mdi-dots-vertical"></i>
            </button>
            <div v-if="commentMenuVisible === comment.id" class="dropdown-menu">
              <ul>
                <li v-if="isCommentAuthor(comment)" @click="editComment(comment)">Edit</li>
                <li v-if="isCommentAuthor(comment)" @click="deleteComment(comment)">Delete</li>
                <li v-else @click="reportComment(comment)">Report</li>
                <li v-else @click="respondToComment(comment)">Respond</li>
              </ul>
            </div>
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
import { ref, computed } from 'vue';
import axios from '../axios';

const props = defineProps({
  visible: Boolean,
  comments: Array,
  post: Object,
  currentUser: Object
});

const emit = defineEmits(['close', 'add-comment']);

const newComment = ref('');
const commentMenuVisible = ref(null);

const close = () => {
  emit('close');
};

const toggleCommentMenu = (commentId) => {
  commentMenuVisible.value = commentMenuVisible.value === commentId ? null : commentId;
};

const addComment = async (post) => {
  if (!newComment.value) {
    alert('Please enter a comment!');
    return;
  }

  const formData = new FormData();
  formData.append('content', newComment.value);

  try {
    await axios.post(`http://localhost:8000/api/posts/${post.id}/comments`, formData, {
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
};

const isCommentAuthor = (comment) => {
  return comment.user.id === props.currentUser.id;
};

const editComment = (comment) => {
  // Implement edit comment logic
};

const deleteComment = (comment) => {
  // Implement delete comment logic
};

const reportComment = (comment) => {
  // Implement report comment logic
};

const respondToComment = (comment) => {
  // Implement respond to comment logic
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
  background-color: white;
  border-radius: 10px;
  padding: 40px;
  border: 3px solid #ccc;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
}

.comment {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  justify-content: space-between;
  padding: 10px;
  border: 1px solid #ccc;
  background: #f9f9f9;
  position: relative;
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

.comment-menu {
  position: absolute;
  right: 10px;
  top: 10px;
}

.comment-menu button {
  background: none;
  border: none;
  cursor: pointer;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #d3d3d3;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown-menu li {
  padding: 10px;
  cursor: pointer;
}

.dropdown-menu li:hover {
  background: #f0f0f0;
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

.comment-text strong {
  display: inline-block;
  margin-right: 10px;
}

.comment-text .date_created_at {
  display: inline-block;
  font-size: smaller;
  color: #888;
}

.comment-text p {
  margin: 5px 0 0 0;
}
</style>
