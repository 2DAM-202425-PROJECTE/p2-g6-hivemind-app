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
          <input type="hidden" name="_token" :value="csrfToken" />

          <v-text-field
            v-model="name"
            label="Your Name"
            :rules="[rules.required]"
            readonly
            outlined
            class="mb-4"
            hint="This is your registered name"
            persistent-hint
          ></v-text-field>

          <v-text-field
            v-model="email"
            label="Your Email"
            :rules="[rules.required, rules.email]"
            readonly
            outlined
            class="mb-4"
            hint="This is your registered email"
            persistent-hint
          ></v-text-field>

          <v-textarea
            v-model="message"
            label="Your Message"
            :rules="[rules.required]"
            required
            outlined
            class="mb-6"
            hint="What's buzzing on your mind?"
          ></v-textarea>

          <v-btn
            :disabled="!valid"
            @click="submit"
            class="btn-primary"
            large
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            Send to Hive
          </v-btn>
        </v-form>

        <p v-if="successMessage" class="mt-4 text-center text-emerald-600 font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          {{ successMessage }}
        </p>
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
const csrfToken = ref('')
const userId = ref('')
const successMessage = ref('')

const rules = {
  required: value => !!value || 'Required field',
  email: value => /.+@.+\..+/.test(value) || 'Please enter a valid email',
}

const fetchUserId = async () => {
  try {
    const response = await apiClient.get("/api/user");
    userId.value = response.data.id;
    name.value = response.data.name;
    email.value = response.data.email;
  } catch (error) {
    console.error("Error getting user ID:", error.response?.data || error.message);
  }
};

onMounted(async () => {
  try {
    fetchUserId();
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
})

const submit = async () => {
  if (valid.value) {
    try {
      await apiClient.post("/api/contact/submit", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        name: name.value,
        email: email.value,
        message: message.value,
      });
      successMessage.value = 'Message sent to the hive successfully!';
      message.value = '';
    } catch (error) {
      console.error('Error:', error)
    }
  }
}
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
