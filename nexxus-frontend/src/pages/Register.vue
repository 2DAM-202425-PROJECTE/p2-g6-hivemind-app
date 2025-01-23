<template>
  <div class="register-container">
    <div class="register-box">
      <img class="logo" src="./../../public/atencionalclientequeescomomejorar.webp" alt="Hivemind Logo" />
      <h1>Create a Hivemind Account</h1>
      <form @submit.prevent="register">
        <label for="username">Username:</label>
        <input
          id="username"
          type="text"
          v-model="username"
          placeholder="Enter your username"
          required
        />
        <label for="email">Email:</label>
        <input
          id="email"
          type="email"
          v-model="email"
          placeholder="Enter your email"
          required
        />
        <label for="password">Password:</label>
        <input
          id="password"
          type="password"
          v-model="password"
          placeholder="Enter your password"
          required
        />
        <label for="confirm-password">Confirm Password:</label>
        <input
          id="confirm-password"
          type="password"
          v-model="confirmPassword"
          placeholder="Confirm your password"
          required
        />
        <button type="submit">Register</button>
      </form>
      <p>
        Already have an account?
        <a href="#" @click.prevent="navigateToLogin">Login</a>
      </p>
    </div>
    <div class="icons">
      <i class="icon network-icon"></i>
      <i class="icon profile-icon"></i>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Register',
  data() {
    return {
      username: '',
      email: '',
      password: '',
      confirmPassword: '',
    };
  },
  methods: {
    register() {
      // Tu lógica de registro aquí
    },
    navigateToLogin() {
      this.$router.push('/login');
    },
  },
};
</script>
<style scoped>
.register-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: #f5f5f5; /* Fondo claro para mejor contraste */
  color: black; /* Color del texto negro */
}
.register-box {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.logo {
  display: block;
  margin: 0 auto 20px;
  max-width: 100%;
  height: auto;
}
h1 {
  font-size: 24px;
  color: black; /* Color del texto negro */
  text-align: center;
}
form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
label {
  color: black; /* Color del texto negro */
}
input {
  padding: 0.5rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
button {
  padding: 0.5rem;
  font-size: 1rem;
  background-color: #42b883;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}
button:hover {
  background-color: #369f6b;
}
.icons {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
.icon {
  font-size: 1.5rem;
  margin: 0 10px;
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
