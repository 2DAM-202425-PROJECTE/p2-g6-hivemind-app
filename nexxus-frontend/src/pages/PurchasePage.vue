<template>
  <div class="purchase-container">
    <NavBar />
    <div class="purchase-content container">
      <!-- Checkout Header -->
      <div class="checkout-header">
        <h1 class="title">Checkout</h1>
        <p class="description">
          All purchases are final. No refunds will be issued once payment is processed. Please review your order carefully before completing the transaction. For support, contact us at <a href="mailto:hivemindnexxuscontact@gmail.com">hivemindnexxuscontact@gmail.com</a>.
        </p>
      </div>

      <!-- Checkout Body -->
      <div class="checkout-container">
        <div class="checkout-inner">
          <!-- Item Details -->
          <div v-if="item" class="item-details-container">
            <div class="item-details">
              <img
                :src="item.iconUrl || 'https://via.placeholder.com/100'"
                :alt="item.name || 'Product Image'"
                class="item-icon"
              >
              <h2>{{ item.name || item.title || 'Unnamed Product' }}</h2>
              <p class="price">{{ item.price || 'Price not available' }}</p>
              <p v-if="item.description" class="description">{{ item.description }}</p>
              <ul v-if="item.features" class="features">
                <li v-for="(feature, index) in item.features" :key="index">{{ feature }}</li>
              </ul>
              <p v-if="item.amount" class="amount">{{ item.amount }} Credits</p>
            </div>
          </div>
          <div v-else class="no-item">
            <p>No item selected or item could not be loaded.</p>
          </div>

          <!-- Payment Section -->
          <div v-if="item" class="payment-container">
            <h3 class="payment-title">Choose Payment Method</h3>
            <div class="payment-methods">
              <!-- Payment Buttons -->
              <div class="payment-buttons">
                <v-btn
                  @click="selectPaymentMethod('card')"
                  :class="{ active: selectedPaymentMethod === 'card' }"
                  class="payment-btn"
                  outlined
                >
                  Credit/Debit Card
                </v-btn>
                <v-btn
                  @click="selectPaymentMethod('paypal')"
                  :class="{ active: selectedPaymentMethod === 'paypal' }"
                  class="payment-btn"
                  outlined
                >
                  PayPal
                </v-btn>
                <v-btn
                  @click="selectPaymentMethod('bizum')"
                  :class="{ active: selectedPaymentMethod === 'bizum' }"
                  class="payment-btn"
                  outlined
                >
                  Bizum
                </v-btn>
                <v-btn
                  @click="selectPaymentMethod('googlepay')"
                  :class="{ active: selectedPaymentMethod === 'googlepay' }"
                  class="payment-btn"
                  outlined
                >
                  Google Pay
                </v-btn>
                <v-btn
                  @click="selectPaymentMethod('credits')"
                  :class="{ active: selectedPaymentMethod === 'credits' }"
                  class="payment-btn"
                  outlined
                >
                  Pay with Credits
                </v-btn>
              </div>

              <!-- Payment Forms (Collapsible) -->
              <transition name="fade">
                <div v-if="selectedPaymentMethod" class="payment-form-container">
                  <div v-if="selectedPaymentMethod === 'card'" class="payment-method">
                    <h4>Credit/Debit Card</h4>
                    <form @submit.prevent="handlePurchase('card')" class="payment-form">
                      <v-text-field v-model="cardNumber" label="Card Number (16 digits)" required outlined :rules="[v => /^\d{16}$/.test(v) || 'Must be 16 digits']"></v-text-field>
                      <v-text-field v-model="expiryDate" label="Expiry Date (MM/YY)" required outlined :rules="[v => /^\d{2}\/\d{2}$/.test(v) || 'Format: MM/YY']"></v-text-field>
                      <v-text-field v-model="cvv" label="CVV (3 digits)" required outlined :rules="[v => /^\d{3}$/.test(v) || 'Must be 3 digits']"></v-text-field>
                      <v-btn type="submit" color="primary" block>Pay with Card</v-btn>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'paypal'" class="payment-method">
                    <h4>PayPal</h4>
                    <form @submit.prevent="handlePurchase('paypal')" class="payment-form">
                      <v-text-field v-model="paypalEmail" label="PayPal Email" type="email" required outlined :rules="[v => /.+@.+\..+/.test(v) || 'Email must contain @']"></v-text-field>
                      <v-text-field v-model="paypalPassword" label="PayPal Password" type="password" required outlined></v-text-field>
                      <v-btn type="submit" color="primary" block>Pay with PayPal</v-btn>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'bizum'" class="payment-method">
                    <h4>Bizum</h4>
                    <form @submit.prevent="handlePurchase('bizum')" class="payment-form">
                      <div class="phone-input">
                        <v-select
                          v-model="googlePayCountryCode"
                          :items="countryCodes"
                          item-title="displayText"
                          item-value="code"
                          outlined
                          dense
                          class="country-code-select"
                          label="Country Code"
                        ></v-select>
                        <v-text-field
                          v-model="googlePayPhone"
                          :label="`Phone Number (${googlePayDigitLength} digits)`"
                          type="tel"
                          required
                          outlined
                          :rules="phoneRules"
                        ></v-text-field>
                      </div>
                      <v-text-field v-model="bizumPin" label="Bizum PIN" type="password" required outlined></v-text-field>
                      <v-btn type="submit" color="primary" block>Pay with Bizum</v-btn>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'googlepay'" class="payment-method">
                    <h4>Google Pay</h4>
                    <form @submit.prevent="handlePurchase('googlepay')" class="payment-form">
                      <v-text-field v-model="googlePayEmail" label="Google Pay Email" type="email" required outlined :rules="[v => /.+@.+\..+/.test(v) || 'Email must contain @']"></v-text-field>
                      <div class="phone-input">
                        <v-select
                          v-model="googlePayCountryCode"
                          :items="countryCodes"
                          item-title="displayText"
                          item-value="code"
                          outlined
                          dense
                          class="country-code-select"
                          label="Country Code"
                        ></v-select>
                        <v-text-field
                          v-model="googlePayPhone"
                          :label="`Phone Number (${googlePayDigitLength} digits)`"
                          type="tel"
                          required
                          outlined
                          :rules="phoneRules"
                        ></v-text-field>
                      </div>
                      <v-btn type="submit" color="primary" block>Pay with Google Pay</v-btn>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'credits'" class="payment-method">
                    <h4>Pay with Credits</h4>
                    <div class="credits-info">
                      <p>Current Credits: <strong>{{ userCredits }}</strong></p>
                      <p>Cost: <strong>{{ item.amount }} Credits</strong></p>
                    </div>
                    <form @submit.prevent="handlePurchase" class="payment-form">
                      <v-btn type="submit" color="primary" block :disabled="userCredits < item.amount">
                        Pay with Credits
                      </v-btn>
                    </form>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>
    </div>
    <AppFooter />

    <!-- Success Dialog -->
    <v-dialog v-model="showSuccessDialog" max-width="500px">
      <v-card class="success-dialog">
        <v-card-title class="headline">
          <v-icon color="green" large left>mdi-check-circle</v-icon>
          Purchase Successful!
        </v-card-title>
        <v-card-text class="dialog-text">
          <p>Thank you for your purchase!</p>
          <p v-if="item && item.amount">
            You've successfully added <strong>{{ item.amount }} credits</strong> to your account using <strong>{{ selectedPaymentMethod }}</strong>.
          </p>
          <p v-else-if="item && String(item.price).includes('Credits')">
            You've successfully purchased <strong>{{ item.name || item.title }}</strong> using <strong>{{ selectedPaymentMethod }}</strong>.
          </p>
          <p>Your current credit balance is: <strong>{{ userCredits }} credits</strong>.</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="closeSuccessDialog">Continue Shopping</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Error Dialog -->
    <v-dialog v-model="showErrorDialog" max-width="500px">
      <v-card class="error-dialog">
        <v-card-title class="headline">
          <v-icon color="red" large left>mdi-alert-circle</v-icon>
          {{ errorTitle }}
        </v-card-title>
        <v-card-text class="dialog-text">
          <p>{{ errorMessage }}</p>
          <p v-if="errorMessage === 'Insufficient credits to complete this purchase.'">
            Please buy more credits in the shop to complete your purchase.
          </p>
          <p v-else-if="errorMessage === 'You already own this item'">
            This item is already in your inventory. No need to purchase it again!
          </p>
          <p v-else>
            Please try again or contact support at <a href="mailto:hivemindnexxuscontact@gmail.com">hivemindnexxuscontact@gmail.com</a>.
          </p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="closeErrorDialog">
            {{ errorMessage === 'Insufficient credits to complete this purchase.' ? 'Go to Shop' : 'OK' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue';
import AppFooter from '../components/AppFooter.vue';
import apiClient from '@/axios.js';

export default {
  name: 'PurchasePage',
  components: {
    NavBar,
    AppFooter,
  },
  data() {
    return {
      item: null,
      cardNumber: '',
      expiryDate: '',
      cvv: '',
      paypalEmail: '',
      paypalPassword: '',
      bizumPhone: '',
      bizumPin: '',
      bizumCountryCode: '+34',
      googlePayEmail: '',
      googlePayPhone: '',
      googlePayCountryCode: '+34',
      selectedPaymentMethod: null,
      userCredits: 1000,
      showSuccessDialog: false,
      showErrorDialog: false,
      errorMessage: '',
      errorTitle: 'Purchase Failed!', // Default title
      countryCodes: [
        { name: 'Spain', code: '+34', digits: 9, displayText: 'Spain (+34)' },
        { name: 'United States', code: '+1', digits: 10, displayText: 'United States (+1)' },
        { name: 'United Kingdom', code: '+44', digits: 10, displayText: 'United Kingdom (+44)' },
        { name: 'France', code: '+33', digits: 9, displayText: 'France (+33)' },
        { name: 'Germany', code: '+49', digits: 11, displayText: 'Germany (+49)' },
        { name: 'Italy', code: '+39', digits: 10, displayText: 'Italy (+39)' },
        { name: 'Brazil', code: '+55', digits: 11, displayText: 'Brazil (+55)' },
        { name: 'Australia', code: '+61', digits: 9, displayText: 'Australia (+61)' },
        { name: 'Canada', code: '+1', digits: 10, displayText: 'Canada (+1)' },
        { name: 'India', code: '+91', digits: 10, displayText: 'India (+91)' },
        { name: 'Japan', code: '+81', digits: 10, displayText: 'Japan (+81)' },
        { name: 'Mexico', code: '+52', digits: 10, displayText: 'Mexico (+52)' },
      ],
      user: { id: null },
    };
  },
  computed: {
    bizumDigitLength() {
      const country = this.countryCodes.find(c => c.code === this.bizumCountryCode);
      return country ? country.digits : 9;
    },
    googlePayDigitLength() {
      const country = this.countryCodes.find(c => c.code === this.googlePayCountryCode);
      return country ? country.digits : 9;
    },
    phoneRules() {
      return [
        v => !!v || 'Phone number is required',
        v => /^\d+$/.test(v) || 'Phone number must contain only digits',
        v => {
          const digits = this.selectedPaymentMethod === 'bizum'
            ? this.bizumDigitLength
            : this.googlePayDigitLength;
          return v.length === digits || `Phone number must be ${digits} digits`;
        },
      ];
    },
  },
  created() {
    const itemId = parseInt(this.$route.params.id, 10);
    if (itemId) {
      this.fetchItemById(itemId);
    }
    this.fetchCurrentUser();
  },
  methods: {
    async fetchItemById(id) {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');

        const response = await apiClient.get(`/api/shop/items/${id}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        });
        this.item = response.data;
      } catch (error) {
        console.error(`Failed to fetch item with ID ${id}:`, error);
        this.errorMessage = `Failed to fetch item: ${error.message}`;
        this.showErrorDialog = true;
      }
    },
    async fetchCurrentUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');

        const response = await apiClient.get('/api/user', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        });
        this.user = response.data;
        this.userCredits = response.data.credits || 1000;
      } catch (error) {
        console.error('Failed to fetch current user:', error);
        this.errorMessage = `Failed to fetch user data: ${error.message}`;
        this.showErrorDialog = true;
      }
    },
    selectPaymentMethod(method) {
      this.selectedPaymentMethod = this.selectedPaymentMethod === method ? null : method;
    },
    async processCreditPurchase() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');

        console.log('Sending payload:', { userId: this.user.id, itemId: this.item.id });
        const response = await apiClient.post(
          '/api/user/process-credit-purchase',
          { userId: this.user.id, itemId: this.item.id },
          { headers: { Authorization: `Bearer ${token}` } }
        );

        if (response.data.error) {
          throw new Error(response.data.error);
        }

        this.userCredits = response.data.credits;
        this.$emit('credits-updated', this.userCredits);
        console.log('Purchase successful with credits');
      } catch (error) {
        console.error('Raw error:', error);
        console.error('Response data:', error.response?.data);
        if (error.response && error.response.data && error.response.data.error) {
          this.errorTitle = error.response.data.error === 'You already own this item' ? 'Item Already Owned!' : 'Purchase Failed!';
          throw new Error(error.response.data.error);
        }
        this.errorTitle = 'Purchase Failed!';
        throw new Error(error.message || 'Failed to process credit purchase');
      }
    },
    async handlePurchase() {
      try {
        if (this.selectedPaymentMethod === 'credits') {
          if (this.userCredits < this.item.amount) {
            this.errorTitle = 'Not Enough Credits!';
            throw new Error('Insufficient credits to complete this purchase.');
          }
          await this.processCreditPurchase();
        } else {
          const itemPrice = parseFloat(this.item.price);
          if (!isNaN(itemPrice) && itemPrice > 0) {
            console.log(`Processing ${this.selectedPaymentMethod} payment - not implemented yet`);
            throw new Error('Non-credit payment methods are not yet implemented.');
          } else {
            throw new Error('Invalid item price');
          }
        }
        this.showSuccessDialog = true;
      } catch (error) {
        console.error('Purchase failed:', error);
        this.errorMessage = error.message;
        this.showErrorDialog = true;
      }
    },
    closeSuccessDialog() {
      this.showSuccessDialog = false;
      this.$router.push('/shop');
    },
    closeErrorDialog() {
      this.showErrorDialog = false;
      this.$router.push('/shop');
    },
  },
};
</script>

<!-- Styles remain unchanged -->
<style scoped>
.purchase-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
  padding-top: 60px;
  color: #1e293b;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.container {
  width: 100%;
  max-width: 900px;
  margin: 0 auto;
  padding: 0 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.purchase-content {
  padding: 3rem 1.5rem;
  flex-grow: 1;
}

.checkout-header {
  background: #ffffff;
  padding: 2rem 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 2.5rem;
  text-align: center;
  width: 100%;
}

.checkout-header .title {
  font-size: 2.25rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.75rem;
}

.checkout-header .description {
  font-size: 1rem;
  color: #64748b;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
}

.checkout-header .description a {
  color: #2563eb;
  text-decoration: none;
  transition: color 0.3s;
}

.checkout-header .description a:hover {
  color: #1d4ed8;
}

.checkout-container {
  background: #ffffff;
  padding: 2.5rem 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  max-width: 900px;
  margin: 0 auto;
  width: 100%;
}

.checkout-inner {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  width: 100%;
}

.item-details-container {
  padding: 1.5rem 2rem;
  border-radius: 10px;
  background: #f8fafc;
  width: 100%;
}

.item-details {
  text-align: center;
  color: #1e293b;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.item-icon {
  width: 120px;
  height: 120px;
  margin-bottom: 1.5rem;
  object-fit: contain;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.item-details h2 {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.item-details .price {
  font-size: 1.25rem;
  font-weight: 500;
  color: #2563eb;
  margin-bottom: 1rem;
}

.item-details .description {
  font-size: 1rem;
  color: #64748b;
  margin-bottom: 1rem;
}

.item-details .features {
  list-style: none;
  padding: 0;
  margin-bottom: 1rem;
}

.item-details .features li {
  font-size: 0.95rem;
  color: #475569;
  margin-bottom: 0.5rem;
  position: relative;
  padding-left: 1.5rem;
}

.item-details .features li:before {
  content: '✓';
  color: #22c55e;
  position: absolute;
  left: 0;
}

.item-details .amount {
  font-size: 1.1rem;
  color: #475569;
}

.no-item p {
  font-size: 1.1rem;
  color: #64748b;
  text-align: center;
}

.payment-container {
  padding: 1.5rem 2rem;
  border-radius: 10px;
  background: #f8fafc;
  width: 100%;
}

.payment-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1.5rem;
  text-align: center;
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  width: 100%;
}

.payment-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1.5rem;
  justify-items: center;
  width: 100%;
}

.payment-btn {
  width: 100%;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  color: #1e293b;
  border: 2px solid #e2e8f0;
  background: #ffffff;
  transition: all 0.3s ease;
}

.payment-btn:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.payment-btn.active {
  background: #2563eb;
  color: #ffffff;
  border-color: #2563eb;
}

.payment-form-container {
  margin-top: 1rem;
  width: 100%;
}

.payment-method h4 {
  font-size: 1.25rem;
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 1rem;
}

.payment-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
}

.credits-info {
  margin-bottom: 1rem;
  text-align: center;
}

.credits-info p {
  font-size: 1rem;
  color: #475569;
  margin-bottom: 0.5rem;
}

.credits-info strong {
  color: #2563eb;
}

.phone-input {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.country-code-select {
  flex: 1;
  min-width: 100px;
  max-width: 250px;
}

.v-text-field {
  border-radius: 6px;
}

.v-btn[color="primary"] {
  background: #2563eb;
  color: #ffffff;
  padding: 0.75rem;
  font-weight: 500;
  border-radius: 8px;
  transition: background 0.3s ease;
}

.v-btn[color="primary"]:hover {
  background: #1d4ed8;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Success Dialog Styles */
.success-dialog {
  background: #000000;
  padding: 1.5rem;
  border-radius: 12px;
}

.headline {
  display: flex;
  align-items: center;
  font-size: 1.5rem;
  font-weight: 600;
  color: #ffffff;
  padding-bottom: 1rem;
}

.dialog-text {
  font-size: 1rem;
  color: #ffffff;
  line-height: 1.6;
}

.dialog-text p {
  margin-bottom: 1rem;
}

.dialog-text strong {
  color: #0000ff;
}

.dialog-text a {
  color: #2563eb;
  text-decoration: none;
}

.dialog-text a:hover {
  color: #1d4ed8;
}

.v-card-actions {
  padding-top: 1rem;
}

/* Error Dialog Styles */
.error-dialog {
  background: #000000;
  padding: 1.5rem;
  border-radius: 12px;
}

@media (max-width: 768px) {
  .purchase-content {
    padding: 2rem 1rem;
  }

  .checkout-header {
    padding: 1.5rem 2rem;
  }

  .checkout-container {
    padding: 2rem 2rem;
  }

  .checkout-header .title {
    font-size: 1.75rem;
  }

  .item-icon {
    width: 80px;
    height: 80px;
  }

  .item-details h2 {
    font-size: 1.5rem;
  }

  .payment-buttons {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .phone-input {
    flex-direction: column;
    gap: 0.5rem;
  }

  .country-code-select {
    width: 100%;
  }
}
</style>
