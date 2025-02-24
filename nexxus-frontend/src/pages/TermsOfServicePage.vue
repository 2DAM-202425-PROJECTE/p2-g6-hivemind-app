<template>
  <div class="privacy-container">
    <Navbar />
    <div class="content">
      <h1>Privacy Policy</h1>
      <div class="privacy-content">
        <h2>Introduction</h2>
        <p>
          At Nexxus by Hivemind, we are committed to protecting your privacy. This Privacy Policy outlines how we collect, use, and safeguard your personal information.
        </p>
        <h2>Information We Collect</h2>
        <p>
          We collect various types of information in connection with the services we provide, including:
        </p>
        <ul>
          <li>Personal identification information (Name, email address, phone number, etc.)</li>
          <li>Usage data (Pages visited, time spent on the site, etc.)</li>
          <li>Cookies and tracking technologies</li>
        </ul>
        <h2>How We Use Your Information</h2>
        <p>
          We use the information we collect for various purposes, including:
        </p>
        <ul>
          <li>To provide and maintain our service</li>
          <li>To notify you about changes to our service</li>
          <li>To allow you to participate in interactive features of our service</li>
          <li>To provide customer support</li>
          <li>To gather analysis or valuable information so that we can improve our service</li>
          <li>To monitor the usage of our service</li>
          <li>To detect, prevent, and address technical issues</li>
        </ul>
        <h2>Security of Your Information</h2>
        <p>
          We take the security of your personal information seriously and use various measures to protect it. However, no method of transmission over the Internet or method of electronic storage is 100% secure.
        </p>
        <h2>Changes to This Privacy Policy</h2>
        <p>
          We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.
        </p>
        <h2>Contact Us</h2>
        <p>
          If you have any questions about this Privacy Policy, please contact us at hivemindnexxuscontact@gmail.com
          .
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
.privacy-container {
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

.privacy-content {
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
