<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 p-4">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg overflow-hidden">
      <!-- Logo y título -->
      <div class="p-8 text-center">
        <img class="w-16 h-16 mx-auto mb-4" src="/logo.png" alt="Hivemind Logo" />
        <h1 class="text-2xl font-bold text-amber-900">Join HiveMind Nexxus</h1>
        <p class="text-sm text-amber-600 mt-1">Become part of the colony - Your digital hive awaits</p>
      </div>

      <form @submit.prevent="register" class="px-8 pb-8">
        <!-- Name -->
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-amber-800 mb-1">Full Name</label>
          <input
            id="name"
            type="text"
            v-model="name"
            placeholder="Your full name"
            autocomplete="name"
            required
            class="w-full p-3 bg-gray-100 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
          />
          <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
        </div>

        <!-- Username -->
        <div class="mb-4">
          <label for="username" class="block text-sm font-medium text-amber-800 mb-1">Username</label>
          <input
            id="username"
            type="text"
            v-model="username"
            placeholder="Choose a username"
            autocomplete="username"
            required
            class="w-full p-3 bg-gray-100 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
          />
          <p v-if="errors.username" class="text-xs text-red-500 mt-1">{{ errors.username }}</p>
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-amber-800 mb-1">Email Address</label>
          <input
            id="email"
            type="email"
            v-model="email"
            placeholder="your@example.com"
            autocomplete="email"
            required
            class="w-full p-3 bg-gray-100 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
          />
          <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-amber-800 mb-1">Password</label>
          <div class="relative">
            <input
              id="password"
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              @keydown="checkCapsLock"
              @keyup="checkCapsLock"
              @blur="capsLockOn = false"
              placeholder="Create a strong password"
              autocomplete="new-password"
              required
              minlength="8"
              class="w-full p-3 pr-12 bg-gray-100 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center space-x-2">
              <!-- Caps Lock Icon -->
              <LockClosedIcon
                v-if="capsLockOn"
                class="h-6 w-6 text-amber-600"
                aria-label="Caps Lock is on"
              />
              <!-- Show/Hide Password Icon -->
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="text-gray-600 hover:text-amber-600 focus:outline-none transition-colors duration-200"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
              >
                <EyeIcon
                  v-if="showPassword"
                  class="h-6 w-6"
                />
                <EyeSlashIcon
                  v-else
                  class="h-6 w-6"
                />
              </button>
            </div>
          </div>

          <!-- Password strength indicators -->
          <div class="text-xs text-gray-500 mt-2">
            <div class="grid grid-cols-2 gap-1">
              <div class="flex items-center" :class="{'text-green-500': password.length >= 8}">
                <svg v-if="password.length >= 8" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>8+ characters</span>
              </div>
              <div class="flex items-center" :class="{'text-green-500': /[A-Z]/.test(password)}">
                <svg v-if="/[A-Z]/.test(password)" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>Uppercase</span>
              </div>
              <div class="flex items-center" :class="{'text-green-500': /[a-z]/.test(password)}">
                <svg v-if="/[a-z]/.test(password)" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>Lowercase</span>
              </div>
              <div class="flex items-center" :class="{'text-green-500': /\d/.test(password)}">
                <svg v-if="/\d/.test(password)" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>Number</span>
              </div>
              <div class="flex items-center" :class="{'text-green-500': /[^A-Za-z0-9]/.test(password)}">
                <svg v-if="/[^A-Za-z0-9]/.test(password)" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>Special char</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Muestra mensajes de error específicos -->
        <div v-if="passwordError" class="text-xs text-red-500 mt-1">
          {{ passwordError }}
        </div>

        <!-- Register button -->
        <button
          type="submit"
          :disabled="!isPasswordValid || isLoading"
          class="w-full bg-amber-500 hover:bg-amber-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
          </svg>
          {{ isLoading ? 'Registering...' : 'Create Account' }}
        </button>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full !border"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white !text-gray-400">Or sign up with</span>
          </div>
        </div>

        <!-- Social login buttons -->
        <div class="flex justify-center mb-4">
          <button class="gsi-material-button" disabled>
            <div class="gsi-material-button-state"></div>
            <div class="gsi-material-button-content-wrapper">
              <div class="gsi-material-button-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                  <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                  <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                  <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                  <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                  <path fill="none" d="M0 0h48v48H0z"></path>
                </svg>
              </div>
              <span class="gsi-material-button-contents">Sign in with Google</span>
              <span style="display: none;">Sign in with Google</span>
            </div>
          </button>
        </div>
      </form>

      <!-- Login link -->
      <div class="px-8 py-4 bg-gray-100 text-center">
        <p class="text-sm text-amber-700">
          Already have an account?
          <router-link to="/auth/login" class="font-medium text-amber-600 hover:underline">Log in</router-link>
        </p>
      </div>
    </div>

    <!-- Snackbars -->
    <v-snackbar
      v-model="showSuccessSnackbar"
      :timeout="3000"
      color="black"
      class="text-white fixed bottom-4 right-4 z-50 max-w-xs"
    >
      <v-icon color="green" class="mr-2">mdi-check-circle</v-icon>
      Please check your email to verify your account!
    </v-snackbar>

    <v-snackbar
      v-model="showErrorSnackbar"
      :timeout="3000"
      color="black"
      class="text-white fixed bottom-4 right-4 z-50 max-w-xs"
    >
      <v-icon color="red" class="mr-2">mdi-alert-circle</v-icon>
      {{ error }}
    </v-snackbar>
  </div>
</template>

<script>
import apiClient from '../../axios.js';
import { EyeIcon, EyeSlashIcon, LockClosedIcon } from '@heroicons/vue/24/outline';

export default {
  components: {
    LockClosedIcon,
    EyeIcon,
    EyeSlashIcon
  },
  data() {
    return {
      name: '',
      username: '',
      email: '',
      password: '',
      error: null,
      errors: {},
      showSuccessSnackbar: false,
      showErrorSnackbar: false,
      capsLockOn: false,
      showPassword: false,
      passwordError: null,
      isLoading: false
    };
  },
  computed: {
    isPasswordValid() {
      if (this.password.length < 8) {
        this.passwordError = 'Password must be at least 8 characters long';
        return false;
      }
      if (!/[A-Z]/.test(this.password)) {
        this.passwordError = 'Password must contain at least one uppercase letter';
        return false;
      }
      if (!/[a-z]/.test(this.password)) {
        this.passwordError = 'Password must contain at least one lowercase letter';
        return false;
      }
      if (!/\d/.test(this.password)) {
        this.passwordError = 'Password must contain at least one number';
        return false;
      }
      if (!/[^A-Za-z0-9]/.test(this.password)) {
        this.passwordError = 'Password must contain at least one special character';
        return false;
      }
      this.passwordError = '';
      return true;
    }
  },
  methods: {
    checkCapsLock(event) {
      this.capsLockOn = event.getModifierState && event.getModifierState('CapsLock');
    },
    async register() {
      this.isLoading = true;
      this.error = null;
      this.errors = {};

      try {
        await apiClient.get('/sanctum/csrf-cookie', { withCredentials: true });
        const response = await apiClient.post('/api/register/pending', {
          name: this.name,
          username: this.username,
          email: this.email,
          password: this.password
        }, { withCredentials: true });

        // Guardar el email para la página de espera
        localStorage.setItem('pendingEmail', this.email);

        this.showSuccessSnackbar = true;
        const successMessage = response.data.message.includes('resent')
          ? 'Verification email resent. Please check your inbox!'
          : 'Please verify your email to complete registration!';

        setTimeout(() => {
          this.showSuccessSnackbar = false;
          this.$router.push({ path: '/auth/check-email', query: { email: this.email } });
        }, 3000);
      } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors || {};
          this.error = 'Please check your inputs';
        } else {
          this.error = err.response?.data?.message || 'Registration failed';
        }
        this.showErrorSnackbar = true;
        setTimeout(() => (this.showErrorSnackbar = false), 3000);
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
/* Add smooth transitions for icons */
.relative svg {
  transition: all 0.2s ease-in-out;
}

/* Slight hover effect for icons */
.relative svg:hover {
  transform: scale(1.1);
}

/* Ensure icons are properly centered */
.relative .flex {
  gap: 0.5rem;
}

/* Optional: Add smooth transitions for icons */
.h-6 {
  transition: all 0.2s ease-in-out;
}

/* Slight hover effect for icons */
.h-6:hover {
  transform: scale(1.1);
}

.gsi-material-button {
  -moz-user-select: none;
  -webkit-user-select: none;
  -ms-user-select: none;
  -webkit-appearance: none;
  background-color: #f2f2f2;
  background-image: none;
  border: 1px solid #747775;
  -webkit-border-radius: 4px;
  border-radius: 4px;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  color: #1f1f1f;
  cursor: pointer;
  font-family: 'Roboto', arial, sans-serif;
  font-size: 14px;
  height: 40px;
  letter-spacing: 0.25px;
  outline: none;
  overflow: hidden;
  padding: 0 12px;
  position: relative;
  text-align: center;
  -webkit-transition: background-color .218s, border-color .218s, box-shadow .218s;
  transition: background-color .218s, border-color .218s, box-shadow .218s;
  vertical-align: middle;
  white-space: nowrap;
  width: 100%;
  max-width: 400px;
  min-width: min-content;
}

.gsi-material-button .gsi-material-button-icon {
  height: 20px;
  margin-right: 12px;
  min-width: 20px;
  width: 20px;
}

.gsi-material-button .gsi-material-button-content-wrapper {
  -webkit-align-items: center;
  align-items: center;
  display: flex;
  -webkit-flex-direction: row;
  flex-direction: row;
  -webkit-flex-wrap: nowrap;
  flex-wrap: nowrap;
  height: 100%;
  justify-content: space-between;
  position: relative;
  width: 100%;
}

.gsi-material-button .gsi-material-button-contents {
  -webkit-flex-grow: 1;
  flex-grow: 1;
  font-family: 'Roboto', arial, sans-serif;
  font-weight: 500;
  overflow: hidden;
  text-overflow: ellipsis;
  vertical-align: top;
}

.gsi-material-button .gsi-material-button-state {
  -webkit-transition: opacity .218s;
  transition: opacity .218s;
  bottom: 0;
  left: 0;
  opacity: 0;
  position: absolute;
  right: 0;
  top: 0;
}

.gsi-material-button:disabled {
  cursor: default;
  background-color: #ffffff61;
  border-color: #1f1f1f1f;
}

.gsi-material-button:disabled .gsi-material-button-contents {
  opacity: 38%;
}

.gsi-material-button:disabled .gsi-material-button-icon {
  opacity: 38%;
}

.gsi-material-button:not(:disabled):active .gsi-material-button-state,
.gsi-material-button:not(:disabled):focus .gsi-material-button-state {
  background-color: #303030;
  opacity: 12%;
}

.gsi-material-button:not(:disabled):hover {
  -webkit-box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
  box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
}

.gsi-material-button:not(:disabled):hover .gsi-material-button-state {
  background-color: #303030;
  opacity: 8%;
}
</style>
