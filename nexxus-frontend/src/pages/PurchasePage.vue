<template>
  <div class="purchase-container">
    <NavBar />
    <div class="purchase-content container">
      <h1 class="text-center title">Checkout</h1>
      <div class="item-details">
        <img :src="item.iconUrl" :alt="item.name" class="item-icon">
        <h2>{{ item.name }}</h2>
        <p>{{ item.price }}</p>
      </div>
      <form @submit.prevent="handlePurchase">
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
import { VTextField, VBtn } from 'vuetify/components'

export default {
  name: 'PurchasePage',
  components: {
    NavBar,
    AppFooter,
    VTextField,
    VBtn
  },
  data() {
    return {
      item: {},
      cardNumber: '',
      expiryDate: '',
      cvv: ''
    }
  },
  created() {
    this.item = this.$route.params.item;
  },
  methods: {
    handlePurchase() {
      console.log('Purchase button clicked');
      console.log('Card Number:', this.cardNumber);
      console.log('Expiry Date:', this.expiryDate);
      console.log('CVV:', this.cvv);
      // Handle the purchase logic here
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
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1rem;
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

form {
  display: flex;
  flex-direction: column;
}

v-text-field {
  margin-bottom: 1rem;
}
</style>
