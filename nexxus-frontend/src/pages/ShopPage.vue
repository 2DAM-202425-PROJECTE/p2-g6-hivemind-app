<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <Navbar />
    <div class="max-w-7xl mx-auto px-6 py-12 animate-fade-in">
      <!-- Header with bee theme -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold mb-4">
          <span class="animate-letter gradient-text" style="--order: 1">S</span>
          <span class="animate-letter gradient-text" style="--order: 2">h</span>
          <span class="animate-letter gradient-text" style="--order: 3">o</span>
          <span class="animate-letter gradient-text" style="--order: 4">p</span>
          <span class="animate-letter gradient-text" style="--order: 5">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 6">t</span>
          <span class="animate-letter gradient-text" style="--order: 7">h</span>
          <span class="animate-letter gradient-text" style="--order: 8">e</span>
          <span class="animate-letter gradient-text" style="--order: 9">&nbsp;</span>
          <span class="animate-letter gradient-text" style="--order: 10">H</span>
          <span class="animate-letter gradient-text" style="--order: 11">i</span>
          <span class="animate-letter gradient-text" style="--order: 12">v</span>
          <span class="animate-letter gradient-text" style="--order: 13">e</span>
        </h1>


        <p class="text-lg text-amber-800">
          Browse our selection of premium items to enhance your experience. Worker bees work hard to bring you these products!
        </p>
      </div>

      <!-- Main shop content -->
      <div class="space-y-12">
        <!-- Trending Items Section -->
        <section class="bg-white rounded-xl shadow-lg p-8 trending-items">
          <h2 class="text-3xl font-bold mb-6 text-amber-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Trending Items
          </h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="item in trendingItems" :key="item.id" class="bg-amber-50 rounded-lg p-4" :class="{ 'opacity-60': isPurchased(item.id) }">
              <div class="item-preview h-24 w-24 rounded-md overflow-hidden mb-3 mx-auto">
                <img
                  :src="item.iconUrl || fallbackImage"
                  :alt="item.name"
                  class="w-full h-full"
                  :class="{ 'object-cover': item.type === 'custom_banner' || item.type === 'background', 'object-contain': item.type !== 'custom_banner' && item.type !== 'background' }"
                  @error="handleImageError($event, item)"
                />
              </div>
              <h3 class="font-bold text-lg text-amber-900">{{ item.name }}</h3>
              <p class="text-amber-700 font-medium mb-3">{{ formatPrice(item.price) }}</p>
              <button
                @click="goToPurchase(item.id)"
                class="btn-primary w-full"
                :disabled="isPurchased(item.id)"
              >
                {{ isPurchased(item.id) ? 'Owned' : 'Purchase' }}
              </button>
            </div>
          </div>
        </section>

        <!-- Subscriptions Section -->
        <section class="bg-white rounded-xl shadow-lg p-8">
          <h2 class="text-3xl font-bold mb-6 text-amber-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Subscriptions
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="tier in subscriptionTiers" :key="tier.id" class="bg-amber-50 rounded-lg p-6 border-2 border-amber-200">
              <img
                :src="tier.iconUrl || fallbackImage"
                :alt="tier.name"
                class="w-16 h-16 mx-auto mb-4"
                @error="handleImageError($event, tier)"
              />
              <h3 class="text-xl font-bold text-center color-black mb-2">{{ tier.name }}</h3>
              <ul class="feature-list text-left color-black mb-4">
                <li class="flex items-center">
                  <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Includes {{ tier.freeItemsCount }} {{ tier.freeItemsCount === 1 ? 'item' : 'items' }}
                </li>
                <li class="flex items-center">
                  <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ tier.freeCredits }} {{ tier.freeCredits === 1 ? 'credit' : 'credits' }} {{ tier.price === 'Free' ? '' : 'monthly' }}
                </li>
              </ul>
              <p class="text-center font-medium color-black mb-4">{{ formatPrice(tier.price) }}</p>
              <button
                @click="handleSubscription(tier)"
                class="w-full text-sm py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center"
                :class="{
                  'btn-owned': tier.status === 'Owned',
                  'btn-downgrade': tier.status === 'Downgrade',
                  'btn-available': tier.status === 'Available'
                }"
                :disabled="tier.status === 'Owned'"
              >
                {{ tier.status === 'Owned' ? 'Owned' : tier.status === 'Downgrade' ? 'Downgrade' : 'Buy' }}
              </button>
            </div>
          </div>
        </section>

        <!-- Credits Section -->
        <section id="buy-credits" class="bg-white rounded-xl shadow-lg p-8">
          <h2 class="text-3xl font-bold mb-6 text-amber-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Buy Credits
          </h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="credit in creditPacks" :key="credit.id" class="bg-amber-50 rounded-lg p-4 text-center" :class="{ 'opacity-60': isPurchased(credit.id) }">
              <div class="credit-preview rounded-md overflow-hidden mb-3 mx-auto">
                <img
                  :src="credit.iconUrl || fallbackImage"
                  :alt="credit.name"
                  class="w-full h-full object-contain"
                  @error="handleImageError($event, credit)"
                />
              </div>
              <h3 class="font-bold color-black">{{ credit.name }}</h3>
              <p class="font-medium mb-3 color-black">{{ formatPrice(credit.price) }}</p>
              <button
                @click="goToPurchase(credit.id)"
                class="btn-primary w-full"
                :disabled="isPurchased(credit.id)"
              >
                {{ isPurchased(credit.id) ? 'Owned' : 'Purchase' }}
              </button>
            </div>
          </div>
        </section>

        <!-- Cosmetics Section -->
        <section class="bg-white rounded-xl shadow-lg p-8">
          <h2 class="text-3xl font-bold mb-6 text-amber-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            Cosmetics
          </h2>

          <div class="space-y-8">
            <div v-for="category in cosmeticCategories" :key="category.title" class="bg-amber-50 rounded-lg p-6">
              <h3 class="text-2xl font-bold mb-3 text-amber-900">
                {{ category.title }}
                <span class="text-sm font-medium text-amber-700">
                  ({{ getOwnedCount(category.items) }}/{{ category.items.length }} owned)
                </span>
              </h3>
              <p class="text-amber-800 mb-4">{{ category.description }}</p>

              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="item in category.items" :key="item.id" class="bg-white rounded-lg p-3" :class="{ 'opacity-60': isPurchased(item.id) }">
                  <div
                    class="item-preview rounded-md overflow-hidden mb-2"
                    :class="{
                      'background-preview': item.type === 'background',
                      'banner-preview': item.type === 'custom_banner',
                      'name-effect-preview': item.type === 'name_effect',
                      'profile-icon-preview': item.type === 'profile_icon',
                      'profile-font-preview': item.type === 'profile_font',
                      'badge-preview': item.type === 'badge'
                    }"
                  >
                    <img
                      :src="item.iconUrl || fallbackImage"
                      :alt="item.name"
                      class="w-full h-full object-cover"
                      @error="handleImageError($event, item)"
                    />
                  </div>
                  <h4 :class="[
                    'font-medium text-amber-900 truncate',
                    category.title === 'Name Effects' ? getNameEffectClass(item.name) : '',
                    category.title === 'Profile Fonts' ? getProfileFontClass(item.name) : '',
                    category.title === 'Name Effects' ? 'effect-active' : ''
                  ]">
                    {{ item.name }}
                  </h4>
                  <p class="text-amber-700 text-sm font-medium mb-2">{{ formatPrice(item.price) }}</p>
                  <button
                    @click="goToPurchase(item.id)"
                    class="btn-primary w-full text-sm py-1"
                    :disabled="isPurchased(item.id)"
                  >
                    {{ isPurchased(item.id) ? 'Owned' : 'Purchase' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script>
import Navbar from '@/components/NavBar.vue';
import Footer from '@/components/AppFooter.vue';
import apiClient from '@/axios.js';

export default {
  name: 'ShopPage',
  components: {
    Navbar,
    Footer,
  },
  data() {
    return {
      subscriptionTiers: [],
      creditPacks: [],
      cosmeticCategories: [],
      userInventory: [],
      userId: null,
      currentSubscriptionTier: null,
      fallbackImage: 'https://api.iconify.design/lucide/image-off.svg',
    };
  },
  computed: {
    trendingItems() {
      const allItems = this.cosmeticCategories.flatMap(category => category.items);
      const availableItems = allItems.filter(item => !this.userInventory.includes(item.id));
      const customBanners = availableItems.filter(item => item.type === 'custom_banner');
      const otherItems = availableItems.filter(item => item.type !== 'custom_banner');
      const shuffledBanners = this.shuffleArray([...customBanners]).slice(0, Math.min(2, customBanners.length));
      const remainingSlots = 4 - shuffledBanners.length;
      const shuffledOthers = this.shuffleArray([...otherItems]).slice(0, Math.min(remainingSlots, otherItems.length));
      return [...shuffledBanners, ...shuffledOthers].slice(0, 4);
    },
  },
  created() {
    this.fetchCurrentUser();
    this.fetchCategorizedItems();
  },
  methods: {
    getNameEffectClass(name) {
      if (!name) return '';
      return name.toLowerCase().replace(/\s+/g, '-');
    },
    getProfileFontClass(name) {
      switch (name) {
        case 'Pixel Art': return 'font-pixel-art';
        case 'Comic Sans': return 'font-comic-sans';
        case 'Gothic': return 'font-gothic';
        case 'Cursive': return 'font-cursive';
        case 'Typewriter': return 'font-typewriter';
        case 'Bubble': return 'font-bubble';
        case 'Neon': return 'font-neon';
        case 'Graffiti': return 'font-graffiti';
        case 'Retro': return 'font-retro';
        case 'Cyberpunk': return 'font-cyberpunk';
        case 'Western': return 'font-western';
        case 'Chalkboard': return 'font-chalkboard';
        case 'Horror': return 'font-horror';
        case 'Futuristic': return 'font-futuristic';
        case 'Handwritten': return 'font-handwritten';
        case 'Bold Script': return 'font-bold-script';
        default: return '';
      }
    },
    getOwnedCount(items) {
      return items.filter(item => this.userInventory.includes(item.id)).length;
    },
    getFreeItemsCount(tierName) {
      switch (tierName) {
        case 'Basic': return 1;
        case 'Premium': return 3;
        case 'Elite': return 5;
        default: return 0;
      }
    },
    getFreeCreditsForTier(tierName) {
      switch (tierName) {
        case 'Basic': return 100;
        case 'Premium': return 500;
        case 'Elite': return 1000;
        default: return 0;
      }
    },
    async fetchCurrentUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');
        const response = await apiClient.get('/api/user');
        this.userId = response.data.id;
        this.currentSubscriptionTier = response.data.subscriptionTier || null;
        await this.fetchUserInventory();
      } catch (error) {
        console.error('Failed to fetch current user:', error);
      }
    },
    async fetchUserInventory() {
      try {
        const response = await apiClient.get(`/api/user/${this.userId}/inventory`);
        this.userInventory = response.data.map(item => item.item_id);
      } catch (error) {
        console.error('Failed to fetch user inventory:', error);
      }
    },
    async fetchCategorizedItems() {
      try {
        const response = await apiClient.get('/api/shop/categorized-items');
        this.subscriptionTiers = response.data.subscriptions.map(tier => ({
          ...tier,
          price: tier.price === 0 ? 'Free' : `${tier.price}€/month`,
          freeItemsCount: this.getFreeItemsCount(tier.name),
          freeCredits: this.getFreeCreditsForTier(tier.name),
          status: tier

            .status // Include status field from backend
        }));
        this.creditPacks = response.data.creditPacks
          .sort((a, b) => parseInt(a.name) - parseInt(b.name))
          .map(pack => ({
            ...pack,
            price: `${pack.price}€`,
          }));
        this.cosmeticCategories = this.categorizeCosmetics(response.data.cosmetics);
      } catch (error) {
        console.error('Failed to fetch categorized items:', error);
      }
    },
    async handleSubscription(tier) {
      try {
        if (tier.status === 'Owned') {
          console.log(`User already subscribed to ${tier.name}`);
          return;
        }
        await this.goToPurchase(tier.id);
      } catch (error) {
        console.error('Failed to handle subscription:', error);
      }
    },
    categorizeCosmetics(cosmetics) {
      return [
        {
          title: 'Profile Icons',
          description: 'Stand out with unique profile icons that showcase your personality',
          items: cosmetics.filter(item => item.type === 'profile_icon'),
        },
        {
          title: 'Backgrounds',
          description: 'Transform your profile with stunning background themes',
          items: cosmetics.filter(item => item.type === 'background'),
        },
        {
          title: 'Profile Fonts',
          description: 'Customize your profile text with unique fonts',
          items: cosmetics.filter(item => item.type === 'profile_font'),
        },
        {
          title: 'Name Effects',
          description: 'Make your username stand out with eye-catching effects',
          items: cosmetics.filter(item => item.type === 'name_effect'),
        },
        {
          title: 'Custom Banners',
          description: 'Enhance your profile header with vibrant animated banners',
          items: cosmetics.filter(item => item.type === 'custom_banner'),
        },
        {
          title: 'Profile Badges',
          description: 'Show off your status with exclusive profile badges',
          items: cosmetics.filter(item => item.type === 'badge'),
        },
      ].filter(category => category.items.length > 0);
    },
    formatPrice(price) {
      if (typeof price === 'string') return price;
      return price === 0 ? 'Free' : `${price} Credits`;
    },
    shuffleArray(array) {
      for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
      }
      return array;
    },
    goToPurchase(itemId) {
      if (!this.isPurchased(itemId)) {
        this.$router.push({ path: `/purchase/${itemId}` });
      }
    },
    isPurchased(itemId) {
      return this.userInventory.includes(itemId);
    },
    handleImageError(event, item) {
      console.warn(`Image failed to load for ${item.name} (ID: ${item.id}): ${item.iconUrl}`);
      event.target.src = this.fallbackImage;
    },
  },
};
</script>

<style scoped>
@import '../styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

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

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

/* Button styles */
.btn-primary {
  @apply px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center;
  @apply bg-gradient-to-r from-amber-500 to-amber-400;
  color: #000000;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  @apply bg-gradient-to-r from-amber-600 to-amber-500;
  box-shadow: 0 10px 15px -3px rgba(245 spectacles, 11, 0.3);
}

.btn-primary:disabled {
  @apply bg-gray-400 cursor-not-allowed;
  box-shadow: none;
}

.btn-owned {
  background: #4caf50;
  color: white;
  cursor: not-allowed;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-downgrade {
  background: #f44336;
  color: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-downgrade:hover {
  background: #d32f2f;
  box-shadow: 0 10px 15px -3px rgba(244, 67, 54, 0.3);
}

.btn-available {
  background-image: linear-gradient(to right, #f59e0b, #d97706);
  color: #000000;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-available:hover {
  background-image: linear-gradient(to right, #f59e0b, #b45309);
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

/* Item preview styles for Trending Items */
.trending-items .item-preview {
  background-color: #f3e8d3;
  height: 96px;
  width: 96px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.trending-items .item-preview img {
  width: 100%;
  height: 100%;
  padding: 8px;
}

.trending-items .item-preview.banner-preview img,
.trending-items .item-preview.background-preview img {
  padding: 0;
}

/* Item preview styles for Credits section */
.credit-preview {
  background-color: #f3e8d3;
  height: 96px;
  width: 96px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.credit-preview img {
  width: 100%;
  height: 100%;
  padding: 8px;
}

/* Item preview styles for Cosmetics section */
.background-preview, .banner-preview {
  background-color: #f3e8d3;
  position: relative;
  height: 96px;
  width: 96px;
  margin: 0 auto;
}

.name-effect-preview, .profile-icon-preview, .profile-font-preview, .badge-preview {
  background-color: #f3e8d3;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 96px;
  width: 96px;
  margin: 0 auto;
}

.badge-preview img {
  object-fit: contain;
  padding: 8px;
}

.profile-font-preview img, .name-effect-preview img {
  object-fit: contain;
  padding: 4px;
}

/* Feature list for subscriptions */
.feature-list {
  list-style: none;
  padding: 0;
}

.feature-list li {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

/* Ensure name effects are active */
.effect-active {
  display: inline-block;
  padding: 0 0.25rem;
}

/* Profile Font Styles */
.font-pixel-art { font-family: 'Press Start 2P', cursive; font-size: 0.75rem; }
.font-comic-sans { font-family: 'Comic Neue', cursive; font-size: 0.85rem; }
.font-gothic { font-family: 'Black Ops One', cursive; font-size: 0.85rem; }
.font-cursive { font-family: 'Dancing Script', cursive; font-size: 0.85rem; }
.font-typewriter { font-family: 'Courier Prime', monospace; font-size: 0.85rem; }
.font-bubble { font-family: 'Bungee', cursive; font-size: 0.85rem; }
.font-neon { font-family: 'Orbitron', sans-serif; font-size: 0.85rem; }
.font-graffiti { font-family: 'Wallpoet', cursive; font-size: 0.85rem; }
.font-retro { font-family: 'VT323', monospace; font-size: 0.85rem; }
.font-cyberpunk { font-family: 'Monoton', cursive; font-size: 0.85rem; }
.font-western { font-family: 'Special Elite', cursive; font-size: 0.85rem; }
.font-chalkboard { font-family: 'Creepster', cursive; font-size: 0.85rem; }
.font-horror { font-family: 'Creepster', cursive; font-size: 0.85rem; }
.font-futuristic { font-family: 'Audiowide', cursive; font-size: 0.85rem; }
.font-handwritten { font-family: 'Caveat', cursive; font-size: 0.85rem; }
.font-bold-script { font-family: 'Permanent Marker', cursive; font-size: 0.85rem; }

.color-black {
  color: #000000;
}

@media (max-width: 768px) {
  .min-h-screen {
    padding: 1rem;
  }

  .max-w-7xl {
    padding: 0.5rem;
  }

  .grid-cols-2 {
    grid-template-columns: repeat(2, 1fr);
  }

  .trending-items .item-preview {
    height: 64px;
    width: 64px;
  }

  .credit-preview {
    height: 64px;
    width: 64px;
  }

  .background-preview, .banner-preview {
    height: 64px;
    width: 64px;
  }

  .name-effect-preview, .profile-icon-preview, .profile-font-preview, .badge-preview {
    height: 64px;
    width: 64px;
  }
}
</style>
