<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden max-w-5xl mx-auto mb-5">
    <!-- Banner -->
    <div class="relative w-full h-48 bg-gray-200 dark:bg-gray-700">
      <img
        v-if="user.banner_photo_path"
        :src="user.banner_photo_path"
        alt="Profile Banner"
        class="w-full h-full object-cover"
      />
      <div
        v-else
        class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400"
      >
        No banner set
      </div>
    </div>

    <!-- Content -->
    <div class="relative px-6 pb-6">
      <!-- Profile photo with equipped icon -->
      <div class="absolute -top-16 left-6 flex flex-col items-center">
        <!-- Equipped Profile Icon (e.g., Crown) -->
        <img
          v-if="user.equipped_profile_icon_path"
          :src="user.equipped_profile_icon_path"
          alt="Equipped Profile Icon"
          class="equipped-icon"
        />
        <!-- Profile Photo -->
        <img
          :src="user.profile_photo_url"
          alt="Profile Pic"
          class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover"
        />
      </div>

      <!-- Info and buttons -->
      <div class="pt-20 flex flex-col md:flex-row md:items-start">
        <!-- User info -->
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h3>
          <div class="text-sm text-gray-500 dark:text-gray-400">@{{ user.username }}</div>
          <div v-if="user.description" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            {{ user.description }}
          </div>
          <div v-else-if="isCurrentUser" class="mt-2 text-sm text-gray-400 dark:text-gray-500 italic">
            What would you like others to know about you? Add a description to your profile
          </div>
          <div class="flex gap-6 mt-3 text-sm text-gray-600 dark:text-gray-400">
            <span>{{ user.posts?.length || 0 }} posts</span>
            <span>{{ user.followers || 0 }} followers</span>
            <span>{{ user.following || 0 }} following</span>
          </div>
        </div>

        <!-- Buttons -->
        <div class="absolute right-6 top-4 flex flex-col space-y-2">
          <button
            v-if="isCurrentUser"
            @click="editProfile"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition duration-200 transform hover:scale-110"
            title="Edit Profile"
          >
            <i class="fas fa-edit"></i>
          </button>
          <button
            v-if="isCurrentUser"
            @click="showInventory = true"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-purple-500 dark:hover:text-purple-400 transition duration-200 transform hover:scale-110"
            title="Inventory"
          >
            <i class="fas fa-box"></i>
          </button>
          <button
            v-if="isCurrentUser"
            @click="connectSocial"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition duration-200 transform hover:scale-110"
            title="Connect Social Accounts"
          >
            <i class="fas fa-link"></i>
          </button>
          <button
            @click="shareProfile"
            class="p-2 text-gray-600 dark:text-gray-300 hover:text-green-500 dark:hover:text-green-400 transition duration-200 transform hover:scale-110"
            title="Share Profile"
          >
            <i class="fas fa-share-alt"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Inventory Modal -->
    <div
      v-if="showInventory"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click="closeInventoryOnBackdrop"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-3xl max-h-[80vh] overflow-y-auto"
        @click.stop
      >
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">Inventory</h3>
          <button @click="showInventory = false" class="text-gray-600 dark:text-gray-300 hover:text-red-500">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div v-if="inventoryCategories.length > 0" class="cosmetics-grid">
          <div v-for="category in inventoryCategories" :key="category.title" class="category-card">
            <h3 class="category-title">{{ category.title }}</h3>
            <p class="category-description">{{ category.description }}</p>
            <div class="items-grid">
              <div v-for="item in category.items" :key="item.id" class="cosmetic-item">
                <img :src="item.icon_url" :alt="item.name" class="cosmetic-icon" />
                <h4 class="item-name">{{ item.name }}</h4>
                <p class="item-price">{{ formatPrice(item.price) }}</p>
                <button
                  v-if="!item.isEquipped"
                  @click="equipItem(item)"
                  class="buy-button"
                >
                  Equip
                </button>
                <button
                  v-else
                  @click="unequipItem(item)"
                  class="buy-button unequip-button"
                >
                  Unequip
                </button>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-500">
          No items in inventory
        </div>
      </div>
    </div>

    <!-- Equip Confirmation Popup -->
    <div
      v-if="showEquipPopup"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click="closeEquipPopupOnBackdrop"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-4"
        @click.stop
      >
        <p class="text-gray-900 dark:text-white">{{ equippedItemName }}</p>
        <button
          @click="showEquipPopup = false"
          class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          OK
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios.js';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  isCurrentUser: {
    type: Boolean,
    required: true,
  },
  editProfile: {
    type: Function,
    required: true,
  },
  connectSocial: {
    type: Function,
    default: () => console.log('Connect Social clicked'),
  },
  shareProfile: {
    type: Function,
    required: true,
  },
});

// Reactive state
const showInventory = ref(false);
const showEquipPopup = ref(false);
const equippedItemName = ref('');
const inventory = ref([]);
const inventoryCategories = ref([]);

// Close inventory modal when clicking on the backdrop
const closeInventoryOnBackdrop = (event) => {
  if (event.target === event.currentTarget) {
    showInventory.value = false;
  }
};

// Close equip confirmation popup when clicking on the backdrop
const closeEquipPopupOnBackdrop = (event) => {
  if (event.target === event.currentTarget) {
    showEquipPopup.value = false;
  }
};

// Equip item function
const equipItem = async (item) => {
  equippedItemName.value = `${item.name} has been equipped`;
  showEquipPopup.value = true;

  if (item.type === 'profile_icon') {
    try {
      const response = await apiClient.post('/api/user/update-equipped-profile-icon', {
        userId: props.user.id,
        equippedProfileIconPath: item.icon_url,
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      // Update the local user equipped profile icon path
      props.user.equipped_profile_icon_path = item.icon_url;
      item.isEquipped = true; // Mark this item as equipped
      // Unequip any other items of the same type
      inventory.value.forEach(i => {
        if (i.type === item.type && i.id !== item.id) {
          i.isEquipped = false;
        }
      });
      console.log('Profile icon equipped successfully:', response.data);
    } catch (error) {
      console.error('Failed to equip profile icon:', error);
    }
  }
};

// Unequip item function
const unequipItem = async (item) => {
  if (item.type === 'profile_icon') {
    try {
      const response = await apiClient.post('/api/user/update-equipped-profile-icon', {
        userId: props.user.id,
        equippedProfileIconPath: null,
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      props.user.equipped_profile_icon_path = null;
      item.isEquipped = false;
      equippedItemName.value = `${item.name} has been unequipped`;
      showEquipPopup.value = true;
      console.log('Profile icon unequipped successfully:', response.data);
    } catch (error) {
      console.error('Failed to unequip profile icon:', error);
    }
  }
};

// Format price to match shop page
const formatPrice = (price) => {
  if (typeof price === 'string') return price; // Already formatted
  return price === 0 ? 'Free' : `${price} Credits`;
};

// Categorize inventory items like shop page
const categorizeInventory = (items) => {
  return [
    {
      title: 'Profile Icons',
      description: 'Stand out with unique profile icons that showcase your personality',
      items: items.filter(item => item.name.includes('Icon')),
    },
    {
      title: 'Backgrounds',
      description: 'Transform your profile with stunning background themes',
      items: items.filter(item =>
        ['Galaxy', 'Night City', 'Mountain', 'Blossom', 'Ocean', 'Forest', 'Desert', 'Space'].includes(item.name)
      ),
    },
    {
      title: 'Animations',
      description: 'Add dynamic effects to make your profile come alive',
      items: items.filter(item =>
        ['Sparkle', 'Wind', 'Fire', 'Wave', 'Rainbow', 'Glitch', 'Pulse', 'Orbit'].includes(item.name)
      ),
    },
    {
      title: 'Emojis',
      description: 'Express yourself with premium animated emojis',
      items: items.filter(item => item.name.includes('Emoji')),
    },
    {
      title: 'Name Effects',
      description: 'Make your username stand out with eye-catching effects',
      items: items.filter(item =>
        ['Gradient', 'Neon', 'Gold', 'Rainbow', 'Glitter', 'Shadow', 'Pixel', 'Cosmic'].includes(item.name)
      ),
    },
    {
      title: 'Profile Frames',
      description: 'Frame your profile picture with stunning borders',
      items: items.filter(item => item.name.includes('Frame')),
    },
  ].filter(category => category.items.length > 0); // Only include categories with items
};

// Load inventory from API and categorize
const loadInventory = async () => {
  try {
    const response = await apiClient.get(`/api/user/${props.user.id}/inventory`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });
    console.log('API Response:', response.data); // Log the API response
    const mappedInventory = response.data.map(item => ({
      id: item.id,
      name: item.item.name,
      price: item.item.price,
      icon_url: item.item.iconUrl,
      type: item.item.type || (item.item.name.includes('Icon') ? 'profile_icon' : 'other'),
      isEquipped: item.item.iconUrl === props.user.equipped_profile_icon_path
    }));
    inventory.value = mappedInventory;
    inventoryCategories.value = categorizeInventory(mappedInventory);
    console.log('Mapped Inventory:', inventory.value);
    console.log('Categorized Inventory:', inventoryCategories.value);
  } catch (error) {
    console.error('Failed to load inventory:', error);
  }
};

// Load equipped state when the profile is loaded
const loadEquippedState = async () => {
  try {
    const response = await apiClient.get(`/api/user/${props.user.id}/equipped-items`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });
    console.log('Equipped Items Response:', response.data);
    // Assuming the API returns an object with equipped items
    if (response.data.equipped_profile_icon_path) {
      props.user.equipped_profile_icon_path = response.data.equipped_profile_icon_path;
    }
  } catch (error) {
    console.error('Failed to load equipped state:', error);
  }
};

// Lifecycle hooks
onMounted(() => {
  console.log('isCurrentUser in ProfileHeader:', props.isCurrentUser);
  loadEquippedState(); // Load equipped state first
  loadInventory(); // Then load inventory to compare equipped state
});
</script>

<style scoped>
.equipped-icon {
  width: 2rem;
  height: 2rem;
  position: absolute;
  top: -1.5rem;
  left: 50%;
  transform: translateX(-50%);
}

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

.category-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #000;
  margin-bottom: 0.5rem;
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

.cosmetic-item {
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  text-align: center;
  transition: background-color 0.3s;
}

.cosmetic-item:hover {
  background: #e9ecef;
}

.cosmetic-icon {
  width: 2rem;
  height: 2rem;
  margin-bottom: 0.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.item-name {
  font-size: 1rem;
  font-weight: 500;
  color: #000;
  margin-bottom: 0.25rem;
}

.item-price {
  font-size: 0.9rem;
  color: #000;
  margin-bottom: 0.5rem;
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

.unequip-button {
  background-color: #dc3545;
}

.unequip-button:hover {
  background-color: #c82333;
}
</style>
