<template>
  <div
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    @click="closeInventoryOnBackdrop"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-5xl max-h-[90vh] overflow-y-auto"
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
            <div v-for="item in category.items" :key="item.id" class="cosmetic-item" :class="{ 'background-item': item.type === 'background', 'banner-item': item.type === 'custom_banner' }">
              <img :src="getItemIconUrl(item)" :alt="item.name" class="cosmetic-icon" @error="handleImageError(item)" />
              <h4 class="item-name">{{ item.name }}</h4>
              <p class="item-price">{{ formatPrice(item.price) }}</p>
              <button
                :class="['buy-button', item.isEquipped ? 'unequip-button' : '']"
                @click="item.isEquipped ? unequipItem(item) : equipItem(item)"
              >
                {{ item.isEquipped ? 'Unequip' : 'Equip' }}
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

// Fallback image and icons for when GIFs fail to load
const fallbackImage = 'https://api.iconify.design/lucide/image-off.svg';
const fallbackIcons = {
  'Cosmic Vortex': 'https://api.iconify.design/mdi/star-four-points.svg?color=%2300BFFF',
  'Neon Cityscape': 'https://api.iconify.design/mdi/pulse.svg?color=%23FF4500',
  'Firestorm Horizon': 'https://api.iconify.design/mdi/fire.svg?color=%23FF4500',
  'Mystic Nebula': 'https://api.iconify.design/mdi/cloud.svg?color=%2300FFFF',
  'Cyber Grid': 'https://api.iconify.design/mdi/grid.svg?color=%2300FFFF',
  'Ethereal Waves': 'https://api.iconify.design/mdi/wave.svg?color=%2300FFFF',
  'Ocean Surge': 'https://api.iconify.design/mdi/water.svg?color=%2300FFFF',
  'Pixel Storm': 'https://api.iconify.design/mdi/lightning-bolt.svg?color=%23FF00FF',
  'Lava Flow': 'https://api.iconify.design/mdi/lava.svg?color=%23FF4500',
  'Frost Vortex': 'https://api.iconify.design/mdi/snowflake.svg?color=%2300FFFF',
  'Steampunk Gears': 'https://api.iconify.design/mdi/cog.svg?color=%23FFD700',
  'Lunar Eclipse': 'https://api.iconify.design/mdi/moon.svg?color=%23FFFFFF',
  'Glitch Matrix': 'https://api.iconify.design/mdi/matrix.svg?color=%2300FF00',
  'Aurora Dance': 'https://api.iconify.design/mdi/weather-night.svg?color=%2300FF00',
  'Galactic Spin': 'https://api.iconify.design/mdi/galaxy.svg?color=%2300FFFF',
  'Rainbow Flux': 'https://api.iconify.design/mdi/rainbow.svg?color=%23FF00FF',
};

const closeInventoryOnBackdrop = (event) => {
  if (event.target === event.currentTarget) emit('close');
};

const closeEquipPopupOnBackdrop = (event) => {
  if (event.target === event.currentTarget) showEquipPopup.value = false;
};

// Method to get the item icon URL with fallback
const getItemIconUrl = (item) => {
  if (item.icon_url && !item.iconFailed) {
    return item.icon_url;
  }
  return fallbackIcons[item.name] || fallbackImage;
};

// Handle image load errors
const handleImageError = (item) => {
  console.warn(`Image failed to load for ${item.name}: ${item.icon_url}`);
  item.iconFailed = true;
  item.icon_url = fallbackIcons[item.name] || fallbackImage;
};

const updateEquipped = async (itemId, type, isUnequip = false) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');

    let endpoint;
    let payload;

    if (type === 'custom_banner') {
      endpoint = '/api/user/update-equipped-banner';
      payload = isUnequip ? { banner_id: null } : { banner_id: itemId };
    } else {
      endpoint = `/api/user/update-equipped-${type}`;
      payload = isUnequip ? { item_id: null } : { item_id: itemId };
    }

    const response = await apiClient.post(endpoint, payload, {
      headers: { Authorization: `Bearer ${token}` },
    });

    return response.data;
  } catch (error) {
    console.error(`Failed to ${isUnequip ? 'unequip' : 'equip'} ${type}:`, error.response?.data || error.message);
    throw error;
  }
};

const equipItem = async (item) => {
  try {
    equippedItemName.value = `${item.name} has been equipped`;
    showEquipPopup.value = true;

    const response = await updateEquipped(item.id, item.type);
    item.isEquipped = true;

    inventory.value.forEach(i => {
      if (i.type === item.type && i.id !== item.id) i.isEquipped = false;
    });
    inventory.value = [...inventory.value];
    inventoryCategories.value = categorizeInventory(inventory.value);

    const field = item.type === 'custom_banner' ? 'equipped_banner_photo_path' : `equipped_${item.type}_path`;
    emit('update-user', { [field]: item.icon_url });
    console.log('Equipped item:', item);
  } catch (error) {
    console.error('Equip failed:', error);
  }
};

const unequipItem = async (item) => {
  try {
    equippedItemName.value = `${item.name} has been unequipped`;
    showEquipPopup.value = true;

    const response = await updateEquipped(item.id, item.type, true);
    item.isEquipped = false;

    inventory.value = [...inventory.value];
    inventoryCategories.value = categorizeInventory(inventory.value);

    const field = item.type === 'custom_banner' ? 'equipped_banner_photo_path' : `equipped_${item.type}_path`;
    emit('update-user', { [field]: null });
    console.log('Unequipped item:', item);
  } catch (error) {
    console.error('Unequip failed:', error);
  }
};

const formatPrice = (price) => {
  if (typeof price === 'string') return price;
  return price === 0 ? 'Free' : `${price} Credits`;
};

const categorizeInventory = (items) => {
  const categorized = [
    { title: 'Profile Icons', description: 'Stand out with unique profile icons', items: items.filter(item => item.type === 'profile_icon') },
    { title: 'Backgrounds', description: 'Transform your profile with stunning themes', items: items.filter(item => item.type === 'background') },
    { title: 'Name Effects', description: 'Make your username pop', items: items.filter(item => item.type === 'name_effect') },
    { title: 'Custom Banners', description: 'Enhance your profile header with animated 4K banners', items: items.filter(item => item.type === 'custom_banner') },
    { title: 'Profile Badges', description: 'Show off your status', items: items.filter(item => item.type === 'badge') },
    { title: 'Profile Fonts', description: 'Customize your text style', items: items.filter(item => item.type === 'profile_font') },
    { title: 'Other Items', description: 'Miscellaneous items', items: items.filter(item => !['profile_icon', 'background', 'name_effect', 'custom_banner', 'badge', 'profile_font'].includes(item.type)) },
  ].filter(category => category.items.length > 0);
  return categorized;
};

const loadInventory = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/inventory`, { headers: { Authorization: `Bearer ${token}` } });
    const mappedInventory = response.data.map(item => {
      const itemData = item.item || item;
      return {
        id: itemData.id || item.id,
        name: itemData.name,
        price: itemData.price || 0,
        icon_url: itemData.iconUrl || itemData.icon_url,
        type: itemData.type || determineItemType(itemData.name),
        isEquipped: checkIfEquipped(itemData),
      };
    });
    inventory.value = mappedInventory;
    inventoryCategories.value = categorizeInventory(mappedInventory);
    console.log('Loaded inventory:', mappedInventory);
  } catch (error) {
    console.error('Failed to load inventory:', error.response?.data || error.message);
  }
};

const determineItemType = (itemName) => {
  if (['Mini Crown', 'Shining Star', 'Glowing Heart', 'Ghostly Aura', 'Crystal Gem', 'Thunder Bolt', 'Moon Glow', 'Sun Flare',
    'Flame Crest', 'Snowflake Spark', 'Leaf Whisper', 'Wave Ripple', 'Cloud Drift', 'Gear Spin', 'Anchor Drop', 'Feather Light'].includes(itemName)) return 'profile_icon';
  if (['Soft Gradient', 'Starry Night', 'Minimal Waves', 'Pastel Sky', 'Urban Glow', 'Forest Mist', 'Ocean Depth', 'Desert Dunes',
    'Mountain Peak', 'Polar Glow', 'Lush Valley', 'Dusk Metropolis', 'Golden Fields', 'Volcanic Ash', 'Nebula Cloud', 'Twilight Horizon'].includes(itemName)) return 'background';
  if (['Gradient Fade', 'Golden Outline', 'Dark Pulse', 'Cosmic Shine', 'Neon Edge', 'Frost Glow', 'Fire Flicker',
    'Emerald Sheen', 'Phantom Haze', 'Electric Glow', 'Solar Flare', 'Wave Shimmer', 'Crystal Pulse', 'Mystic Aura', 'Shadow Veil', 'Digital Pulse'].includes(itemName)) return 'name_effect';
  if (['Cosmic Vortex', 'Neon Cityscape', 'Firestorm Horizon', 'Mystic Nebula', 'Cyber Grid', 'Ethereal Waves', 'Ocean Surge', 'Pixel Storm',
    'Lava Flow', 'Frost Vortex', 'Steampunk Gears', 'Lunar Eclipse', 'Glitch Matrix', 'Aurora Dance', 'Galactic Spin', 'Rainbow Flux'].includes(itemName)) return 'custom_banner';
  if (['Verified Badge', 'Founder Badge', 'VIP Badge', 'Creator Badge', 'Explorer Badge', 'Legend Badge', 'Pioneer Badge', 'Guardian Badge',
    'Warrior Badge', 'Sage Badge', 'Star Gazer Badge', 'Trailblazer Badge', 'Elementalist Badge', 'Innovator Badge', 'Nomad Badge', 'Champion Badge'].includes(itemName)) return 'badge';
  if (['Pixel Art', 'Comic Sans', 'Gothic', 'Cursive', 'Typewriter', 'Bubble', 'Neon', 'Graffiti', 'Retro', 'Cyberpunk',
    'Western', 'Chalkboard', 'Horror', 'Futuristic', 'Handwritten', 'Bold Script'].includes(itemName)) return 'profile_font';
  return 'other';
};

const checkIfEquipped = (item) => {
  const iconUrl = item.iconUrl || item.icon_url;
  if (item.type === 'profile_icon') return iconUrl === props.user.equipped_profile_icon_path;
  if (item.type === 'background') return iconUrl === props.user.equipped_background_path;
  if (item.type === 'name_effect') return iconUrl === props.user.equipped_name_effect_path;
  if (item.type === 'custom_banner') return iconUrl === props.user.equipped_banner_photo_path;
  if (item.type === 'badge') return iconUrl === props.user.equipped_badge_path;
  if (item.type === 'profile_font') return item.name === props.user.equipped_profile_font_path;
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

.cosmetic-item.background-item .cosmetic-icon,
.cosmetic-item.banner-item .cosmetic-icon {
  width: 100%;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.cosmetic-item:not(.background-item):not(.banner-item) .cosmetic-icon {
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
</style>
