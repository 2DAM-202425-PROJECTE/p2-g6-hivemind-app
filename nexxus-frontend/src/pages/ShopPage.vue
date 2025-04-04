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
        <h1 class="text-center title">Welcome to our product page!</h1>
        <p class="text-center subtitle">
          Here you will find a wide range of subscriptions and credits to customize your profile.
          Explore our options and choose the one that best suits your needs.
        </p>
      </section>

      <!-- Trending Items Section -->
      <section class="trending-section">
        <h2 class="section-title">Trending Items</h2>
        <div class="trending-grid">
          <div v-for="item in trendingItems" :key="item.id" class="trending-item" :class="{ 'purchased': isPurchased(item.id) }">
            <div class="item-icon" :class="{ 'background-preview': item.type === 'background' }">
              <img :src="item.iconUrl" :alt="item.name" class="cosmetic-icon" @error="handleImageError(item)" />
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
            <img :src="tier.iconUrl" :alt="tier.name" class="cosmetic-icon" @error="handleImageError(tier)" />
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
            <img :src="credit.iconUrl" :alt="credit.name" class="credit-icon" @error="handleImageError(credit)" />
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
              <div v-for="item in category.items" :key="item.id" class="cosmetic-item" :class="{ 'purchased': isPurchased(item.id), 'background-item': item.type === 'background' }">
                <div class="item-preview" :class="{ 'background-preview': item.type === 'background' }">
                  <img :src="item.iconUrl" :alt="item.name" class="cosmetic-icon" @error="handleImageError(item)" />
                </div>
                <h4 class="item-name">{{ item.name }}</h4>
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
      return this.shuffleArray([...allItems]).slice(0, 5);
    },
  },
  created() {
    this.fetchCurrentUser();
    this.fetchCategorizedItems();
  },
  methods: {
    async fetchCurrentUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');

        const response = await apiClient.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.userId = response.data.id;
        await this.fetchUserInventory();
      } catch (error) {
        console.error('Failed to fetch current user:', error);
      }
    },
    async fetchUserInventory() {
      try {
        const token = localStorage.getItem('token');
        const response = await apiClient.get(`/api/user/${this.userId}/inventory`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.userInventory = response.data.map(item => item.item_id);
      } catch (error) {
        console.error('Failed to fetch user inventory:', error);
      }
    },
    async fetchCategorizedItems() {
      try {
        const response = await apiClient.get('/api/shop/categorized-items', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        });
        this.subscriptionTiers = response.data.subscriptions.map(tier => ({
          ...tier,
          price: tier.price === 0 ? 'Free' : `${tier.price}€/month`
        }));
        this.creditPacks = response.data.creditPacks.map(pack => ({
          ...pack,
          price: `${pack.price}€`
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
          items: cosmetics.filter(item =>
            ['Mini Crown', 'Shining Star', 'Glowing Heart', 'Ghostly Aura', 'Crystal Gem', 'Thunder Bolt', 'Moon Glow', 'Sun Flare',
              'Flame Crest', 'Snowflake Spark', 'Leaf Whisper', 'Wave Ripple', 'Cloud Drift', 'Gear Spin', 'Anchor Drop', 'Feather Light'].includes(item.name)
          ),
        },
        {
          title: 'Backgrounds',
          description: 'Transform your profile with stunning background themes',
          items: cosmetics.filter(item =>
            ['Soft Gradient', 'Starry Night', 'Minimal Waves', 'Pastel Sky', 'Urban Glow', 'Forest Mist', 'Ocean Depth', 'Desert Dunes',
              'Mountain Peak', 'Aurora Veil', 'Lush Valley', 'Twilight City', 'Golden Fields', 'Icy Plains', 'Volcanic Ash', 'Cosmic Dust'].includes(item.name)
          ),
        },
        {
          title: 'Animations',
          description: 'Add dynamic effects to make your profile come alive',
          items: cosmetics.filter(item =>
            ['Gentle Sparkle', 'Fading Pulse', 'Soft Ripple', 'Orbit Glow', 'Subtle Glitch', 'Twirl Flash', 'Pulse Wave', 'Star Burst',
              'Meteor Shower', 'Vortex Spin', 'Flame Dance', 'Frost Swirl', 'Electric Surge', 'Shadow Fade', 'Rainbow Pulse', 'Bubble Pop'].includes(item.name)
          ),
        },
        {
          title: 'Name Effects',
          description: 'Make your username stand out with eye-catching effects',
          items: cosmetics.filter(item =>
            ['Soft Glow', 'Gradient Fade', 'Golden Outline', 'Dark Pulse', 'Cosmic Shine', 'Neon Edge', 'Frost Glow', 'Fire Flicker',
              'Emerald Sheen', 'Shadow Drift', 'Electric Glow', 'Lunar Haze', 'Solar Flare', 'Wave Shimmer', 'Crystal Pulse', 'Rainbow Gleam'].includes(item.name)
          ),
        },
        {
          title: 'Profile Frames',
          description: 'Frame your profile picture with stunning borders',
          items: cosmetics.filter(item =>
            ['Golden Ring', 'Crystal Edge', 'Star Border', 'Cloud Frame', 'Tech Circuit', 'Leaf Wreath', 'Wave Crest', 'Pixel Grid',
              'Flame Halo', 'Frost Ring', 'Gear Frame', 'Moon Orbit', 'Sun Burst', 'Vine Wrap', 'Neon Circuit', 'Starfield Edge'].includes(item.name)
          ),
        },
        {
          title: 'Profile Badges',
          description: 'Show off your status with exclusive profile badges',
          items: cosmetics.filter(item =>
            ['Verified Badge', 'Founder Badge', 'VIP Badge', 'Creator Badge', 'Explorer Badge', 'Legend Badge', 'Pioneer Badge', 'Guardian Badge',
              'Warrior Badge', 'Sage Badge', 'Star Gazer Badge', 'Trailblazer Badge', 'Elementalist Badge', 'Innovator Badge', 'Nomad Badge', 'Champion Badge'].includes(item.name)
          ),
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
    handleImageError(item) {
      console.warn(`Image failed to load for ${item.name}: ${item.iconUrl}`);
      item.iconUrl = this.fallbackImage;
    },
  },
};
</script>

<style scoped>
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
  .items-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 480px) and (max-width: 767px) {
  .items-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 479px) {
  .items-grid {
    grid-template-columns: repeat(2, 1fr);
  }
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

.text-center {
  text-align: center;
  color: #000;
}

.title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #000;
  margin-bottom: 1rem;
}

.subtitle {
  font-size: 1.1rem;
  color: #000;
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.75rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  color: #000;
}

.tier-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #000;
}

.tier-price {
  font-size: 1.25rem;
  color: #000;
  font-weight: bold;
  margin-bottom: 1.5rem;
}

/* Trending Section */
.trending-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.trending-item {
  background: #fff;
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.trending-item h3,
.trending-item p {
  color: #000;
}

/* Subscription Grid */
.subscription-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.subscription-card {
  background: #fff;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Credits Section */
.credits-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.credit-card {
  background: #fff;
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.credit-card h3,
.credit-card p {
  color: #000;
}

/* Cosmetics Section */
.cosmetics-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

.category-card {
  background: #fff;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.cosmetic-item {
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  text-align: center;
}

.background-item .item-preview {
  height: 100px;
  overflow: hidden;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.background-preview .cosmetic-icon {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cosmetic-icon {
  width: 2rem;
  height: 2rem;
  margin-bottom: 0.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.category-card h3,
.cosmetic-item h4,
.cosmetic-item p {
  color: #000;
}

/* Purchased Item Styling */
.purchased {
  background: #e0e0e0;
  opacity: 0.7;
}

.purchased .buy-button {
  background-color: #a0a0a0;
  cursor: not-allowed;
}

.purchased .buy-button:hover {
  background-color: #a0a0a0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .shop-content {
    padding: 1rem;
  }

  .title {
    font-size: 2rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .subscription-grid,
  .cosmetics-grid {
    grid-template-columns: 1fr;
  }
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
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: background-color 0.3s;
}

.buy-button:hover {
  background-color: darkgrey;
}
</style>
