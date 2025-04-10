<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <Navbar />
    <div class="max-w-2xl mx-auto animate-fade-in">
      <!-- Header with subtle bee theme -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold mb-2">
          <span class="animate-letter gradient-text" style="--order: 1">C</span>
          <span class="animate-letter gradient-text" style="--order: 2">o</span>
          <span class="animate-letter gradient-text" style="--order: 3">n</span>
          <span class="animate-letter gradient-text" style="--order: 4">t</span>
          <span class="animate-letter gradient-text" style="--order: 5">a</span>
          <span class="animate-letter gradient-text" style="--order: 6">c</span>
          <span class="animate-letter gradient-text" style="--order: 7">t</span>
          <span class="animate-letter gradient-text" style="--order: 8">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 9">T</span>
          <span class="animate-letter gradient-text" style="--order: 10">h</span>
          <span class="animate-letter gradient-text" style="--order: 11">e</span>
          <span class="animate-letter gradient-text" style="--order: 12">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 13">H</span>
          <span class="animate-letter gradient-text" style="--order: 14">i</span>
          <span class="animate-letter gradient-text" style="--order: 15">v</span>
          <span class="animate-letter gradient-text" style="--order: 16">e</span>
        </h1>
        <p class="text-lg text-amber-800">
          Our worker bees are ready to help. Send us your message and we'll get back to you soon.
        </p>
      </div>

      <!-- Form with bee-themed elements -->
      <div class="bg-white rounded-xl shadow-lg p-8">
        <v-form ref="form" v-model="valid">
          <v-text-field
            v-model="name"
            label="Your Name"
            readonly
            outlined
            class="mb-4"
            hint="This is your registered name"
            persistent-hint
          ></v-text-field>

          <v-text-field
            v-model="email"
            label="Your Email"
            readonly
            outlined
            class="mb-4"
            hint="This is your registered email"
            persistent-hint
          ></v-text-field>

          <v-textarea
            v-model="message"
            label="Your Message"
            maxlength="1000"
            required
            outlined
            class="mb-6"
            hint="What's buzzing on your mind?"
          ></v-textarea>

          <div class="flex items-center">
            <v-btn
              :disabled="!valid || loading"
              @click="submit"
              class="btn-primary"
              large
            >
              <v-progress-circular v-if="loading" indeterminate size="20" class="mr-2" />
              <template v-else>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Send to Hive
              </template>
            </v-btn>

            <p v-if="successMessage" class="ml-6 text-emerald-600 font-medium transition-all duration-1000 ease-in-out"
               :class="successMessage ? 'opacity-100' : 'transition-all duration-500 opacity-0'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              {{ successMessage }}
            </p>

            <p v-if="errorMessage" class="ml-6 text-red-600 font-medium transition-all duration-1000 ease-in-out"
               :class="errorMessage ? 'opacity-100' : 'opacity-0'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ errorMessage }}
            </p>
          </div>
        </v-form>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'
import apiClient from "@/axios.js";

const valid = ref(false)
const name = ref('')
const email = ref('')
const message = ref('')
const userId = ref('')
const successMessage = ref('')
const errorMessage = ref('');
const loading = ref(false);

const fetchUserId = async () => {
  try {
    const response = await apiClient.get('/api/user');
    userId.value = response.data.id;
    name.value = response.data.name;
    email.value = response.data.email;
  } catch (error) {
    errorMessage.value = 'Failed to load your data. Please log in again.';
    console.error('Error getting user ID:', error.response?.data || error.message);
  }
};

// On component mount, fetch user data
onMounted(async () => {
  try {
    await fetchUserId();
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
})

const submit = async () => {
  if (valid.value) {
    loading.value = true;
    try {
      // Call the API to submit the contact form
      await apiClient.post('/api/contact/submit', {
        name: name.value,
        email: email.value,
        message: message.value,
      });
      successMessage.value = 'Message sent to the hive successfully!';
      message.value = '';

      setTimeout(() => {
        successMessage.value = null;
      }, 3000);
    } catch (error) {
      errorMessage.value = error.response?.data?.message || 'Something went wrong. Please try again.';
      setTimeout(() => {
        errorMessage.value = null;
      }, 3000);
    } finally {
      loading.value = false;
    }
  }
};
</script>

<style scoped>
/* Reuse your existing animations */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes letter {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-letter {
  display: inline-block;
  opacity: 0;
  animation: letter 0.5s ease-out forwards;
  animation-delay: calc(var(--order) * 0.1s);
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

/* Button styles matching your theme */
.btn-primary {
  @apply px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center justify-center;
  @apply bg-gradient-to-r from-amber-500 to-amber-400 text-amber-900;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  @apply bg-gradient-to-r from-amber-600 to-amber-500 transform -translate-y-0.5;
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

/* Form styling */
.v-text-field, .v-textarea {
  border-radius: 8px;
}

.v-input__slot {
  background-color: #fff8f0 !important;
  border: 1px solid #f3e8d3 !important;
}
</style>
