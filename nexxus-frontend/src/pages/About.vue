<template>
  <div class="about-container">
    <Navbar />
    <h1>About Nexxus by Hivemind</h1>
    <div class="about-content">
      <h2>Project Overview</h2>
      <p>
        Nexxus by Hivemind is a SaaS application focused on creating a social network. Our goal is to provide a platform where users can share their stories and experiences with a like-minded community.
      </p>
      <h2>Objectives</h2>
      <p>
        Our objective is to create a social network with the following basic functionalities:
      </p>
      <ul>
        <li>Messaging, calls/video calls, live streaming, posts, and stories.</li>
        <li>A credit system for users to earn rewards by playing and chatting, which can be spent on profile customizations.</li>
        <li>Account levels with an experience system, achievements, and badges.</li>
        <li>Reward store: Exclusive content like emojis and accessories.</li>
        <li>Collaborations and events: Credits for joint activities.</li>
        <li>Positive feedback: Credits for likes and positive comments.</li>
      </ul>
      <h2>Target Audience</h2>
      <p>
        Our target audience consists of teenagers and young adults passionate about video games who want to share their content and experiences with a like-minded community.
      </p>
      <h2>Functional and Non-Functional Requirements</h2>
      <h3>Functional</h3>
      <ul>
        <li>User authentication / User profile</li>
        <li>Messaging functionalities</li>
        <li>Posts and stories</li>
        <li>Calls, video calls, and live streaming</li>
        <li>Credit system and a store</li>
        <li>Account levels and achievements</li>
        <li>Collaborations and events</li>
      </ul>
      <h3>Non-Functional</h3>
      <ul>
        <li>Performance</li>
        <li>Scalability</li>
        <li>Security</li>
        <li>Usability</li>
        <li>Compatibility</li>
        <li>Availability</li>
        <li>Maintenance and accessibility</li>
      </ul>
      <h2>Technical Requirements</h2>
      <p>
        The project uses the following technologies:
      </p>
      <ul>
        <li>Backend: MySQL, PHP</li>
        <li>Frontend: HTML, CSS, JS, React Native</li>
        <li>Frameworks: Vue.js, TailwindCSS, Laravel</li>
        <li>Development Tools: GitHub, PHPStorm, Visual Studio Code</li>
      </ul>
      <h2>Development Team</h2>
      <p>
        Our development team consists of passionate and skilled individuals:
      </p>
      <ul>
        <li>Manel: Front-end Developer</li>
        <li>Harry: Front-end Developer</li>
        <li>Gerard: Back-end Developer</li>
        <li>Radostin: Back-end Developer</li>
      </ul>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
</script>

<style scoped>
.about-container {
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

.about-content {
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

h3 {
  font-size: 18px;
  padding-top: 20px;
}

ul {
  list-style-type: disc;
  padding-left: 20px;
}
</style>
