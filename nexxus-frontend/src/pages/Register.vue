<template>
  <div class="login-container">
    <div class="login-box">
      <img class="logo" src="/logo.png" alt="Hivemind Logo" />
      <h1>Create your Hivemind Account</h1>
      <form @submit.prevent="register">
        <label for="name">Name:</label>
        <input id="name" type="text" v-model="name" placeholder="Enter your name" required />

        <label for="username">Username:</label>
        <input id="username" type="text" v-model="username" placeholder="Enter your username" required />

        <label for="email">Email:</label>
        <input id="email" type="text" v-model="email" placeholder="Enter your email" required />

        <label for="password">Password:</label>
        <input id="password" type="password" v-model="password" placeholder="Enter your password" required />

        <button type="submit">Register</button>
      </form>
      <p>
        You already have an account?
        <router-link to="/login">Log in here</router-link>
      </p>
    </div>

    <!-- Success Snackbar -->
    <v-snackbar
      v-model="showSuccessSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
      Registration successful!
    </v-snackbar>

    <!-- Error Snackbar -->
    <v-snackbar
      v-model="showErrorSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
      {{ error }}
    </v-snackbar>

    <!-- Password Length Snackbar -->
    <v-snackbar
      v-model="showPasswordErrorSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
      Password must be at least 8 characters long
    </v-snackbar>

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
      name: '',
      username: '',
      email: '',
      password: '',
      error: null,
      showSuccessSnackbar: false,
      showErrorSnackbar: false,
      showPasswordErrorSnackbar: false,
    };
  },
  methods: {
    async register() {
      // Check password length first
      if (this.password.length < 8) {
        this.showPasswordErrorSnackbar = true;
        // Hide the snackbar after 3 seconds
        setTimeout(() => {
          this.showPasswordErrorSnackbar = false;
        }, 3000);
        return; // Exit the function if password is too short
      }

      try {
        // Register the user
        const response = await apiClient.post('/api/register', {
          name: this.name,
          username: this.username,
          email: this.email,
          password: this.password,
        });

        // Automatically log in the user
        const loginResponse = await apiClient.post('/api/login', {
          email: this.email,
          password: this.password,
          device_name: 'web',
        });

        // Store the token
        localStorage.setItem('token', loginResponse.data.token);

        this.error = null;
        this.showSuccessSnackbar = true;

        // Automatically redirect to profile completion page after 3 seconds
        setTimeout(() => {
          this.showSuccessSnackbar = false;
          this.$router.push('/complete-profile');
        }, 3000);
      } catch (err) {
        this.error = 'Registration failed. Please check your details.';
        this.showErrorSnackbar = true;

        // Automatically hide error snackbar after 3 seconds
        setTimeout(() => {
          this.showErrorSnackbar = false;
        }, 3000);
      }
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
  background-color: #f0f2f5;
  border: 5px solid #ccc;
  border-radius: 10px;
  position: relative;
  color: black;
}

.login-box {
  text-align: center;
  background: #ffffff;
  padding: 3rem;
  border-radius: 24px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 800px;
}

.logo {
  width: 60px;
  height: 60px;
  margin-bottom: 1.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

h1 {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: black;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
}

input {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1.1rem;
  color: black;
}

input::placeholder {
  color: black;
}

button {
  margin-top: 1.5rem;
  padding: 0.9rem;
  background-color: #555;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.1rem;
}

button:hover {
  background-color: #333;
}

p {
  margin-top: 1.5rem;
  font-size: 1rem;
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

.custom-snackbar {
  z-index: 10000;
  margin-bottom: 16px;
  margin-right: 200px;
  position: fixed;
  bottom: 0;
  right: 0;
  left: auto;
  transform: none;
  max-width: calc(100% - 32px);
}

.white--text {
  color: white !important;
}
</style>
