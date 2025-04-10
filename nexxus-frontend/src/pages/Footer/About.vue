<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <Navbar />

    <div class="max-w-4xl mx-auto px-6 py-12 animate-fade-in">
      <!-- Encabezado animado -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold mb-4">
          <span class="animate-letter gradient-text" style="--order: 1">A</span>
          <span class="animate-letter gradient-text" style="--order: 2">b</span>
          <span class="animate-letter gradient-text" style="--order: 3">o</span>
          <span class="animate-letter gradient-text" style="--order: 4">u</span>
          <span class="animate-letter gradient-text" style="--order: 5">t</span>
          <span class="animate-letter gradient-text" style="--order: 6">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 7">T</span>
          <span class="animate-letter gradient-text" style="--order: 8">h</span>
          <span class="animate-letter gradient-text" style="--order: 9">e</span>
          <span class="animate-letter gradient-text" style="--order: 10">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 11">H</span>
          <span class="animate-letter gradient-text" style="--order: 12">i</span>
          <span class="animate-letter gradient-text" style="--order: 13">v</span>
          <span class="animate-letter gradient-text" style="--order: 14">e</span>
        </h1>
        <p class="text-xl text-amber-800 animate-fade-in-delay">
          Building connections, one buzz at a time
        </p>
      </div>

      <!-- Tarjetas de contenido -->
      <div class="space-y-8">
        <!-- Project Overview -->
        <div class="bg-white rounded-xl shadow-lg p-8 transform transition-all hover:scale-[1.01] hover:shadow-xl">
          <h2 class="text-2xl font-bold text-amber-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Project Overview
          </h2>
          <p class="text-lg text-gray-700">
            HiveMind by Nexxus is a next-gen social platform where gamers connect, share, and grow together.
            We're creating a vibrant ecosystem where every interaction matters and every member contributes to the hive.
          </p>
        </div>

        <!-- Objectives -->
        <div class="bg-white rounded-xl shadow-lg p-8 transform transition-all hover:scale-[1.01] hover:shadow-xl">
          <h2 class="text-2xl font-bold text-amber-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            Our Hive Objectives
          </h2>
          <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <li v-for="(item, index) in objectives" :key="index" class="flex items-start">
              <span class="text-amber-500 mr-2">•</span>
              <span class="text-gray-700">{{ item }}</span>
            </li>
          </ul>
        </div>

        <!-- Target Audience -->
        <div class="bg-white rounded-xl shadow-lg p-8 transform transition-all hover:scale-[1.01] hover:shadow-xl">
          <h2 class="text-2xl font-bold text-amber-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Our Community
          </h2>
          <p class="text-lg text-gray-700">
            Designed for gamers and content creators who want to share their passion,
            connect with like-minded individuals, and be rewarded for their engagement in the community.
          </p>
        </div>

        <!-- Team -->
        <div class="bg-white rounded-xl shadow-lg p-8 transform transition-all hover:scale-[1.01] hover:shadow-xl">
          <h2 class="text-2xl font-bold text-amber-700 mb-6 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            The Hive Builders
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="(member, index) in team" :key="'member'+index" class="text-center">
              <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-amber-200 flex items-center justify-center text-amber-700 text-2xl font-bold">
                {{ member.initials }}
              </div>
              <div class="font-bold text-amber-700">{{ member.name }}</div>
              <div class="text-sm text-gray-600">{{ member.role }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';

const objectives = [
  "Create an engaging social platform for gamers",
  "Implement messaging, calls, live streaming, and content sharing",
  "Develop a credit reward system for user engagement",
  "Design account levels with XP, achievements, and badges",
  "Build a reward store with exclusive digital content",
  "Facilitate collaborations and community events",
  "Encourage positive interactions through rewards"
];

const reqs = [
  "Secure login and personalized profiles",
  "User-to-user chat",
  "Content sharing (like stories or posts)",
  "Voice and video calls",
  "Rewards and credit system",
  "Unlockable levels and achievements",
  "Collaboration tools between users"
];


const technologies = [
  { name: "Backend", items: ["MySQL", "PHP"] },
  { name: "Frontend", items: ["HTML", "CSS", "JavaScript"] },
  { name: "Frameworks", items: ["Vue.js", "TailwindCSS", "Laravel"] },
  { name: "Tools", items: ["GitHub", "PHPStorm", "VS Code"] }
];

const team = [
  { name: "Harry White", role: "Frontend Developer", initials: "HW" },
  { name: "Manel Méndez", role: "Frontend Developer", initials: "MM" },
  { name: "Gerard Moreno", role: "Backend Developer", initials: "GM" },
  { name: "Radostin Valeriev", role: "Backend Developer", initials: "RV" }
];
</script>

<style scoped>
/* Animations */
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

.animate-fade-in-delay {
  opacity: 0;
  animation: fade-in 1s ease-out forwards;
  animation-delay: 0.8s;
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

/* Hover effects */
.transform {
  transition: all 0.3s ease;
}
</style>
