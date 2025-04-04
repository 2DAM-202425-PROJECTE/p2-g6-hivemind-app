<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 p-4">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="p-8 text-center">
        <img class="w-16 h-16 mx-auto mb-4" src="/logo.png" alt="Hivemind Logo" />
        <h1 class="text-2xl font-bold text-amber-900">Revisa tu Correo</h1>
        <p class="text-sm text-amber-700 mt-2">
          Hemos enviado un enlace de verificación a <span class="font-medium">{{ email }}</span>. Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace para verificar tu cuenta.
        </p>
      </div>
      <div class="px-8 pb-8 text-center">
        <button
          @click="checkVerification"
          class="mt-4 bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300"
        >
          Continuar
        </button>
        <button
          @click="resendEmail"
          class="mt-4 ml-4 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300"
        >
          Reenviar Correo
        </button>
      </div>
    </div>
    <v-snackbar v-model="showSuccessSnackbar" :timeout="3000" color="black" class="text-white fixed bottom-4 right-4 z-50 max-w-xs">
      <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
      Correo reenviado con éxito!
    </v-snackbar>
    <v-snackbar v-model="showErrorSnackbar" :timeout="3000" color="black" class="text-white fixed bottom-4 right-4 z-50 max-w-xs">
      <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
      {{ error }}
    </v-snackbar>
  </div>
</template>

<script>
import apiClient from '../../axios.js';

export default {
  data() {
    return {
      email: '',
      error: null,
      showSuccessSnackbar: false,
      showErrorSnackbar: false,
    };
  },
  created() {
    this.email = this.$route.query.email || 'your email';
  },
  methods: {
    async checkVerification() {
      try {
        const response = await apiClient.get('/api/check-verification', {
          params: { email: this.email },
          withCredentials: true,
        });
        if (response.data.verified) {
          this.$router.push('/profile');
        } else {
          this.error = 'Por favor, verifica tu correo antes de continuar.';
          this.showErrorSnackbar = true;
          setTimeout(() => (this.showErrorSnackbar = false), 3000);
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Error al verificar el estado';
        this.showErrorSnackbar = true;
        setTimeout(() => (this.showErrorSnackbar = false), 3000);
      }
    },
    async resendEmail() {
      try {
        await apiClient.post('/api/resend-verification', { email: this.email }, { withCredentials: true });
        this.showSuccessSnackbar = true;
        setTimeout(() => (this.showSuccessSnackbar = false), 3000);
      } catch (err) {
        this.error = err.response?.data?.message || 'Error al reenviar el correo';
        this.showErrorSnackbar = true;
        setTimeout(() => (this.showErrorSnackbar = false), 3000);
      }
    },
  },
};
</script>
