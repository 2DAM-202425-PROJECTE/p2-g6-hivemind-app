<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-amber-50 to-amber-100">
    <div class="text-center bg-white p-12 rounded-xl shadow-lg w-full max-w-3xl">
      <!-- Logo -->
      <img class="w-16 h-16 mb-6 mx-auto" src="/logo.png" alt="Hivemind Logo" />

      <!-- Título con toque temático -->
      <h1 class="text-amber-900 text-3xl font-bold mb-8">Connect to HiveMind Nexxus</h1>

      <form @submit.prevent="login">
        <label for="email" class="text-amber-800">Email:</label>
        <input
          id="email"
          type="text"
          v-model="email"
          placeholder="Enter your email"
          required
          class="w-full p-3 border border-amber-300 rounded-md text-lg mt-2 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
        />

        <label for="password" class="text-amber-800 mt-4">Password:</label>
        <input
          id="password"
          type="password"
          v-model="password"
          placeholder="Enter your password"
          required
          class="w-full p-3 border border-amber-300 rounded-md text-lg mt-2 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
        />

        <!-- Botón con estilo mejorado -->
        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 px-4 mt-6 rounded-md flex items-center justify-center transition-all duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
          Join the Hive
        </button>
      </form>

      <p class="text-amber-700 mt-6">
        Don't have an account?
        <router-link to="/register" class="text-amber-600 hover:underline">Register here</router-link>
      </p>
    </div>

    <!-- Snackbars (manteniendo tu estructura Vuetify) -->
    <v-snackbar
      v-model="showSuccessSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
      Connected successfully!
    </v-snackbar>

    <v-snackbar
      v-model="showErrorSnackbar"
      :timeout="3000"
      color="black"
      class="white--text custom-snackbar"
    >
      <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
      {{ error }}
    </v-snackbar>

    <!-- Iconos (opcional, puedes eliminarlos si prefieres) -->
    <div class="absolute bottom-4 flex gap-4">
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
      showSuccessSnackbar: false,
      showErrorSnackbar: false,
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
        this.showSuccessSnackbar = true;

        setTimeout(() => {
          this.showSuccessSnackbar = false;
          this.$router.push('/home');
        }, 3000);
      } catch (err) {
        this.error = 'Login failed. Please check your credentials.';
        this.showErrorSnackbar = true;

        setTimeout(() => {
          this.showErrorSnackbar = false;
        }, 3000);
      }
    },
  },
};
</script>


<style scoped>
/* Snackbar (igual a tu versión) */
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

