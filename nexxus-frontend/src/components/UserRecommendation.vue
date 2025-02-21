<template>
  <div class="recommendation-box">
    <h2>Recommended Users</h2>
    <div class="user-list">
      <div v-for="user in users.slice(0, 4)" :key="user.id" class="user-item">
        <img :src="user.profile_photo_url" alt="Profile Picture" class="profile-pic" />
        <span>{{ user.name }}</span>
        <button @click="addFriend(user.name)" class="add-button">+</button>
      </div>
    </div>
    <div v-if="showPopup" class="popup">
      Added {{ addedUser }} as a friend
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserRecommendation',
  data() {
    return {
      users: [],
      showPopup: false,
      addedUser: '',
    };
  },
  methods: {
    addFriend(userName) {
      this.addedUser = userName;
      this.showPopup = true;
      setTimeout(() => {
        this.showPopup = false;
      }, 2000);
    },
    fetchRandomUsers() {
      axios.get('/api/random-users')
        .then(response => {
          this.users = response.data;
        })
        .catch(error => {
          console.error('Error fetching random users:', error);
        });
    },
  },
  mounted() {
    this.fetchRandomUsers();
  },
};
</script>

<style scoped>
.recommendation-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 10px;
  border-radius: 5px;
  max-width: 100%;
  overflow-x: auto;
  margin-top: 20px;
}

h2 {
  margin-bottom: 10px;
}

.user-list {
  display: flex;
  justify-content: center;
  gap: 10px;
  flex-wrap: nowrap;
  padding-bottom: 90px;
}

.user-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
  width: 150px;
  text-align: center;
}

.profile-pic {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  margin-bottom: 5px;
}

.add-button {
  background: none;
  border: none;
  color: blue;
  font-size: 1.5rem;
  cursor: pointer;
}

.popup {
  padding: 10px;
  background-color: #7f7f7f;
  color: white;
  border-radius: 5px;
  text-align: center;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1000;
}
</style>
