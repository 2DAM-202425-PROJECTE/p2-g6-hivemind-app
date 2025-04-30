<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 text-gray-800 p-32">
    <NavBar />
    <div class="max-w-7xl mx-auto px-6 py-12 animate-fade-in">
      <!-- Checkout Header -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-12 text-center">
        <h1 class="text-4xl font-extrabold mb-4 gradient-text">
          Checkout
        </h1>
        <p class="text-lg text-amber-800">
          All purchases are final. No refunds will be issued once payment is processed. Please review your order carefully before completing the transaction. For support, contact us at <a href="mailto:hivemindnexxuscontact@gmail.com" class="text-amber-600 hover:text-amber-700">hivemindnexxuscontact@gmail.com</a> or at the contact page in the footer.
        </p>
      </div>

      <!-- Checkout Body -->
      <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Item Details -->
          <div v-if="item" class="bg-amber-50 rounded-lg p-6">
            <div class="text-center">
              <div
                class="item-preview rounded-md overflow-hidden mb-4 mx-auto"
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
                  :alt="item.name || 'Product Image'"
                  class="w-full h-full object-cover"
                  @error="handleImageError($event, item)"
                />
              </div>
              <h2
                class="text-2xl font-bold text-amber-900 mb-2"
                :class="[
                  item.type === 'name_effect' ? getNameEffectClass(item.name) : '',
                  item.type === 'profile_font' ? getProfileFontClass(item.name) : '',
                  item.type === 'name_effect' ? 'effect-active' : ''
                ]"
              >
                {{ item.name || item.title || 'Unnamed Product' }}
              </h2>
              <p v-if="categoryInfo" class="text-sm font-medium text-amber-700 mb-3">
                {{ categoryInfo.title }} ({{ getOwnedCount(categoryInfo.items) }}/{{ categoryInfo.items.length }} owned)
              </p>
              <p class="text-lg font-medium text-amber-700 mb-3">{{ formatPrice(item.price) }}</p>
              <p v-if="item.description" class="text-amber-800 mb-3">{{ item.description }}</p>
              <ul v-if="item.features" class="text-amber-800 mb-3 text-left">
                <li v-for="(feature, index) in item.features" :key="index" class="flex items-center">
                  <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ feature }}
                </li>
              </ul>
              <p v-if="item.amount" class="text-amber-800">{{ item.amount }} Credits</p>
            </div>
          </div>
          <div v-else class="bg-amber-50 rounded-lg p-6 text-center">
            <p class="text-amber-800">No item selected or item could not be loaded.</p>
          </div>

          <!-- Payment Section -->
          <div v-if="item" class="bg-amber-50 rounded-lg p-6">
            <h3 class="text-xl font-bold text-amber-900 mb-4 text-center">Choose Payment Method</h3>
            <div class="space-y-6">
              <!-- Payment Buttons -->
              <div class="grid grid-cols-2 gap-4">
                <button
                  @click="selectPaymentMethod('card')"
                  :class="{ 'btn-primary': selectedPaymentMethod !== 'card', 'btn-primary active': selectedPaymentMethod === 'card' }"
                  class="w-full"
                >
                  Credit/Debit Card
                </button>
                <button
                  @click="selectPaymentMethod('paypal')"
                  :class="{ 'btn-primary': selectedPaymentMethod !== 'paypal', 'btn-primary active': selectedPaymentMethod === 'paypal' }"
                  class="w-full"
                >
                  PayPal
                </button>
                <button
                  @click="selectPaymentMethod('bizum')"
                  :class="{ 'btn-primary': selectedPaymentMethod !== 'bizum', 'btn-primary active': selectedPaymentMethod === 'bizum' }"
                  class="w-full"
                >
                  Bizum
                </button>
                <button
                  @click="selectPaymentMethod('googlepay')"
                  :class="{ 'btn-primary': selectedPaymentMethod !== 'googlepay', 'btn-primary active': selectedPaymentMethod === 'googlepay' }"
                  class="w-full"
                >
                  Google Pay
                </button>
                <button
                  v-if="item.type !== 'credit_pack'"
                  @click="selectPaymentMethod('credits')"
                  :class="{ 'btn-primary': selectedPaymentMethod !== 'credits', 'btn-primary active': selectedPaymentMethod === 'credits' }"
                  class="w-full col-span-2"
                >
                  Pay with Credits
                </button>
              </div>

              <!-- Payment Forms -->
              <transition name="fade">
                <div v-if="selectedPaymentMethod" class="mt-4">
                  <div v-if="selectedPaymentMethod === 'card'" class="payment-method">
                    <h4 class="text-lg font-medium text-amber-900 mb-3">Credit/Debit Card</h4>
                    <form @submit.prevent="handlePurchase('card')" class="space-y-3">
                      <v-text-field v-model="cardNumber" label="Card Number (16 digits)" required outlined :rules="[v => /^\d{16}$/.test(v) || 'Must be 16 digits']" class="rounded-md"></v-text-field>
                      <v-text-field v-model="expiryDate" label="Expiry Date (MM/YY)" required outlined :rules="[v => /^\d{2}\/\d{2}$/.test(v) || 'Format: MM/YY']" class="rounded-md"></v-text-field>
                      <v-text-field v-model="cvv" label="CVV (3 digits)" required outlined :rules="[v => /^\d{3}$/.test(v) || 'Must be 3 digits']" class="rounded-md"></v-text-field>
                      <button type="submit" class="btn-primary w-full">Pay with Card</button>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'paypal'" class="payment-method">
                    <h4 class="text-lg font-medium text-amber-900 mb-3">PayPal</h4>
                    <form @submit.prevent="handlePurchase('paypal')" class="space-y-3">
                      <v-text-field v-model="paypalEmail" label="PayPal Email" type="email" required outlined :rules="[v => /.+@.+\..+/.test(v) || 'Email must contain @']" class="rounded-md"></v-text-field>
                      <v-text-field v-model="paypalPassword" label="PayPal Password" type="password" required outlined class="rounded-md"></v-text-field>
                      <button type="submit" class="btn-primary w-full">Pay with PayPal</button>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'bizum'" class="payment-method">
                    <h4 class="text-lg font-medium text-amber-900 mb-3">Bizum</h4>
                    <form @submit.prevent="handlePurchase('bizum')" class="space-y-3">
                      <div class="flex gap-3">
                        <v-select
                          v-model="bizumCountryCode"
                          :items="countryCodes"
                          item-title="displayText"
                          item-value="code"
                          outlined
                          dense
                          class="country-code-select rounded-md"
                          label="Country Code"
                        ></v-select>
                        <v-text-field
                          v-model="bizumPhone"
                          :label="`Phone Number (${bizumDigitLength} digits)`"
                          type="tel"
                          required
                          outlined
                          :rules="phoneRules"
                          class="rounded-md"
                        ></v-text-field>
                      </div>
                      <v-text-field v-model="bizumPin" label="Bizum PIN" type="password" required outlined class="rounded-md"></v-text-field>
                      <button type="submit" class="btn-primary w-full">Pay with Bizum</button>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'googlepay'" class="payment-method">
                    <h4 class="text-lg font-medium text-amber-900 mb-3">Google Pay</h4>
                    <form @submit.prevent="handlePurchase('googlepay')" class="space-y-3">
                      <v-text-field v-model="googlePayEmail" label="Google Pay Email" type="email" required outlined :rules="[v => /.+@.+\..+/.test(v) || 'Email must contain @']" class="rounded-md"></v-text-field>
                      <div class="flex gap-3">
                        <v-select
                          v-model="googlePayCountryCode"
                          :items="countryCodes"
                          item-title="displayText"
                          item-value="code"
                          outlined
                          dense
                          class="country-code-select rounded-md"
                          label="Country Code"
                        ></v-select>
                        <v-text-field
                          v-model="googlePayPhone"
                          :label="`Phone Number (${googlePayDigitLength} digits)`"
                          type="tel"
                          required
                          outlined
                          :rules="phoneRules"
                          class="rounded-md"
                        ></v-text-field>
                      </div>
                      <button type="submit" class="btn-primary w-full">Pay with Google Pay</button>
                    </form>
                  </div>

                  <div v-if="selectedPaymentMethod === 'credits'" class="payment-method">
                    <h4 class="text-lg font-medium text-amber-900 mb-3">Pay with Credits</h4>
                    <div class="text-center mb-3">
                      <p class="text-amber-800">Current Credits: <strong class="text-amber-900">{{ userCredits }}</strong></p>
                      <p class="text-amber-800">Cost: <strong class="text-amber-900">{{ item.amount || formatPrice(item.price) }}</strong></p>
                    </div>
                    <form @submit.prevent="handlePurchase('credits')">
                      <button
                        type="submit"
                        class="btn-primary w-full"
                        :disabled="userCredits < (item.price || 0) || item.type === 'credit_pack'"
                      >
                        Pay with Credits
                      </button>
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
    <v-dialog v-model="showSuccessDialog" max-width="500">
      <v-card class="bg-white rounded-xl p-6">
        <v-card-title class="text-2xl font-bold text-amber-900 flex items-center">
          <svg class="h-8 w-8 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Purchase Successful!
        </v-card-title>
        <v-card-text class="text-amber-800">
          <p class="mb-3">Thank you for your purchase!</p>
          <p v-if="item && item.amount" class="mb-3">
            You've successfully added <strong class="text-amber-900">{{ item.amount }} credits</strong> to your account using <strong class="text-amber-900">{{ selectedPaymentMethod }}</strong>.
          </p>
          <p v-else class="mb-3">
            You've successfully purchased <strong class="text-amber-900">{{ item.name || item.title }}</strong> using <strong class="text-amber-900">{{ selectedPaymentMethod }}</strong>.
          </p>
          <p>Your current credit balance is: <strong class="text-amber-900">{{ userCredits }} credits</strong>.</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <button class="btn-primary" @click="closeSuccessDialog">Continue Shopping</button>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Error Dialog -->
    <v-dialog v-model="showErrorDialog" max-width="500">
      <v-card class="bg-white rounded-xl p-6">
        <v-card-title class="text-2xl font-bold text-amber-900 flex items-center">
          <svg class="h-8 w-8 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ errorTitle }}
        </v-card-title>
        <v-card-text class="text-amber-800">
          <p class="mb-3">{{ errorMessage }}</p>
          <p v-if="errorMessage === 'Insufficient credits to complete this purchase.'" class="mb-3">
            Please buy more credits in the shop to complete your purchase.
          </p>
          <p v-else-if="errorMessage === 'You already own this item'" class="mb-3">
            This item is already in your inventory. No need to purchase it again!
          </p>
          <p v-else class="mb-3">
            Please try again or contact support at <a href="mailto:hivemindnexxuscontact@gmail.com" class="text-amber-600 hover:text-amber-700">hivemindnexxuscontact@gmail.com</a>.
          </p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <button class="btn-primary" @click="closeErrorDialog">
            {{ errorMessage === 'Insufficient credits to complete this purchase.' ? 'Go to Shop' : 'OK' }}
          </button>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue';
import AppFooter from '@/components/AppFooter.vue';
import apiClient from '@/axios.js';
import { getNameEffectClass } from '@/utils/nameEffects';

export default {
  name: 'PurchasePage',
  components: {
    NavBar,
    AppFooter,
  },
  data() {
    return {
      item: null,
      categoryInfo: null,
      userInventory: [],
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
      errorTitle: 'Purchase Failed!',
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
      fallbackImage: 'https://api.iconify.design/lucide/image-off.svg',
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
    this.fetchCategorizedItems();
  },
  methods: {
    getNameEffectClass,
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
    formatPrice(price) {
      if (typeof price === 'string') return price;
      if (this.item && this.item.type === 'credit_pack') {
        return `${price}€`;
      }
      return price === 0 ? 'Free' : `${price} Credits`;
    },
    async fetchItemById(id) {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');
        const response = await apiClient.get(`/api/shop/items/${id}`);
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
        const response = await apiClient.get('/api/user');
        this.user = response.data;
        this.userCredits = response.data.credits || 1000;
        await this.fetchUserInventory();
      } catch (error) {
        console.error('Failed to fetch current user:', error);
        this.errorMessage = `Failed to fetch user data: ${error.message}`;
        this.showErrorDialog = true;
      }
    },
    async fetchUserInventory() {
      try {
        const response = await apiClient.get(`/api/user/${this.user.id}/inventory`);
        this.userInventory = response.data.map(item => item.item_id);
      } catch (error) {
        console.error('Failed to fetch user inventory:', error);
      }
    },
    async fetchCategorizedItems() {
      try {
        const response = await apiClient.get('/api/shop/categorized-items');
        const cosmeticCategories = this.categorizeCosmetics(response.data.cosmetics);
        if (this.item && this.item.type) {
          this.categoryInfo = cosmeticCategories.find(category =>
            category.items.some(item => item.type === this.item.type)
          );
        }
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
    selectPaymentMethod(method) {
      this.selectedPaymentMethod = this.selectedPaymentMethod === method ? null : method;
    },
    async processCreditPurchase() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');
        const response = await apiClient.post('/api/user/process-credit-purchase', {
          userId: this.user.id,
          itemId: this.item.id,
        });
        if (response.data.error) {
          throw new Error(response.data.error);
        }
        this.userCredits = response.data.credits;
        this.$emit('credits-updated', this.userCredits);
        console.log('Purchase successful with credits');
      } catch (error) {
        console.error('Raw error:', error);
        console.error('Response data:', error.response?.data);
        throw new Error(error.response?.data?.error || error.message || 'Failed to process credit purchase');
      }
    },
    async processRealMoneyPurchase() {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');
        const response = await apiClient.post('/api/user/process-real-money-purchase', {
          userId: this.user.id,
          itemId: this.item.id,
        });
        if (response.data.error) {
          throw new Error(response.data.error);
        }
        this.userCredits = response.data.credits;
        this.$emit('credits-updated', this.userCredits);
        console.log('Real-money purchase successful');
      } catch (error) {
        console.error('Raw error:', error);
        console.error('Response data:', error.response?.data);
        throw new Error(error.response?.data?.error || error.message || 'Failed to process real-money purchase');
      }
    },
    async updateUserCredits(amount) {
      try {
        const token = localStorage.getItem('token');
        if (!token) throw new Error('No access token found. Please log in.');
        const response = await apiClient.post('/api/user/update-credits', {
          userId: this.user.id,
          amount: amount,
        });
        if (response.data.error) {
          throw new Error(response.data.error);
        }
        this.userCredits = response.data.credits;
        this.$emit('credits-updated', this.userCredits);
        console.log('User credits updated:', this.userCredits);
      } catch (error) {
        console.error('Failed to update user credits:', error);
        throw new Error(error.message || 'Failed to update user credits');
      }
    },
    validatePaymentInputs() {
      if (this.selectedPaymentMethod === 'card') {
        return (
          /^\d{16}$/.test(this.cardNumber) &&
          /^\d{2}\/\d{2}$/.test(this.expiryDate) &&
          /^\d{3}$/.test(this.cvv)
        );
      }
      if (this.selectedPaymentMethod === 'paypal') {
        return /.+@.+\..+/.test(this.paypalEmail) && this.paypalPassword.length > 0;
      }
      if (this.selectedPaymentMethod === 'bizum') {
        const digits = this.bizumDigitLength;
        return /^\d+$/.test(this.bizumPhone) && this.bizumPhone.length === digits && this.bizumPin.length > 0;
      }
      if (this.selectedPaymentMethod === 'googlepay') {
        const digits = this.googlePayDigitLength;
        return /.+@.+\..+/.test(this.googlePayEmail) && /^\d+$/.test(this.googlePayPhone) && this.googlePayPhone.length === digits;
      }
      return true;
    },
    async handlePurchase(method) {
      try {
        if (this.selectedPaymentMethod === 'credits') {
          if (this.item.type !== 'credit_pack' && this.userCredits < this.item.price) {
            this.errorTitle = 'Not Enough Credits!';
            this.errorMessage = 'Insufficient credits to complete this purchase.';
            this.showErrorDialog = true;
            return;
          }
          await this.processCreditPurchase();
        } else {
          if (!this.validatePaymentInputs()) {
            this.errorTitle = 'Invalid Input';
            this.errorMessage = 'Please fill in all required payment fields correctly.';
            this.showErrorDialog = true;
            return;
          }
          console.log(`Simulating ${method || this.selectedPaymentMethod} payment for ${this.item.price}€`);
          await this.processRealMoneyPurchase();
        }
        this.showSuccessDialog = true;
      } catch (error) {
        console.error('Purchase failed:', error);
        this.errorTitle = error.response?.data?.error === 'You already own this item' ? 'Item Already Owned!' : 'Purchase Failed!';
        this.errorMessage = error.message || 'An error occurred during the purchase.';
        this.showErrorDialog = true;
      }
    },
    closeSuccessDialog() {
      this.showSuccessDialog = false;
      this.$router.push('/shop');
    },
    closeErrorDialog() {
      this.showErrorDialog = false;
      if (this.errorMessage === 'Insufficient credits to complete this purchase.') {
        this.$router.push('/shop#buy-credits');
      } else {
        this.$router.push('/shop');
      }
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

/* Animations from ShopPage */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.gradient-text {
  background-image: linear-gradient(to right, #F59E0B, #D97706);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

/* Button styles from ShopPage */
.btn-primary {
  @apply px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center;
  @apply bg-gradient-to-r from-amber-500 to-amber-400 text-amber-900;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  @apply bg-gradient-to-r from-amber-600 to-amber-500 transform -translate-y-0.5;
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.btn-primary.active {
  @apply bg-gradient-to-r from-amber-600 to-amber-500;
  box-shadow: 0 6px 8px -2px rgba(245, 158, 11, 0.4);
}

.btn-primary:disabled {
  @apply bg-gray-400 cursor-not-allowed transform-none;
  box-shadow: none;
}

/* Item preview styles from ShopPage */
.background-preview, .banner-preview {
  background-color: #f3e8d3;
  position: relative;
  height: 128px;
  width: 128px;
}

.name-effect-preview, .profile-icon-preview, .profile-font-preview, .badge-preview {
  background-color: #f3e8d3;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 96px;
  width: 96px;
}

.badge-preview img {
  object-fit: contain;
  padding: 8px;
}

.profile-font-preview img, .name-effect-preview img {
  object-fit: contain;
  padding: 4px;
}

.effect-active {
  display: inline-block;
  padding: 0 0.25rem;
}

/* Payment form styles */
.v-text-field, .v-select {
  border-radius: 6px;
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

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Profile Font Styles from ShopPage */
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

@media (max-width: 768px) {
  .min-h-screen {
    padding: 1rem;
  }

  .max-w-7xl {
    padding: 0.5rem;
  }

  .grid-cols-2 {
    grid-template-columns: 1fr;
  }

  .background-preview, .banner-preview {
    height: 96px;
    width: 96px;
  }

  .name-effect-preview, .profile-icon-preview, .profile-font-preview, .badge-preview {
    height: 64px;
    width: 64px;
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
