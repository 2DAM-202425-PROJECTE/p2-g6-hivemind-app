<template>
  <v-footer height="60" app>
    <div class="footer-content">
      <div class="footer-center">
        <span>Nexxus by Hivemind</span>
        <span>&copy; 2025-{{ new Date().getFullYear() }}</span>
        <div class="footer-links">
          <router-link to="/about">About</router-link>
          <router-link to="/contact">Contact</router-link>
          <router-link to="/privacy-policy">Privacy Policy</router-link>
          <router-link to="/terms-of-service">Terms of Service</router-link>
        </div>
      </div>
    </div>
  </v-footer>
</template>

<script setup>
</script>

<style scoped>
.footer-content {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 0 16px;
  background-color: #000; /* Black background */
  color: rgba(255, 255, 255, 0.7); /* Light text color */
}

.footer-center {
  text-align: center;
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 10px; /* Add some space between the text and links */
}

.footer-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
}

.footer-links a:hover {
  text-decoration: underline;
}
</style>
