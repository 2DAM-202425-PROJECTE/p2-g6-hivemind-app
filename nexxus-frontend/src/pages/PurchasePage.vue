<template>
  <div class="purchase-container">
    <NavBar />
    <div class="purchase-content container">
      <h1 class="text-center title">Checkout</h1>
      <div v-if="item" class="item-details">
        <img :src="item.iconUrl" :alt="item.name" class="item-icon">
        <h2>{{ item.name }}</h2>
        <p>{{ item.price }}</p>
      </div>
      <div v-else class="no-item">
        <p>No item has been selected to purchase.</p>
      </div>
      <form v-if="item" @submit.prevent="handlePurchase">
        <v-text-field v-model="cardNumber" label="Card Number" required></v-text-field>
        <v-text-field v-model="expiryDate" label="Expiry Date (MM/YY)" required></v-text-field>
        <v-text-field v-model="cvv" label="CVV" required></v-text-field>
        <v-btn type="submit" color="primary">Purchase</v-btn>
      </form>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'

export default {
  name: 'PurchasePage',
  components: {
    NavBar,
    AppFooter
  },
  data() {
    return {
      item: null,
      cardNumber: '',
      expiryDate: '',
      cvv: ''
    }
  },
  created() {
    const itemId = this.$route.params.id;
    if (itemId) {
      this.item = this.getItemById(itemId);
    }
  },
  methods: {
    getItemById(id) {
      const allItems = [
        ...this.$root.$data.subscriptionTiers,
        ...this.$root.$data.creditPacks,
        ...this.$root.$data.cosmeticCategories.flatMap(category => category.items)
      ];
      return allItems.find(item => item.id === id);
    },
    handlePurchase() {
      console.log('Purchase button clicked');
      console.log('Card Number:', this.cardNumber);
      console.log('Expiry Date:', this.expiryDate);
      console.log('CVV:', this.cvv);

      alert('Purchase successful!');
      this.$router.push('/shop');
    }
  }
}
</script>

<style scoped>
.purchase-container {
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

.purchase-content {
  padding: 2rem 1rem;
}

.title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #000;
  margin-bottom: 1rem;
  text-align: center;
}

.item-details {
  text-align: center;
  margin-bottom: 2rem;
}

.item-icon {
  width: 100px;
  height: 100px;
  margin-bottom: 1rem;
}

.no-item {
  text-align: center;
  margin-bottom: 2rem;
  color: #666;
}

form {
  display: flex;
  flex-direction: column;
}

v-text-field {
  margin-bottom: 1rem;
}

button {
  background-color: #2563eb;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
}

button:hover {
  background-color: #1d4ed8;
}
</style>
