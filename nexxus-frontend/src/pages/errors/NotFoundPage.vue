<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-6">
    <div class="text-center animate-fade-in max-w-2xl">
      <h1 class="text-8xl font-extrabold mb-4">
        <span class="jump gradient-text" style="--order: 1">4</span>
        <span class="jump gradient-text" style="--order: 2">0</span>
        <span class="jump gradient-text" style="--order: 3">4</span>
      </h1>
      <p class="text-2xl mb-2 font-medium text-amber-900">Oops! HiveMind can't find this path...</p>
      <p class="text-lg mb-8 text-amber-800">
        This honeycomb is empty! If you think it's an error,
        report it to our worker bees for quick help.
      </p>
      <div class="flex gap-5 justify-center">
        <router-link
          to="/"
          class="btn-primary"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
          </svg>
          Return to Hive
        </router-link>
        <router-link
          to="/contact"
          class="btn-secondary"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
          </svg>
          Contact Worker Bees
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes jump {
  0% { transform: translateY(0) scale(1); }
  30% { transform: translateY(-40px) scale(1.1); }
  60% { transform: translateY(0) scale(1); }
  80% { transform: translateY(-10px) scale(1.02); }
  100% { transform: translateY(0) scale(1); }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.jump {
  display: inline-block;
  animation: jump 2s ease-in-out;
  animation-delay: calc(var(--order) * 0.3s);
  animation-iteration-count: infinite;
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.btn-primary, .btn-secondary {
  @apply px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary {
  @apply bg-gradient-to-r from-amber-500 to-amber-400 text-amber-900;
}

.btn-primary:hover {
  @apply bg-gradient-to-r from-amber-600 to-amber-500 transform -translate-y-0.5;
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.btn-secondary {
  @apply bg-amber-50 text-amber-800 border border-amber-300;
}

.btn-secondary:hover {
  @apply bg-amber-100 transform -translate-y-0.5 border-amber-400;
}
</style>
