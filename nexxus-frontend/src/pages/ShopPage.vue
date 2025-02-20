<template>
  <div class="shop-container">
    <NavBar />

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
          <div v-for="(item, index) in trendingItems" :key="index" class="trending-item">
            <div class="item-icon">
              <img :src="item.iconUrl" :alt="item.name" class="cosmetic-icon">
            </div>
            <h3 class="item-name">{{ item.name }}</h3>
            <p class="item-price">{{ item.price }}</p>
            <button class="buy-button" @click="navigateToPurchase(item)">Purchase</button>          </div>
        </div>
      </section>

      <!-- Subscriptions Section -->
      <section class="subscriptions-section">
        <h2 class="section-title">Subscriptions</h2>
        <div class="subscription-grid">
          <div v-for="tier in subscriptionTiers" :key="tier.title" class="subscription-card">
            <h3 class="tier-title">{{ tier.title }}</h3>
            <p class="tier-price">{{ tier.price }}</p>
            <ul class="tier-features">
              <li v-for="(feature, index) in tier.features" :key="index">
                {{ feature }}
              </li>
            </ul>
            <button class="subscribe-button">Subscribe Now</button>
          </div>
        </div>
      </section>

      <!-- Credits Section -->
      <section id="buy-credits" class="credits-section">
        <h2 class="section-title">Buy Credits</h2>
        <div class="credits-grid">
          <div v-for="(credit, index) in creditPacks" :key="index" class="credit-card">
            <img :src="credit.iconUrl" :alt="credit.amount" class="credit-icon">
            <h3 class="credit-amount">{{ credit.amount }} Credits</h3>
            <p class="credit-price">{{ credit.price }}</p>
            <button class="buy-button" @click="navigateToPurchase(item)">Purchase</button>          </div>
        </div>
      </section>
      <!-- Cosmetics Section -->
      <section class="cosmetics-section">
        <h2 class="section-title">Cosmetics</h2>
        <div class="cosmetics-grid">
          <div class="category-card" v-for="category in cosmeticCategories" :key="category.title">
            <h3 class="category-title">{{ category.title }}</h3>
            <p class="category-description">{{ category.description }}</p>
            <div class="items-grid">
              <div v-for="item in category.items" :key="item.name" class="cosmetic-item">
                <img :src="item.iconUrl" :alt="item.name" class="cosmetic-icon">
                <h4 class="item-name">{{ item.name }}</h4>
                <p class="item-price">{{ item.price }}</p>
                <button class="buy-button" @click="navigateToPurchase(item)">Purchase</button>              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'

export default {
  name: 'ShopPage',
  components: {
    NavBar,
    AppFooter
  },
  data() {
    return {
      subscriptionTiers: [
        {
          title: 'Basic',
          price: 'Free',
          features: [
            'Access to basic features',
            '1 exclusive profile icon',
            '100 monthly credits'
          ]
        },
        {
          title: 'Standard Premium',
          price: '10€/month',
          features: [
            'Advanced features',
            '3 exclusive icons',
            '500 monthly credits'
          ]
        },
        {
          title: 'Ultimate Premium',
          price: '20€/month',
          features: [
            'Full access to all features',
            '10 exclusive icons',
            '1000 monthly credits + bonuses'
          ]
        }
      ],
      creditPacks: [
        { amount: '100', price: '2€', iconUrl: 'https://api.iconify.design/lucide/coins.svg' },
        { amount: '500', price: '8€', iconUrl: 'https://api.iconify.design/lucide/coins.svg' },
        { amount: '1000', price: '15€', iconUrl: 'https://api.iconify.design/lucide/coins.svg' },
        { amount: '5000', price: '60€', iconUrl: 'https://api.iconify.design/lucide/coins.svg' }
      ],
      cosmeticCategories: [
        {
          title: 'Profile Icons',
          description: 'Stand out with unique profile icons that showcase your personality',
          items: [
            { name: 'Crown Icon', price: '250 Credits', iconUrl: 'https://api.iconify.design/lucide/crown.svg' },
            { name: 'Star Icon', price: '150 Credits', iconUrl: 'https://api.iconify.design/lucide/star.svg' },
            { name: 'Heart Icon', price: '100 Credits', iconUrl: 'https://api.iconify.design/lucide/heart.svg' },
            { name: 'Ghost Icon', price: '200 Credits', iconUrl: 'https://api.iconify.design/lucide/ghost.svg' },
            { name: 'Diamond Icon', price: '300 Credits', iconUrl: 'https://api.iconify.design/lucide/diamond.svg' },
            { name: 'Lightning Icon', price: '200 Credits', iconUrl: 'https://api.iconify.design/lucide/zap.svg' },
            { name: 'Fire Icon', price: '200 Credits', iconUrl: 'https://api.iconify.design/lucide/flame.svg' },
            { name: 'Music Icon', price: '150 Credits', iconUrl: 'https://api.iconify.design/lucide/music.svg' }
          ]
        },
        {
          title: 'Backgrounds',
          description: 'Transform your profile with stunning background themes',
          items: [
            { name: 'Galaxy', price: '500 Credits', iconUrl: 'https://api.iconify.design/lucide/sparkles.svg' },
            { name: 'Night City', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/building-2.svg' },
            { name: 'Mountain', price: '300 Credits', iconUrl: 'https://api.iconify.design/lucide/mountain.svg' },
            { name: 'Blossom', price: '350 Credits', iconUrl: 'https://api.iconify.design/lucide/flower-2.svg' },
            { name: 'Ocean', price: '450 Credits', iconUrl: 'https://api.iconify.design/lucide/waves.svg' },
            { name: 'Forest', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/trees.svg' },
            { name: 'Desert', price: '350 Credits', iconUrl: 'https://api.iconify.design/lucide/sun.svg' },
            { name: 'Space', price: '550 Credits', iconUrl: 'https://api.iconify.design/lucide/moon.svg' }
          ]
        },
        {
          title: 'Animations',
          description: 'Add dynamic effects to make your profile come alive',
          items: [
            { name: 'Sparkle', price: '600 Credits', iconUrl: 'https://api.iconify.design/lucide/sparkles.svg' },
            { name: 'Wind', price: '700 Credits', iconUrl: 'https://api.iconify.design/lucide/wind.svg' },
            { name: 'Fire', price: '800 Credits', iconUrl: 'https://api.iconify.design/lucide/flame.svg' },
            { name: 'Wave', price: '750 Credits', iconUrl: 'https://api.iconify.design/lucide/waves.svg' },
            { name: 'Rainbow', price: '900 Credits', iconUrl: 'https://api.iconify.design/lucide/palette.svg' },
            { name: 'Glitch', price: '850 Credits', iconUrl: 'https://api.iconify.design/lucide/scan-line.svg' },
            { name: 'Pulse', price: '700 Credits', iconUrl: 'https://api.iconify.design/lucide/activity.svg' },
            { name: 'Orbit', price: '800 Credits', iconUrl: 'https://api.iconify.design/lucide/orbit.svg' }
          ]
        },
        {
          title: 'Emojis',
          description: 'Express yourself with premium animated emojis',
          items: [
            { name: 'Smile Emoji', price: '50 Credits', iconUrl: 'https://api.iconify.design/lucide/smile.svg' },
            { name: 'Laugh Emoji', price: '50 Credits', iconUrl: 'https://api.iconify.design/lucide/laugh.svg' },
            { name: 'Wink Emoji', price: '50 Credits', iconUrl: 'https://api.iconify.design/lucide/smile-plus.svg' },
            { name: 'Cool Emoji', price: '50 Credits', iconUrl: 'https://api.iconify.design/lucide/sun.svg' },
            { name: 'Party Emoji', price: '75 Credits', iconUrl: 'https://api.iconify.design/lucide/party-popper.svg' },
            { name: 'Love Emoji', price: '75 Credits', iconUrl: 'https://api.iconify.design/lucide/heart-handshake.svg' },
            { name: 'Surprised Emoji', price: '50 Credits', iconUrl: 'https://api.iconify.design/lucide/circle-dot.svg' },
            { name: 'Star Eyes Emoji', price: '75 Credits', iconUrl: 'https://api.iconify.design/lucide/star.svg' }
          ]
        },
        {
          title: 'Name Effects',
          description: 'Make your username stand out with eye-catching effects',
          items: [
            { name: 'Gradient', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/gradient.svg' },
            { name: 'Neon', price: '450 Credits', iconUrl: 'https://api.iconify.design/lucide/lamp.svg' },
            { name: 'Gold', price: '500 Credits', iconUrl: 'https://api.iconify.design/lucide/badge.svg' },
            { name: 'Rainbow', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/palette.svg' },
            { name: 'Glitter', price: '450 Credits', iconUrl: 'https://api.iconify.design/lucide/sparkles.svg' },
            { name: 'Shadow', price: '350 Credits', iconUrl: 'https://api.iconify.design/lucide/box.svg' },
            { name: 'Pixel', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/square.svg' },
            { name: 'Cosmic', price: '500 Credits', iconUrl: 'https://api.iconify.design/lucide/stars.svg' }
          ]
        },
        {
          title: 'Profile Frames',
          description: 'Frame your profile picture with stunning borders',
          items: [
            { name: 'Golden Frame', price: '300 Credits', iconUrl: 'https://api.iconify.design/lucide/square.svg' },
            { name: 'Crystal Frame', price: '350 Credits', iconUrl: 'https://api.iconify.design/lucide/pentagon.svg' },
            { name: 'Flame Frame', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/flame.svg' },
            { name: 'Nature Frame', price: '300 Credits', iconUrl: 'https://api.iconify.design/lucide/flower.svg' },
            { name: 'Tech Frame', price: '350 Credits', iconUrl: 'https://api.iconify.design/lucide/cpu.svg' },
            { name: 'Star Frame', price: '400 Credits', iconUrl: 'https://api.iconify.design/lucide/star.svg' },
            { name: 'Cloud Frame', price: '300 Credits', iconUrl: 'https://api.iconify.design/lucide/cloud.svg' },
            { name: 'Royal Frame', price: '450 Credits', iconUrl: 'https://api.iconify.design/lucide/crown.svg' }
          ]
        }
      ]
    }
  },
  computed: {
    trendingItems() {
      const allItems = this.cosmeticCategories.flatMap(category => category.items);
      return this.shuffleArray(allItems).slice(0, 5);
    }
  },
  methods: {
    shuffleArray(array) {
      for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
      }
      return array;
    },
    navigateToPurchase(item) {
      this.$router.push({ name: 'PurchasePage', params: { item } });
    }
  }
}

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
  grid-template-columns: repeat(4, 1fr); /* 4 columns for all screen sizes */
  gap: 1rem;
  max-width: 100%;
}

@media (min-width: 768px) {
  .items-grid {
    grid-template-columns: repeat(4, 1fr); /* Exactly 4 columns on desktop */
  }
}

@media (min-width: 480px) and (max-width: 767px) {
  .items-grid {
    grid-template-columns: repeat(3, 1fr); /* 3 columns on tablets */
  }
}

@media (max-width: 479px) {
  .items-grid {
    grid-template-columns: repeat(2, 1fr); /* 2 columns on mobile */
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

.tier-features {
  list-style: none;
  padding: 0;
  margin-bottom: 1.5rem;
}

.tier-features li {
  margin-bottom: 0.5rem;
  color: #666;
}

/* Credits Section */
.credits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

/* Buttons */
.buy-button,
.subscribe-button {
  background-color: #2563eb;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
}

.buy-button:hover,
.subscribe-button:hover {
  background-color: #1d4ed8;
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
  .credits-grid,
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
</style>
