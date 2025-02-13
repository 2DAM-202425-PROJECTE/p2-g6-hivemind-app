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
    </div>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/NavBar.vue'
import Footer from '@/components/AppFooter.vue'

const valid = ref(false)
const name = ref('')
const email = ref('')
const message = ref('')
const csrfToken = ref('')

const rules = {
  required: value => !!value || 'Required.',
  email: value => /.+@.+\..+/.test(value) || 'E-mail must be valid.',
}

onMounted(async () => {
  try {
    // Fetch CSRF token
    await fetch('http://127.0.0.1:8000/sanctum/csrf-cookie', {
      credentials: 'include',
    })
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    console.log('CSRF token fetched:', csrfToken.value)

    // Fetch the logged-in user's details
    const response = await fetch('http://127.0.0.1:8000/api/user', {
      credentials: 'include',
    })
    if (response.ok) {
      const userData = await response.json()
      console.log('User data fetched:', userData)
      email.value = userData.email
    } else {
      console.error('Failed to fetch user data:', response.status, response.statusText)
    }
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
})

const submit = async () => {
  if (valid.value) {
    try {
      const response = await fetch('http://127.0.0.1:8000/contact/submit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken.value,
        },
        body: JSON.stringify({
          name: name.value,
          email: email.value,
          message: message.value,
        }),
        credentials: 'include',
      })
      if (!response.ok) {
        throw new Error('Network response was not ok')
      }
      const data = await response.json()
      console.log('Form submitted:', data)
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
</style>
