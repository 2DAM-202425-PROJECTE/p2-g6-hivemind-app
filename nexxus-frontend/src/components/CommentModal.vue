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
                <li v-if="!isCommentAuthor(comment)" @click="reportComment(comment)">Report</li>
                <li v-if="!isCommentAuthor(comment)" @click="respondToComment(comment)">Respond</li>
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
    <v-dialog v-model="editCommentPopup" max-width="500">
      <v-card>
        <v-card-title>Edit Comment</v-card-title>
        <v-card-text>
          <v-text-field v-model="editCommentContent" label="Comment" outlined>
          </v-text-field>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="cancelEditComment">Cancel</v-btn>
          <v-btn color="primary" @click="saveEditComment">Update Comment</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '../axios';

const isDeleting = ref(false);
const newComment = ref('');
const commentMenuVisible = ref(null);
const editCommentPopup = ref(false);
const selectedComment = ref(null);
const editCommentContent = ref('');

const props = defineProps({
  visible: Boolean,
  comments: {
    type: Array,
    default: () => [],
  },
  post: {
    type: Object,
    default: () => ({}),
  },
  currentUser: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['close', 'add-comment', 'update-comment', 'delete-comments']);

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
  if (!comment.user || !props.currentUser) {
    console.log('Comment user or current user is undefined');
    console.log('Comment:', comment);
    console.log('Current User:', props.currentUser);
    return false;
  }
  // console.log('Comment User ID:', comment.user.id);
  // console.log('Current User ID:', props.currentUser.id);
  return comment.user.id === props.currentUser.id;
};

const editComment = async (comment) => {
  selectedComment.value = comment;
  editCommentContent.value = comment.content || '';
  editCommentPopup.value = true;
};

const cancelEditComment = () => {
  editCommentPopup.value = false;
  selectedComment.value = null;
  editCommentContent.value = '';
};

const saveEditComment = async () => {
  if (!editCommentContent.value) {
    alert('Please enter a comment!');
    return;
  }

  try {
    const response = await axios.put(`http://localhost:8000/api/comments/${selectedComment.value.id}`, {
      content: editCommentContent.value
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    });

    const updatedComment = response.data.comment;
    emit('update-comment', updatedComment);

    alert('Comment updated successfully!');
    editCommentPopup.value = false;
    selectedComment.value = null;
    editCommentContent.value = '';
    window.location.href = '/home';
  } catch (error) {
    console.error(error);
    alert('Failed to update comment.');
  }
};

const deleteComment = async (comment) => {
  isDeleting.value = true;
  try{
    const response = await axios.delete(`http://localhost:8000/api/comments/${comment.id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    if (Array.isArray(props.comments)) {
      const deletedComments = props.comments.filter(c => c.id !== comment.id);
      emit('delete-comments', deletedComments);
    }

    location.reload();
  } catch (error) {
    console.error('Failed to delete comment:', error.responde?.data || error.message);
    alert('Failed to delete comment.' + error.response?.data?.message || error.message);
  } finally {
    isDeleting.value = false;
  }
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