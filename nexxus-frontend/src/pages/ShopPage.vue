<template>
  <div class="shop-container">
    <NavBar />
    <router-view
      :subscription-tiers="subscriptionTiers"
      :credit-packs="creditPacks"
      :cosmetic-categories="cosmeticCategories"
    />

    <div class="shop-content container">
      <!-- Welcome Section -->
      <section class="welcome-section">
        <h1 class="text-center title">Welcome to Our Product Page!</h1>
        <p class="text-center subtitle">
          Discover a variety of subscriptions, credits, and cosmetics to personalize your profile.
          Find the perfect option for you below!
        </p>
      </section>

      <!-- Trending Items Section -->
      <section class="trending-section">
        <h2 class="section-title">Trending Items</h2>
        <div class="trending-grid">
          <div v-for="item in trendingItems" :key="item.id" class="trending-item" :class="{ 'purchased': isPurchased(item.id) }">
            <div class="item-icon" :class="{ 'background-preview': item.type === 'background', 'banner-preview': item.type === 'custom_banner' }">
              <img
                :src="item.iconUrl || fallbackImage"
                :alt="item.name"
                class="cosmetic-icon"
                @error="handleImageError($event, item)"
              />
            </div>
            <h3 class="item-name">{{ item.name }}</h3>
            <p class="item-price">{{ formatPrice(item.price) }}</p>
            <button
              @click="goToPurchase(item.id)"
              class="buy-button"
              :disabled="isPurchased(item.id)"
            >
              {{ isPurchased(item.id) ? 'Owned' : 'Purchase' }}
            </button>
          </div>
        </div>
      </section>

      <!-- Subscriptions Section -->
      <section class="subscriptions-section">
        <h2 class="section-title">Subscriptions</h2>
        <div class="subscription-grid">
          <div v-for="tier in subscriptionTiers" :key="tier.id" class="subscription-card" :class="{ 'purchased': isPurchased(tier.id) }">
            <img
              :src="tier.iconUrl || fallbackImage"
              :alt="tier.name"
              class="cosmetic-icon"
              @error="handleImageError($event, tier)"
            />
            <h3 class="tier-title">{{ tier.name }}</h3>
            <p class="tier-price">{{ formatPrice(tier.price) }}</p>
            <button
              @click="goToPurchase(tier.id)"
              class="buy-button"
              :disabled="isPurchased(tier.id)"
            >
              {{ isPurchased(tier.id) ? 'Owned' : 'Purchase' }}
            </button>
          </div>
        </div>
      </section>

      <!-- Credits Section -->
      <section id="buy-credits" class="credits-section">
        <h2 class="section-title">Buy Credits</h2>
        <div class="credits-grid">
          <div v-for="credit in creditPacks" :key="credit.id" class="credit-card">
            <img
              :src="credit.iconUrl || fallbackImage"
              :alt="credit.name"
              class="credit-icon"
              @error="handleImageError($event, credit)"
            />
            <h3 class="credit-amount">{{ credit.name }}</h3>
            <p class="credit-price">{{ formatPrice(credit.price) }}</p>
            <button @click="goToPurchase(credit.id)" class="buy-button">Purchase</button>
          </div>
        </div>
      </section>

      <!-- Cosmetics Section -->
      <section class="cosmetics-section">
        <h2 class="section-title">Cosmetics</h2>
        <div class="cosmetics-grid">
          <div v-for="category in cosmeticCategories" :key="category.title" class="category-card">
            <h3 class="category-title">{{ category.title }}</h3>
            <p class="category-description">{{ category.description }}</p>
            <div class="items-grid">
              <div v-for="item in category.items" :key="item.id" class="cosmetic-item" :class="{ 'purchased': isPurchased(item.id), 'background-item': item.type === 'background', 'banner-item': item.type === 'custom_banner', 'name-effect-item': item.type === 'name_effect' }">
                <div class="item-preview" :class="{ 'background-preview': item.type === 'background', 'banner-preview': item.type === 'custom_banner', 'name-effect-preview': item.type === 'name_effect' }">
                  <img
                    :src="item.iconUrl || fallbackImage"
                    :alt="item.name"
                    class="cosmetic-icon"
                    @error="handleImageError($event, item)"
                  />
                </div>
                <h4 :class="['item-name', category.title === 'Name Effects' ? getNameEffectClass(item.name) : '', category.title === 'Profile Fonts' ? getProfileFontClass(item.name) : '']">
                  {{ item.name }}
                </h4>
                <p class="item-price">{{ formatPrice(item.price) }}</p>
                <button
                  @click="goToPurchase(item.id)"
                  class="buy-button"
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

    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue';
import AppFooter from '../components/AppFooter.vue';
import apiClient from '@/axios.js';

export default {
  name: 'ShopPage',
  components: {
    NavBar,
    AppFooter,
  },
  data() {
    return {
      subscriptionTiers: [],
      creditPacks: [],
      cosmeticCategories: [],
      userInventory: [],
      userId: null,
      fallbackImage: 'https://api.iconify.design/lucide/image-off.svg',
    };
  },
  computed: {
    trendingItems() {
      const allItems = this.cosmeticCategories.flatMap(category => category.items);
      const customBanners = allItems.filter(item => item.type === 'custom_banner');
      const otherItems = allItems.filter(item => item.type !== 'custom_banner');
      // Prioritize custom banners (3) and take 2 other items
      const shuffledBanners = this.shuffleArray([...customBanners]).slice(0, 3);
      const shuffledOthers = this.shuffleArray([...otherItems]).slice(0, 2);
      return [...shuffledBanners, ...shuffledOthers];
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
    async fetchCurrentUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');
        const response = await apiClient.get('/api/user');
        this.userId = response.data.id;
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
      event.target.src = this.fallbackImage; // Set fallback image directly
    },
  },
};
</script>

<style scoped>
@import '../styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

.shop-container {
  min-height: 100vh;
  background-color: #f0f2f5;
  padding-top: 60px;
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.category-description {
  color: #666;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  max-width: 100%;
}

@media (min-width: 768px) {
  .items-grid { grid-template-columns: repeat(4, 1fr); }
}
@media (min-width: 480px) and (max-width: 767px) {
  .items-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 479px) {
  .items-grid { grid-template-columns: repeat(2, 1fr); }
}

.shop-content {
  padding: 2rem 1rem;
}

section {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.text-center { text-align: center; color: #000; }
.title { font-size: 2.5rem; font-weight: bold; color: #000; margin-bottom: 1rem; }
.subtitle { font-size: 1.1rem; color: #000; margin-bottom: 2rem; }
.section-title { font-size: 1.75rem; font-weight: bold; margin-bottom: 1.5rem; color: #000; }
.tier-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem; color: #000; }
.tier-price { font-size: 1.25rem; color: #000; font-weight: bold; margin-bottom: 1.5rem; }

.trending-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
.trending-item { background: #fff; border-radius: 8px; padding: 1.5rem; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.trending-item h3, .trending-item p { color: #000; }

.subscription-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
.subscription-card { background: #fff; border-radius: 8px; padding: 2rem; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.credits-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
.credit-card { background: #fff; border-radius: 8px; padding: 1.5rem; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.credit-card h3, .credit-card p { color: #000; }
.cosmetics-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
.category-card { background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.cosmetic-item { padding: 1rem; background: #f8f9fa; border-radius: 8px; text-align: center; }

.background-item .item-preview,
.banner-item .item-preview {
  height: 250px; /* Increased for better visibility of GIPHY animations */
  overflow: hidden;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  background-color: #e0e0e0; /* Light gray background for contrast */
}

.background-preview .cosmetic-icon,
.banner-preview .cosmetic-icon {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.name-effect-item .item-preview {
  width: 48px;
  height: 48px;
  margin: 0 auto 0.5rem;
}

.cosmetic-icon {
  width: 32px;
  height: 32px;
  margin-bottom: 0.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.category-card h3, .cosmetic-item h4, .cosmetic-item p { color: #000; }
.purchased { background: #e0e0e0; opacity: 0.7; }
.purchased .buy-button { background-color: #a0a0a0; cursor: not-allowed; }
.purchased .buy-button:hover { background-color: #a0a0a0; }

@media (max-width: 768px) {
  .shop-content { padding: 1rem; }
  .title { font-size: 2rem; }
  .section-title { font-size: 1.5rem; }
  .subscription-grid, .cosmetics-grid { grid-template-columns: 1fr; }
  .background-item .item-preview,
  .banner-item .item-preview { height: 180px; } /* Adjusted for smaller screens */
  .trending-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
}

.credit-icon {
  width: 2.5rem;
  height: 2.5rem;
  margin-bottom: 1rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.buy-button {
  background-color: grey;
  color: white;
  border: none;
  padding: 0.5rem 1rem; /* Slightly larger for better clickability */
  border-radius: 4px;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: background-color 0.3s;
}

.buy-button:hover { background-color: darkgrey; }

/* Profile Font Styles */
.font-pixel-art { font-family: 'Press Start 2P', cursive; }
.font-comic-sans { font-family: 'Comic Neue', cursive; }
.font-gothic { font-family: 'Black Ops One', cursive; }
.font-cursive { font-family: 'Dancing Script', cursive; }
.font-typewriter { font-family: 'Courier Prime', monospace; }
.font-bubble { font-family: 'Bungee', cursive; }
.font-neon { font-family: 'Orbitron', sans-serif; }
.font-graffiti { font-family: 'Wallpoet', cursive; }
.font-retro { font-family: 'VT323', monospace; }
.font-cyberpunk { font-family: 'Monoton', cursive; }
.font-western { font-family: 'Special Elite', cursive; }
.font-chalkboard { font-family: 'Creepster', cursive; }
.font-horror { font-family: 'Creepster', cursive; }
.font-futuristic { font-family: 'Audiowide', cursive; }
.font-handwritten { font-family: 'Caveat', cursive; }
.font-bold-script { font-family: 'Permanent Marker', cursive; }
</style>
