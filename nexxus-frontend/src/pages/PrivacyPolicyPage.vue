<template>
  <div class="terms-container">
    <Navbar />
    <div class="content">
      <h1>Terms of Service</h1>
      <div class="terms-content">
        <h2>Introduction</h2>
        <p>
          Welcome to Nexxus by Hivemind. These terms of service outline the rules and regulations for the use of our website and services.
        </p>
        <h2>Acceptance of Terms</h2>
        <p>
          By accessing and using our services, you accept and agree to be bound by these terms. If you do not agree to these terms, please do not use our services.
        </p>
        <h2>Use of Services</h2>
        <p>
          You agree to use our services only for lawful purposes and in a way that does not infringe the rights of, restrict, or inhibit anyone else's use and enjoyment of the services.
        </p>
        <h2>Account Registration</h2>
        <p>
          To access certain features of our services, you may be required to register an account. You agree to provide accurate and complete information during the registration process and to keep your account information up to date.
        </p>
        <h2>Termination</h2>
        <p>
          We may terminate or suspend your access to our services at any time, without prior notice or liability, for any reason, including if you breach these terms.
        </p>
        <h2>Changes to Terms</h2>
        <p>
          We reserve the right to modify these terms at any time. We will notify you of any changes by posting the new terms on this page. Your continued use of the services after any such changes constitutes your acceptance of the new terms.
        </p>
        <h2>Contact Us</h2>
        <p>
          If you have any questions about these terms, please contact us at support@nexxus.com.
        </p>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
</script>

<style scoped>
.terms-container {
  font-family: Arial, sans-serif;
  padding: 20px;
  padding-top: 90px;
  padding-bottom: 90px;
  background-color: white;
  min-height: 100vh;
  color: black;
}

h1 {
  font-size: 24px;
  padding-bottom: 20px;
}

.terms-content {
  max-width: 800px;
  margin: 0 auto;
  background: #f0f0f0;
  border: 1px solid #d3d3d3;
  border-radius: 10px;
  padding: 20px;
}

h2 {
  font-size: 20px;
  padding-top: 30px;
}

ul {
  list-style-type: disc;
  padding-left: 20px;
}
</style>
