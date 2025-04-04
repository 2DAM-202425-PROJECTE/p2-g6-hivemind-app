<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-6">
    <div class="text-center animate-fade-in max-w-2xl">
      <!-- Animación de verificación exitosa/error -->
      <div class="mb-8 animate-bounce-in" v-if="!error">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="mb-8 animate-shake" v-else>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>

      <!-- Título con animación -->
      <h1 class="text-4xl font-extrabold mb-4 gradient-text" :class="{'!text-emerald-600': !error, '!text-red-600': error}">
        <template v-if="!error">
          <span class="animate-letter" style="--order: 1">V</span>
          <span class="animate-letter" style="--order: 2">e</span>
          <span class="animate-letter" style="--order: 3">r</span>
          <span class="animate-letter" style="--order: 4">i</span>
          <span class="animate-letter" style="--order: 5">f</span>
          <span class="animate-letter" style="--order: 6">i</span>
          <span class="animate-letter" style="--order: 7">e</span>
          <span class="animate-letter" style="--order: 8">d</span>
        </template>
        <template v-else>
          <span class="animate-letter" style="--order: 1">E</span>
          <span class="animate-letter" style="--order: 2">r</span>
          <span class="animate-letter" style="--order: 3">r</span>
          <span class="animate-letter" style="--order: 4">o</span>
          <span class="animate-letter" style="--order: 5">r</span>
        </template>
      </h1>

      <!-- Mensaje con animación -->
      <div class="overflow-hidden">
        <p class="text-xl mb-2 font-medium" :class="{'text-emerald-700': !error, 'text-red-700': error}" v-if="!error">
          <span class="animate-text">Your account has been successfully verified!</span>
        </p>
        <p class="text-xl mb-2 font-medium text-red-700" v-else>
          <span class="animate-text">{{ error }}</span>
        </p>
      </div>

      <p class="text-lg mb-8 text-amber-800 animate-fade-in-delay" v-if="!error">
        Welcome to the HiveMind! You're now ready to explore all the features.
      </p>

      <!-- Botones con animación -->
      <div class="flex gap-5 justify-center animate-fade-in-delay">
        <button
          @click="goLogin"
          class="btn-primary"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
          </svg>
          Log In
        </button>

        <router-link
          to="/"
          class="btn-secondary"
          v-if="!error"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
          </svg>
          Return to Hive
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import apiClient from '../../axios.js';

export default {
  props: ['token'],
  data() {
    return {
      error: null,
    };
  },
  created() {
    if (this.token) {
      this.verifyEmailWithToken();
    } else {
      this.error = 'Enlace inválido. Falta el token de verificación.';
    }
  },
  methods: {
    async verifyEmailWithToken() {
      try {
        await apiClient.get(`/api/verify-email/${this.token}`, { withCredentials: true });
        // No redirect here, let user go back manually
      } catch (err) {
        this.error = err.response?.data?.message || 'Error al verificar el correo';
      }
    },
    goLogin() {
      this.$router.push({ path: '/auth/login', query: { email: this.$route.query.email } });
    },
  },
};
</script>

<style scoped>
/* Animaciones base de la página 404 */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes jump {
  0% { transform: translateY(0) scale(1); }
  30% { transform: translateY(-40px) scale(1.1); }
  60% { transform: translateY(0) scale(1); }
  80% { transform: translateY(-10px) scale(1.02); }
  100% { transform: translateY(0) scale(1); }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.btn-primary, .btn-secondary {
  @apply px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary {
  @apply bg-gradient-to-r from-amber-500 to-amber-400 text-amber-900;
}

.btn-primary:hover {
  @apply bg-gradient-to-r from-amber-600 to-amber-500 transform -translate-y-0.5;
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.btn-secondary {
  @apply bg-amber-50 text-amber-800 border border-amber-300;
}

.btn-secondary:hover {
  @apply bg-amber-100 transform -translate-y-0.5 border-amber-400;
}

/* Nuevas animaciones específicas para esta página */
@keyframes bounce-in {
  0% { opacity: 0; transform: scale(0.3) translateY(100px); }
  50% { opacity: 1; transform: scale(1.05); }
  70% { transform: scale(0.9); }
  100% { transform: scale(1) translateY(0); }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
  20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes letter {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes text {
  0% { opacity: 0; transform: translateX(-20px); }
  100% { opacity: 1; transform: translateX(0); }
}

@keyframes bee {
  0% { opacity: 0; transform: translateX(var(--start)) translateY(0); }
  10% { opacity: 1; }
  90% { opacity: 1; }
  100% { transform: translateX(110%) translateY(-20px); opacity: 0; }
}

.animate-bounce-in {
  animation: bounce-in 1s ease-out;
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}

.animate-letter {
  display: inline-block;
  opacity: 0;
  animation: letter 0.5s ease-out forwards;
  animation-delay: calc(var(--order) * 0.1s);
}

.animate-text {
  display: inline-block;
  opacity: 0;
  animation: text 0.8s ease-out forwards;
  animation-delay: 0.3s;
}

.animate-fade-in-delay {
  opacity: 0;
  animation: fade-in 1s ease-out forwards;
  animation-delay: 0.8s;
}

.bee {
  position: absolute;
  bottom: 0;
  left: 0;
  opacity: 0;
}

.animate-bee {
  animation: bee linear forwards;
  animation-duration: var(--duration);
  animation-delay: var(--delay);
}
</style>
