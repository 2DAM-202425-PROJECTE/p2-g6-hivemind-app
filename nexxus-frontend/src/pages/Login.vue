<template>
  <div class="login-container">
    <div class="login-box">
      <img class="logo" src="/logo.png" alt="Hivemind Logo" />
      <h1>Log in to Hivemind Account</h1>
      <form @submit.prevent="login">
        <label for="email">Email:</label>
        <input id="email" type="text" v-model="email" placeholder="Enter your email" required />

        <label for="password">Password:</label>
        <input id="password" type="password" v-model="password" placeholder="Enter your password" required />

        <button type="submit">Login</button>
      </form>
      <p v-if="error" style="color: red">{{ error }}</p>
      <p>
        You don't have an account?
        <router-link to="/register">Register here</router-link>
      </p>
    </div>

    <!-- Success Popup -->
    <div v-if="showPopup" class="popup">
      <div class="popup-content">
        <p>✅ Signed in successfully!</p>
        <button @click="closePopup">OK</button>
      </div>
    </div>

    <div class="icons">
      <i class="icon network-icon"></i>
      <i class="icon profile-icon"></i>
    </div>
  </div>
</template>

<script>
import apiClient from '../axios.js';

export default {
  data() {
    return {
      email: '',
      password: '',
      deviceName: 'web',
      error: null,
      showPopup: false, // Controls popup visibility
    };
  },
  methods: {
    async login() {
      try {
        const response = await apiClient.post('/api/login', {
          email: this.email,
          password: this.password,
          device_name: this.deviceName,
        });

        localStorage.setItem('token', response.data.token);
        this.error = null;
        this.showPopup = true; // Show the popup

        // Automatically redirect after 1.5 seconds
        setTimeout(() => {
          this.$router.push('/home');
        }, 1500);
      } catch (err) {
        this.error = 'Login failed. Please check your credentials.';
      }
    },
    closePopup() {
      this.showPopup = false;
      this.$router.push('/home'); // Ensure redirection on manual close
    },
  },
};
</script>

<style scoped>
.login-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background-color: #f9f9f9;
  border: 5px solid #ccc;
  border-radius: 10px;
  position: relative;
  color: black;
}

.login-box {
  text-align: center;
  background: #7f7f7f;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 90%;
  max-width: 400px;
}

.logo {
  width: 50px;
  height: 50px;
  margin-bottom: 1rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

h1 {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  color: black;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

input {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
}
input::placeholder {
  color: white;
}

button {
  margin-top: 1rem;
  padding: 0.7rem;
  background-color: #555;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
}

button:hover {
  background-color: #333;
}

p {
  margin-top: 1rem;
  font-size: 0.9rem;
}

a {
  color: #007bff;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

.icons {
  position: absolute;
  bottom: 1rem;
  display: flex;
  gap: 1rem;
}

.icon {
  font-size: 1.5rem;
}

.network-icon {
  background: url("/network-icon.png") no-repeat center;
  width: 24px;
  height: 24px;
}

.profile-icon {
  background: url("/profile-icon.png") no-repeat center;
  width: 24px;
  height: 24px;
}

/* Popup styles */
.popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #7f7f7f;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  text-align: center;
  z-index: 1000;
}

.popup-content {
  font-size: 1.2rem;
}

.popup button {
  margin-top: 10px;
  padding: 5px 15px;
  border: none;
  background: #28a745;
  color: white;
  border-radius: 5px;
  cursor: pointer;
}

.popup button:hover {
  background: #218838;
}
</style>
