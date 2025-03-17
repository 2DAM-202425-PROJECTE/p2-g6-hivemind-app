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
      showSuccessSnackbar: false, // Controls success snackbar visibility
      showErrorSnackbar: false,   // Controls error snackbar visibility
    };
  },
  methods: {
    async register() {
      try {
        const response = await apiClient.post('/api/register', {
          name: this.name,
          username: this.username,
          email: this.email,
          password: this.password,
        });

        this.error = null;
        this.showSuccessSnackbar = true; // Show success snackbar

        // Automatically redirect to login after 3 seconds
        setTimeout(() => {
          this.showSuccessSnackbar = false;
          this.$router.push('/login');
        }, 3000);
      } catch (err) {
        this.error = 'Registration failed. Please check your details.';
        this.showErrorSnackbar = true; // Show error snackbar

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
  background-color: #f0f2f5; /* Updated background color */
  border: 5px solid #ccc;
  border-radius: 10px;
  position: relative;
  color: black;
}

.login-box {
  text-align: center;
  background: #ffffff; /* Updated container background color */
  padding: 3rem;
  border-radius: 24px; /* Updated border radius */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 800px; /* Updated max-width */
}

.logo {
  width: 60px; /* Slightly larger logo */
  height: 60px;
  margin-bottom: 1.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

h1 {
  font-size: 2rem; /* Larger font size for the heading */
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
  margin-top: 1.5rem; /* Increased spacing */
  margin-bottom: 0.5rem;
}

input {
  padding: 0.75rem; /* Increased padding for larger inputs */
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1.1rem; /* Slightly larger font size */
  color: black; /* Text color when typing */
}

input::placeholder {
  color: black; /* Placeholder text color */
}

button {
  margin-top: 1.5rem; /* Increased spacing */
  padding: 0.9rem; /* Larger button */
  background-color: #555;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.1rem; /* Slightly larger font size */
}

button:hover {
  background-color: #333;
}

p {
  margin-top: 1.5rem; /* Increased spacing */
  font-size: 1rem; /* Slightly larger font size */
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
  z-index: 10000; /* Ensure it appears above other elements */
  margin-bottom: 16px; /* Offset from the bottom */
  margin-right: 200px; /* Offset from the right edge */
  position: fixed; /* Fixed positioning */
  bottom: 0; /* Stick to the bottom */
  right: 0; /* Stick to the right */
  left: auto; /* Prevent centering */
  transform: none; /* Override any default transform */
  max-width: calc(100% - 32px); /* Ensure it doesn't exceed viewport width */
}

/* Ensure white text for snackbars */
.white--text {
  color: white !important;
}
</style>
