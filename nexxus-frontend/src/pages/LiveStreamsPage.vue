<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <Navbar />
    <div class="container mx-auto px-4 py-6">
      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <div class="w-full lg:w-64 flex-shrink-0">
          <div class="bg-white rounded-xl shadow-md p-4 border border-amber-100">
            <h2 class="text-lg font-bold text-amber-900 mb-3">Categories</h2>
            <ul class="space-y-2">
              <li class="px-3 py-2 rounded-lg bg-amber-50 text-amber-800 font-medium">Test Gaming</li>
              <li class="px-3 py-2 rounded-lg hover:bg-amber-50 text-amber-800">Test Music</li>
              <li class="px-3 py-2 rounded-lg hover:bg-amber-50 text-amber-800">Test Art</li>
              <li class="px-3 py-2 rounded-lg hover:bg-amber-50 text-amber-800">Test Chat</li>
              <li class="px-3 py-2 rounded-lg text-amber-500 italic">More Coming Soon...</li>
            </ul>

            <h2 class="text-lg font-bold text-amber-900 mt-6 mb-3">Followed Channels</h2>
            <ul class="space-y-2">
              <li class="flex items-center px-3 py-2 rounded-lg bg-amber-50">
                <img src="https://via.placeholder.com/30" class="w-8 h-8 rounded-full mr-3" alt="Streamer" />
                <span class="text-amber-800">Test Streamer 1</span>
                <span class="ml-auto w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
              </li>
              <li class="flex items-center px-3 py-2 rounded-lg hover:bg-amber-50">
                <img src="https://via.placeholder.com/30" class="w-8 h-8 rounded-full mr-3" alt="Streamer" />
                <span class="text-amber-800">Test Streamer 2</span>
              </li>
              <li class="flex items-center px-3 py-2 rounded-lg text-amber-500">
                <img src="https://via.placeholder.com/30" class="w-8 h-8 rounded-full mr-3 opacity-50" alt="Streamer" />
                <span class="italic">Test Streamer 3 (Offline)</span>
              </li>
            </ul>

            <div class="mt-6 pt-4 border-t border-amber-100">
              <button
                class="w-full py-2 px-4 bg-amber-100 text-amber-800 rounded-lg font-medium disabled:opacity-50"
                disabled
              >
                Test Feature
              </button>
              <p class="text-xs text-amber-600 text-center mt-2">Test Streams: 0</p>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
          <!-- Header -->
          <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-amber-100">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
              <div class="flex space-x-1">
                <button
                  class="px-4 py-2 bg-amber-500 text-white font-medium rounded-lg disabled:opacity-75"
                  disabled
                >
                  Following (Test)
                </button>
                <button
                  class="px-4 py-2 text-amber-800 font-medium rounded-lg hover:bg-amber-50 disabled:opacity-75"
                  disabled
                >
                  Browse (Test)
                </button>
              </div>

              <div class="relative flex-1">
                <input
                  type="text"
                  placeholder="Search streams (Test)"
                  disabled
                  class="w-full pl-10 pr-4 py-2 bg-amber-50 rounded-lg text-amber-800 placeholder-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-300"
                />
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>

              <button
                class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-medium rounded-lg hover:from-amber-600 hover:to-amber-700 transition-all disabled:opacity-75"
                disabled
              >
                Go Live (Coming Soon)
              </button>
            </div>
          </div>

          <!-- Tags Filter -->
          <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-amber-100">
            <h3 class="text-lg font-bold text-amber-900 mb-3">Filter by Tags (Test)</h3>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Test Tag 1</span>
              <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Test Tag 2</span>
              <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">Test Tag 3</span>
              <span class="px-3 py-1 text-amber-500 italic rounded-full text-sm">More Tags Soon</span>
              <button
                class="px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-sm hover:bg-amber-300 disabled:opacity-50"
                disabled
              >
                Test Filter
              </button>
            </div>
          </div>

          <!-- Top Streams -->
          <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-amber-100">
            <h3 class="text-lg font-bold text-amber-900 mb-3">Top Streams (Test)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Stream 1
                </div>
                <p class="font-medium text-amber-900">Test Streamer 1</p>
                <p class="text-sm text-amber-600">0 viewers</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Stream 2
                </div>
                <p class="font-medium text-amber-900">Test Streamer 2</p>
                <p class="text-sm text-amber-600">0 viewers</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Stream 3
                </div>
                <p class="font-medium text-amber-900">Test Streamer 3</p>
                <p class="text-sm text-amber-600">0 viewers</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3 opacity-50">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2 italic">
                  Coming Soon
                </div>
                <p class="font-medium text-amber-900 italic">More Streams</p>
                <p class="text-sm text-amber-600">N/A</p>
              </div>
            </div>
          </div>

          <!-- Coming Soon -->
          <div class="bg-white rounded-xl shadow-md p-8 mb-6 text-center border border-amber-100">
            <h2 class="text-3xl font-bold text-amber-900 mb-4">Coming Soon!</h2>
            <p class="text-lg text-amber-800 mb-6">
              Live streaming features are buzzing with activity in our hive!<br>
              Worker bees are building something amazing for you.
            </p>
            <div class="flex justify-center gap-4">
              <button
                class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg font-medium hover:bg-amber-200 transition-colors disabled:opacity-50"
                disabled
              >
                Test Live Now
              </button>
              <button
                class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg font-medium hover:bg-amber-200 transition-colors disabled:opacity-50"
                disabled
              >
                Test Schedule
              </button>
              <button
                class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg font-medium hover:bg-amber-200 transition-colors disabled:opacity-50"
                disabled
              >
                Test Clips
              </button>
            </div>
          </div>

          <!-- Recommended Channels -->
          <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-amber-100">
            <h3 class="text-lg font-bold text-amber-900 mb-3">Live Channels We Think You'll Like (Test)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Channel A
                </div>
                <p class="font-medium text-amber-900">Test Streamer A</p>
                <p class="text-sm text-amber-600">0 viewers</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Channel B
                </div>
                <p class="font-medium text-amber-900">Test Streamer B</p>
                <p class="text-sm text-amber-600">0 viewers</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Channel C
                </div>
                <p class="font-medium text-amber-900">Test Streamer C</p>
                <p class="text-sm text-amber-600">0 viewers</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3 opacity-50">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2 italic">
                  More Soon
                </div>
                <p class="font-medium text-amber-900 italic">Coming Soon</p>
                <p class="text-sm text-amber-600">N/A</p>
              </div>
            </div>
          </div>

          <!-- Featured Events -->
          <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-amber-100">
            <h3 class="text-lg font-bold text-amber-900 mb-3">Featured Events (Test)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Event 1
                </div>
                <p class="font-medium text-amber-900">Test Tournament</p>
                <p class="text-sm text-amber-600">TBD</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Event 2
                </div>
                <p class="font-medium text-amber-900">Test Concert</p>
                <p class="text-sm text-amber-600">TBD</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3 opacity-50">
                <div class="h-32 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2 italic">
                  More Soon
                </div>
                <p class="font-medium text-amber-900 italic">Coming Soon</p>
                <p class="text-sm text-amber-600">N/A</p>
              </div>
            </div>
          </div>

          <!-- Stream Categories -->
          <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-amber-100">
            <h3 class="text-lg font-bold text-amber-900 mb-3">Test Stream Categories</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Gaming
                </div>
                <p class="text-sm text-amber-600">Test Streams: 0</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Music
                </div>
                <p class="text-sm text-amber-600">Test Streams: 0</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2">
                  Test Art
                </div>
                <p class="text-sm text-amber-600">Test Streams: 0</p>
              </div>
              <div class="bg-amber-50 rounded-lg p-3 opacity-50">
                <div class="h-24 bg-amber-100 rounded-lg flex items-center justify-center text-amber-800 mb-2 italic">
                  More Soon
                </div>
                <p class="text-sm text-amber-600">N/A</p>
              </div>
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
</script>
