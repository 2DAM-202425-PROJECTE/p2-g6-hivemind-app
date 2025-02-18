<template>
  <div class="contact-container">
    <Navbar />
    <h1>Contact Us</h1>
    <p class="contact-description">We would love to hear from you! Please fill out the form below to get in touch with us.</p>
    <div class="contact-card">
      <v-form ref="form" v-model="valid">
        <input type="hidden" name="_token" :value="csrfToken" />
        <v-text-field
          v-model="name"
          label="Name"
          :rules="[rules.required]"
          required
        ></v-text-field>
        <v-text-field
          v-model="email"
          label="Email"
          :rules="[rules.required, rules.email]"
          required
        ></v-text-field>
        <v-textarea
          v-model="message"
          label="Message"
          :rules="[rules.required]"
          required
        ></v-textarea>
        <v-btn :disabled="!valid" @click="submit">Submit</v-btn>
      </v-form>
      <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
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
  required: value => !!value || 'Required.',
  email: value => /.+@.+\..+/.test(value) || 'E-mail must be valid.',
}

const fetchUserId = async () => {
  try {
    const response = await apiClient.get("/api/user", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    userId.value = response.data.id;
    name.value = response.data.name;
    email.value = response.data.email;
  } catch (error) {
    console.error("Error al obtenir l'ID de l'usuari:", error.response?.data || error.message);
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
      successMessage.value = 'Form sent successfully!';
      message.value = '';
    } catch (error) {
      console.error('Error:', error)
    }
  }
}
</script>

<style scoped>
.contact-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 80px; /* Add padding to the top */
  background-color: #f0f2f5;
  min-height: 100vh;
  color: #1c1e21;
  padding-bottom: 90px;
}

h1 {
  font-size: 24px;
  margin-bottom: 10px;
  text-align: center;
}

.contact-description {
  font-size: 16px;
  margin-bottom: 20px;
  text-align: center;
  color: #555;
}

.contact-card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  margin: 0 auto;
  width: 100%;
}

.v-btn {
  display: block;
  margin: 20px auto;
  background-color: #7f7f7f;
  color: white;
}

.v-btn:hover {
  background-color: #5f5f5f;
}

.success-message {
  color: #000000;
  text-align: center;
  margin-top: 20px;
}
</style>
