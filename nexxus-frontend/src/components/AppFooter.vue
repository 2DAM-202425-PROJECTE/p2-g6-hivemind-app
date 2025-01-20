<template>
  <v-footer height="60" app>
    <div class="footer-content">
      <div class="footer-center">
        <span>Nexxus by Hivemind</span>
        <span>&copy; 2025-{{ new Date().getFullYear() }}
        </span>
        <div class="footer-links">
          <a href="/about">About</a>
          <a href="/contact">Contact</a>
          <a href="/privacy">Privacy Policy</a>
          <a href="/terms">Terms of Service</a>
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
  color: rgba(255, 255, 255, 0.7);
}

.footer-center {
  text-align: center;
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
}

.footer-links a:hover {
  text-decoration: underline;
}
</style>
