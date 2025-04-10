<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-6">
    <div class="text-center animate-fade-in max-w-2xl">
      <!-- Animated mail icon -->
      <div class="animate-bounce-in mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Animated title -->
      <h1 class="text-4xl font-extrabold mb-4">
        <span class="animate-letter gradient-text" style="--order: 1">C</span>
        <span class="animate-letter gradient-text" style="--order: 2">h</span>
        <span class="animate-letter gradient-text" style="--order: 3">e</span>
        <span class="animate-letter gradient-text" style="--order: 4">c</span>
        <span class="animate-letter gradient-text" style="--order: 5">k</span>
        <span class="animate-letter gradient-text" style="--order: 6">&nbsp;</span>
        <span class="animate-letter gradient-text" style="--order: 7">Y</span>
        <span class="animate-letter gradient-text" style="--order: 8">o</span>
        <span class="animate-letter gradient-text" style="--order: 9">u</span>
        <span class="animate-letter gradient-text" style="--order: 10">r</span>
        <span class="animate-letter gradient-text" style="--order: 11">&nbsp;</span>
        <span class="animate-letter gradient-text" style="--order: 12">E</span>
        <span class="animate-letter gradient-text" style="--order: 13">m</span>
        <span class="animate-letter gradient-text" style="--order: 14">a</span>
        <span class="animate-letter gradient-text" style="--order: 15">i</span>
        <span class="animate-letter gradient-text" style="--order: 16">l</span>
      </h1>

      <!-- Animated message -->
      <div class="overflow-hidden">
        <p class="text-lg mb-2 font-medium text-amber-900">
          <span class="animate-text">Our worker bees have delivered a verification link to</span>
        </p>
        <p class="text-xl font-bold text-amber-700 mb-4 animate-fade-in-delay">
          {{ email }}
        </p>
        <p class="text-lg text-amber-800 animate-fade-in-delay">
          Check your inbox (don't forget the spam folder) and follow the instruction to complete your hive verification.
        </p>
      </div>

      <!-- Animated buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 animate-fade-in-delay">
        <button
          @click="checkVerification"
          class="btn-primary"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Continue
        </button>

        <button
          @click="resendEmail"
          class="btn-secondary"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Resend Email
        </button>
      </div>
    </div>

    <!-- Custom notifications -->
    <transition name="slide-fade">
      <div v-if="showSuccessSnackbar" class="fixed bottom-4 right-4 z-50 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center max-w-xs">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Email resent successfully!
      </div>
    </transition>

    <transition name="slide-fade">
      <div v-if="showErrorSnackbar" class="fixed bottom-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center max-w-xs">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        {{ error }}
      </div>
    </transition>
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
          params: { email: this.email }
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
        await apiClient.post('/api/resend-verification', { email: this.email });
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

<style scoped>
/* Animaciones base */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce-in {
  0% { opacity: 0; transform: scale(0.3) translateY(100px); }
  50% { opacity: 1; transform: scale(1.05); }
  70% { transform: scale(0.9); }
  100% { transform: scale(1) translateY(0); }
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

/* Transiciones para notificaciones */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}

/* Clases de animación */
.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-bounce-in {
  animation: bounce-in 1s ease-out;
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

/* Estilos de botones (consistentes con las otras páginas) */
.btn-primary, .btn-secondary {
  @apply px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center justify-center;
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

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
</style>
