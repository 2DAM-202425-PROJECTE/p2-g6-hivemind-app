<template>
  <div
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    @click="closeInventoryOnBackdrop"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-5xl max-h-[90vh]"
      @click.stop
    >
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Inventory</h3>
        <button @click="emit('close')" class="text-gray-600 dark:text-gray-300 hover:text-red-500">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div v-if="inventoryCategories.length > 0" class="cosmetics-grid">
        <div v-for="category in inventoryCategories" :key="category.title" class="category-card">
          <h3 class="category-title">{{ category.title }}</h3>
          <p class="category-description">{{ category.description }}</p>
          <div class="items-grid">
            <div v-for="item in category.items" :key="item.id" class="cosmetic-item" :class="{ 'background-item': item.isBackground }">
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

    <div
      v-if="showEquipPopup"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click="closeEquipPopupOnBackdrop"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-4" @click.stop>
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
  user: { type: Object, required: true },
});

const emit = defineEmits(['close', 'update-user']);

const showEquipPopup = ref(false);
const equippedItemName = ref('');
const inventory = ref([]);
const inventoryCategories = ref([]);

const closeInventoryOnBackdrop = (event) => {
  if (event.target === event.currentTarget) emit('close');
};

const closeEquipPopupOnBackdrop = (event) => {
  if (event.target === event.currentTarget) showEquipPopup.value = false;
};

const equipItem = async (item) => {
  equippedItemName.value = `${item.name} has been equipped`;
  showEquipPopup.value = true;

  const updateEquipped = async (endpoint, field, path) => {
    try {
      const token = localStorage.getItem('token');
      if (!token) throw new Error('No authentication token found.');
      const response = await apiClient.post(endpoint, {
        userId: props.user.id,
        [field]: path,
      }, {
        headers: { Authorization: `Bearer ${token}` },
      });
      const updatedFields = { [field]: path };
      emit('update-user', updatedFields);
      item.isEquipped = true;
      inventory.value.forEach(i => {
        if (i.type === item.type && i.id !== item.id) i.isEquipped = false;
      });
      inventoryCategories.value = categorizeInventory(inventory.value);
    } catch (error) {
      console.error(`Failed to equip ${field}:`, error.response?.data || error.message);
    }
  };

  switch (item.type) {
    case 'profile_icon':
      await updateEquipped('/api/user/update-equipped-profile-icon', 'equipped_profile_icon_path', item.icon_url);
      break;
    case 'profile_frame':
      await updateEquipped('/api/user/update-equipped-profile-frame', 'equipped_profile_frame_path', item.icon_url);
      break;
    case 'background':
      await updateEquipped('/api/user/update-equipped-background', 'equipped_background_path', item.icon_url);
      break;
    case 'animation':
      await updateEquipped('/api/user/update-equipped-animation', 'equipped_animation_path', item.icon_url);
      break;
    case 'name_effect':
      await updateEquipped('/api/user/update-equipped-name-effect', 'equipped_name_effect_path', item.icon_url);
      break;
    case 'badge':
      await updateEquipped('/api/user/update-equipped-badge', 'equipped_badge_path', item.icon_url);
      break;
  }
};

const unequipItem = async (item) => {
  const updateUnequipped = async (endpoint, field) => {
    try {
      const token = localStorage.getItem('token');
      if (!token) throw new Error('No authentication token found.');
      const response = await apiClient.post(endpoint, {
        userId: props.user.id,
        [field]: null,
      }, {
        headers: { Authorization: `Bearer ${token}` },
      });
      const updatedFields = { [field]: null };
      emit('update-user', updatedFields);
      item.isEquipped = false;
      equippedItemName.value = `${item.name} has been unequipped`;
      showEquipPopup.value = true;
      inventoryCategories.value = categorizeInventory(inventory.value);
    } catch (error) {
      console.error(`Failed to unequip ${field}:`, error.response?.data || error.message);
    }
  };

  switch (item.type) {
    case 'profile_icon':
      await updateUnequipped('/api/user/update-equipped-profile-icon', 'equipped_profile_icon_path');
      break;
    case 'profile_frame':
      await updateUnequipped('/api/user/update-equipped-profile-frame', 'equipped_profile_frame_path');
      break;
    case 'background':
      await updateUnequipped('/api/user/update-equipped-background', 'equipped_background_path');
      break;
    case 'animation':
      await updateUnequipped('/api/user/update-equipped-animation', 'equipped_animation_path');
      break;
    case 'name_effect':
      await updateUnequipped('/api/user/update-equipped-name-effect', 'equipped_name_effect_path');
      break;
    case 'badge':
      await updateUnequipped('/api/user/update-equipped-badge', 'equipped_badge_path');
      break;
  }
};

const formatPrice = (price) => {
  if (typeof price === 'string') return price;
  return price === 0 ? 'Free' : `${price} Credits`;
};

const categorizeInventory = (items) => {
  const categorized = [
    { title: 'Profile Icons', description: 'Stand out with unique profile icons', items: items.filter(item => item.type === 'profile_icon') },
    { title: 'Backgrounds', description: 'Transform your profile with stunning themes', items: items.filter(item => { if (item.type === 'background') item.isBackground = true; return item.type === 'background'; }) },
    { title: 'Animations', description: 'Add dynamic effects', items: items.filter(item => item.type === 'animation') },
    { title: 'Name Effects', description: 'Make your username pop', items: items.filter(item => item.type === 'name_effect') },
    { title: 'Profile Frames', description: 'Frame your profile picture', items: items.filter(item => item.type === 'profile_frame') },
    { title: 'Profile Badges', description: 'Show off your status', items: items.filter(item => item.type === 'badge') },
    { title: 'Other Items', description: 'Miscellaneous items', items: items.filter(item => !['profile_icon', 'background', 'animation', 'name_effect', 'profile_frame', 'badge'].includes(item.type)) },
  ].filter(category => category.items.length > 0);
  return categorized;
};

const loadInventory = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/inventory`, { headers: { Authorization: `Bearer ${token}` } });
    console.log('Raw API response:', response.data);
    const mappedInventory = response.data.map(item => {
      const itemData = item.item || item;
      return {
        id: item.id,
        name: itemData.name,
        price: itemData.price || 0,
        icon_url: itemData.iconUrl || itemData.icon_url,
        type: itemData.type || determineItemType(itemData.name),
        isEquipped: checkIfEquipped(itemData),
      };
    });
    console.log('Mapped inventory:', mappedInventory);
    inventory.value = mappedInventory;
    inventoryCategories.value = categorizeInventory(mappedInventory);
  } catch (error) {
    console.error('Failed to load inventory:', error.response?.data || error.message);
  }
};

const determineItemType = (itemName) => {
  if (['Mini Crown', 'Shining Star', 'Glowing Heart', 'Ghostly Aura', 'Crystal Gem', 'Thunder Bolt', 'Moon Glow', 'Sun Flare',
    'Flame Crest', 'Snowflake Spark', 'Leaf Whisper', 'Wave Ripple', 'Cloud Drift', 'Gear Spin', 'Anchor Drop', 'Feather Light'].includes(itemName)) return 'profile_icon';
  if (['Soft Gradient', 'Starry Night', 'Minimal Waves', 'Pastel Sky', 'Urban Glow', 'Forest Mist', 'Ocean Depth', 'Desert Dunes',
    'Mountain Peak', 'Northern Lights', 'Lush Valley', 'Dusk Metropolis', 'Golden Fields', 'Frosty Tundra', 'Volcanic Ash', 'Nebula Cloud'].includes(itemName)) return 'background';
  if (['Gentle Sparkle', 'Fading Pulse', 'Soft Ripple', 'Orbit Glow', 'Subtle Glitch', 'Twirl Flash', 'Pulse Wave', 'Star Burst',
    'Stellar Rain', 'Vortex Spin', 'Flame Dance', 'Frost Swirl', 'Electric Surge', 'Dusk Transition', 'Rainbow Pulse', 'Bubble Pop'].includes(itemName)) return 'animation';
  if (['Soft Glow', 'Gradient Fade', 'Golden Outline', 'Dark Pulse', 'Cosmic Shine', 'Neon Edge', 'Frost Glow', 'Fire Flicker',
    'Emerald Sheen', 'Shadow Drift', 'Electric Glow', 'Lunar Haze', 'Solar Flare', 'Wave Shimmer', 'Crystal Pulse', 'Rainbow Gleam'].includes(itemName)) return 'name_effect';
  if (['Golden Ring', 'Crystal Edge', 'Star Border', 'Cloud Frame', 'Tech Circuit', 'Leaf Wreath', 'Wave Crest', 'Pixel Grid',
    'Flame Halo', 'Frost Ring', 'Gear Frame', 'Moon Orbit', 'Sun Burst', 'Ivy Crown', 'Neon Circuit', 'Starfield Edge'].includes(itemName)) return 'profile_frame';
  if (['Verified Badge', 'Founder Badge', 'VIP Badge', 'Creator Badge', 'Explorer Badge', 'Legend Badge', 'Pioneer Badge', 'Guardian Badge',
    'Warrior Badge', 'Sage Badge', 'Star Gazer Badge', 'Trailblazer Badge', 'Elementalist Badge', 'Innovator Badge', 'Nomad Badge', 'Champion Badge'].includes(itemName)) return 'badge';
  return 'other';
};

const checkIfEquipped = (item) => {
  const iconUrl = item.iconUrl || item.icon_url;
  if (item.type === 'profile_icon') return iconUrl === props.user.equipped_profile_icon_path;
  if (item.type === 'background') return iconUrl === props.user.equipped_background_path;
  if (item.type === 'animation') return iconUrl === props.user.equipped_animation_path;
  if (item.type === 'name_effect') return iconUrl === props.user.equipped_name_effect_path;
  if (item.type === 'profile_frame') return iconUrl === props.user.equipped_profile_frame_path;
  if (item.type === 'badge') return iconUrl === props.user.equipped_badge_path;
  return false;
};

onMounted(() => {
  loadInventory();
});
</script>

<style scoped>
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
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 1rem;
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

.cosmetic-item.background-item .cosmetic-icon {
  width: 100%;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.cosmetic-item:not(.background-item) .cosmetic-icon {
  width: 2rem;
  height: 2rem;
  margin-bottom: 0.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.item-name {
  font-size: 0.9rem;
  font-weight: 500;
  color: #000;
  margin-bottom: 0.25rem;
}

.item-price {
  font-size: 0.8rem;
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

.fixed.inset-0 {
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.bg-white.dark\:bg-gray-800.rounded-lg.p-6.w-full.max-w-5xl {
  max-height: 90vh;
}
</style>
