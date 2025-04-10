<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <Navbar />

    <div class="max-w-4xl mx-auto px-6 py-12 animate-fade-in">
      <!-- Encabezado animado -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold mb-4">
          <span class="animate-letter gradient-text" style="--order: 1">H</span>
          <span class="animate-letter gradient-text" style="--order: 2">i</span>
          <span class="animate-letter gradient-text" style="--order: 3">v</span>
          <span class="animate-letter gradient-text" style="--order: 4">e</span>
          <span class="animate-letter gradient-text" style="--order: 5">M</span>
          <span class="animate-letter gradient-text" style="--order: 6">i</span>
          <span class="animate-letter gradient-text" style="--order: 7">n</span>
          <span class="animate-letter gradient-text" style="--order: 8">d</span>
          <span class="animate-letter gradient-text" style="--order: 9">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 10">T</span>
          <span class="animate-letter gradient-text" style="--order: 11">e</span>
          <span class="animate-letter gradient-text" style="--order: 12">r</span>
          <span class="animate-letter gradient-text" style="--order: 13">m</span>
          <span class="animate-letter gradient-text" style="--order: 14">s</span>
        </h1>
        <p class="text-xl text-amber-800 animate-fade-in-delay">
          The rules of our hive community
        </p>
      </div>

      <!-- Contenido de Términos -->
      <div class="bg-white rounded-xl shadow-lg p-8 md:p-10 space-y-8">
        <!-- Sección 1 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Welcome to the Hive
          </h2>
          <p class="term-text">
            These Terms of Service govern your access to and use of HiveMind by Nexxus.<br>
            By joining our hive, you're agreeing to create a positive, respectful community for all members.
          </p>
        </div>

        <!-- Sección 2 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Joining Our Community
          </h2>
          <p class="term-text">
            By creating an account or using HiveMind services, you confirm you're at least 13 years old and agree to these Terms.
            If you're under 18, you affirm that a parent or guardian has reviewed and agrees to these Terms on your behalf.
          </p>
        </div>

        <!-- Sección 3 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Your Hive Account
          </h2>
          <p class="term-text">
            To access all hive features, you'll need to create an account with accurate information.
            You're responsible for maintaining the confidentiality of your login credentials and all activities under your account.
          </p>
          <ul class="term-list">
            <li>Provide true, current and complete information</li>
            <li>Choose a strong password and keep it secure</li>
            <li>Immediately notify us of any unauthorized use</li>
          </ul>
        </div>

        <!-- Sección 4 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Community Guidelines
          </h2>
          <p class="term-text">
            Help us maintain a welcoming hive by respecting all members. Prohibited activities include:
          </p>
          <ul class="term-list">
            <li>Harassment, bullying, or hate speech</li>
            <li>Sharing inappropriate or illegal content</li>
            <li>Spamming or unauthorized promotions</li>
            <li>Impersonation or false representation</li>
            <li>Any activity that violates laws or others' rights</li>
          </ul>
        </div>

        <!-- Sección 5 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Content & Ownership
          </h2>
          <p class="term-text">
            You retain ownership of content you create in our hive, but grant us a license to display and distribute it.
            We may remove content that violates these Terms without notice.
          </p>
        </div>

        <!-- Sección 6 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Account Termination
          </h2>
          <p class="term-text">
            We reserve the right to suspend or terminate accounts that violate these Terms.
            You may also terminate your account at any time through your account settings.
          </p>
        </div>

        <!-- Sección 7 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Changes to Our Terms
          </h2>
          <p class="term-text">
            We may update these Terms as our hive evolves. We'll notify you of significant changes,
            and continued use after changes constitutes acceptance of the new Terms.
          </p>
        </div>

        <!-- Sección 8 -->
        <div class="term-section">
          <h2 class="term-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Contact the Hive Keepers
          </h2>
          <p class="term-text">
            Questions about these Terms? Reach our worker bees at:
            <a href="mailto:hivemindnexxuscontact@gmail.com" class="text-amber-600 hover:underline">hivemindnexxuscontact@gmail.com</a>,
            or by using the contact form available on our <a href="/contact" class="text-amber-600 hover:underline">Contact page</a>.
          </p>

          <p class="term-text mt-2">
            Last updated: April 2025
          </p>
        </div>
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

/* Term sections */
.term-section {
  @apply pb-6 border-b border-amber-100 last:border-0 last:pb-0;
}

.term-title {
  @apply text-2xl font-bold text-amber-700 mb-3 flex items-center;
}

.term-text {
  @apply text-gray-700 mb-3 leading-relaxed;
}

.term-list {
  @apply list-disc pl-5 space-y-1 mt-2 text-gray-700;
}

.term-list li {
  @apply pl-2;
}
</style>
