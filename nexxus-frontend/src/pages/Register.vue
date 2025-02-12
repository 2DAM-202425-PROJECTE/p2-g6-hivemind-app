<template>
  <div class="login-container">
    <div class="login-box">
      <img class="logo" src="../assets/nexxus.jpeg" alt="Hivemind Logo" />
      <h1>Create your Hivemind Account</h1>
      <form @submit.prevent="register">
        <label for="name">Name:</label>
        <input id="name" type="text" v-model="name" placeholder="Enter your name" required />

        <label for="email">Email:</label>
        <input id="email" type="text" v-model="email" placeholder="Enter your email" required />

        <label for="password">Password:</label>
        <input id="password" type="password" v-model="password" placeholder="Enter your password" required />

        <button type="submit">Register</button>
      </form>
      <p v-if="error" style="color: red">{{ error }}</p>
      <p>
        You already have an account?
        <router-link to="/login">Log in here</router-link>
      </p>
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
      name: '',
      email: '',
      password: '',
      error: null,
    };
  },
  methods: {
    async register() {
      try {
        const response = await apiClient.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
        });
        alert('Registration successful!');
        this.$router.push('/login');
      } catch (err) {
        this.error = 'Registration failed. Please check your details.';
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
  color: white; /* Set placeholder text color to white */
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
</style>
