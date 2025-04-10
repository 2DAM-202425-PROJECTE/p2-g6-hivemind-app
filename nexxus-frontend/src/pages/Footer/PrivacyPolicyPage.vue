<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <Navbar />

    <div class="max-w-4xl mx-auto px-6 py-12 animate-fade-in">
      <!-- Animated Header -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold mb-4">
          <span class="animate-letter gradient-text" style="--order: 1">P</span>
          <span class="animate-letter gradient-text" style="--order: 2">r</span>
          <span class="animate-letter gradient-text" style="--order: 3">i</span>
          <span class="animate-letter gradient-text" style="--order: 4">v</span>
          <span class="animate-letter gradient-text" style="--order: 5">a</span>
          <span class="animate-letter gradient-text" style="--order: 6">c</span>
          <span class="animate-letter gradient-text" style="--order: 7">y</span>
          <span class="animate-letter gradient-text" style="--order: 8">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 9">P</span>
          <span class="animate-letter gradient-text" style="--order: 10">o</span>
          <span class="animate-letter gradient-text" style="--order: 11">l</span>
          <span class="animate-letter gradient-text" style="--order: 12">i</span>
          <span class="animate-letter gradient-text" style="--order: 13">c</span>
          <span class="animate-letter gradient-text" style="--order: 14">y</span>
        </h1>
        <p class="text-xl text-amber-800 animate-fade-in-delay">
          How we protect your data in the Hive
        </p>
      </div>

      <!-- Privacy Content -->
      <div class="bg-white rounded-xl shadow-lg p-8 md:p-10 space-y-8">
        <!-- Section 1 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Data We Collect
          </h2>
          <p class="policy-text">
            To provide and improve our services, we collect:
          </p>
          <ul class="policy-list">
            <li>Account information (username, email, profile details)</li>
            <li>Content you create (posts, messages, comments)</li>
            <li>Usage data (how you interact with our services)</li>
            <li>Device information (for technical optimization)</li>
          </ul>
        </div>

        <!-- Section 2 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            How We Use Your Data
          </h2>
          <p class="policy-text">
            Your information helps us:
          </p>
          <ul class="policy-list">
            <li>Provide and maintain our services</li>
            <li>Improve and personalize your experience</li>
            <li>Develop new features and functionality</li>
            <li>Communicate important service updates</li>
            <li>Ensure community safety and security</li>
          </ul>
        </div>

        <!-- Section 3 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Data Sharing
          </h2>
          <p class="policy-text">
            We respect your privacy. We don't sell your data and only share it:
          </p>
          <ul class="policy-list">
            <li>With your consent for specific purposes</li>
            <li>With service providers who assist our operations</li>
            <li>When required by law or to protect rights/safety</li>
            <li>During business transfers (mergers or acquisitions)</li>
          </ul>
        </div>

        <!-- Section 4 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Your Rights
          </h2>
          <p class="policy-text">
            You have control over your data:
          </p>
          <ul class="policy-list">
            <li>Access and review your personal information</li>
            <li>Update or correct inaccurate data</li>
            <li>Request deletion of your account and data</li>
            <li>Opt-out of marketing communications</li>
            <li>Download your data for portability</li>
          </ul>
        </div>

        <!-- Section 5 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Data Security
          </h2>
          <p class="policy-text">
            We implement robust security measures including:
          </p>
          <ul class="policy-list">
            <li>Encryption of sensitive data</li>
            <li>Regular security audits</li>
            <li>Secure server infrastructure</li>
            <li>Access controls and authentication</li>
          </ul>
        </div>

        <!-- Section 6 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Policy Updates
          </h2>
          <p class="policy-text">
            We may update this policy as our services evolve. We'll notify you of significant changes through our platform or email.
          </p>
        </div>

        <!-- Section 7 -->
        <div class="policy-section">
          <h2 class="policy-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Contact Us
          </h2>
          <p class="term-text">
            Questions about these Terms? Reach our worker bees at:
            <a href="mailto:hivemindnexxuscontact@gmail.com" class="text-amber-600 hover:underline">hivemindnexxuscontact@gmail.com</a>,
            or by using the contact form available on our <a href="/contact" class="text-amber-600 hover:underline">Contact page</a>.
          </p>
          
          <p class="policy-text mt-2">
            Effective Date: April 2025
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

/* Policy sections */
.policy-section {
  @apply pb-6 border-b border-amber-100 last:border-0 last:pb-0;
}

.policy-title {
  @apply text-2xl font-bold text-amber-700 mb-3 flex items-center;
}

.policy-text {
  @apply text-gray-700 mb-3 leading-relaxed;
}

.policy-list {
  @apply list-disc pl-5 space-y-1 mt-2 text-gray-700;
}

.policy-list li {
  @apply pl-2;
}
</style>
