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
          <div
            class="category-header"
            @click="toggleCategory(category.title)"
          >
            <h3 class="category-title">{{ category.title }}</h3>
            <p class="category-count">
              (You own {{ category.items.length }}/{{ getTotalItems(category.title) }})
            </p>
            <i
              :class="['fas', category.isOpen ? 'fa-chevron-up' : 'fa-chevron-down', 'toggle-icon']"
            ></i>
          </div>
          <p class="category-description">{{ category.description }}</p>
          <div v-if="category.isOpen" class="items-grid">
            <div
              v-for="item in category.items"
              :key="item.id"
              class="cosmetic-item"
              :class="{ 'background-item': item.type === 'background', 'banner-item': item.type === 'custom_banner' }"
            >
              <img
                :src="getItemIconUrl(item)"
                :alt="item.name"
                class="cosmetic-icon"
                @error="handleImageError($event, item)"
              />
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
        <p class="text-gray-900 dark:text-white">{{ equippedItemMessage }}</p>
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
const equippedItemMessage = ref('');
const inventory = ref([]);
const inventoryCategories = ref([]);

// Track open/closed state of categories
const categoryStates = ref({});

const fallbackImage = 'https://api.iconify.design/lucide/image-off.svg';
const fallbackIcons = {
  'Misty Peaks': 'https://api.iconify.design/mdi/mountain.svg?color=%23666666',
  'Cascading Falls': 'https://api.iconify.design/mdi/waterfall.svg?color=%2300FFFF',
  'Stormy Waves': 'https://api.iconify.design/mdi/wave.svg?color=%230066CC',
  'Desert Sunset': 'https://api.iconify.design/mdi/weather-sunset.svg?color=%23FF4500',
  'Northern Lights': 'https://api.iconify.design/mdi/weather-night.svg?color=%2300FF00',
  'Gentle Waterfall': 'https://api.iconify.design/mdi/water.svg?color=%2300FFFF',
  'Autumn Drift': 'https://api.iconify.design/mdi/leaf.svg?color=%23FFA500',
  'Tech Grid': 'https://api.iconify.design/mdi/grid.svg?color=%2300FFFF',
  'Particle Flow': 'https://api.iconify.design/mdi/dots-horizontal.svg?color=%23FF00FF',
  'Circuit Pulse': 'https://api.iconify.design/mdi/circuit-board.svg?color=%2300FF00',
  'Matrix Rain': 'https://api.iconify.design/mdi/matrix.svg?color=%2300FF00',
  'Cyber Skyline': 'https://api.iconify.design/mdi/city.svg?color=%23FF4500',
  'Code Rainfall': 'https://api.iconify.design/mdi/code-braces.svg?color=%2300FF00',
  'Holo Waves': 'https://api.iconify.design/mdi/waveform.svg?color=%2300FFFF',
  'Neon Pulse': 'https://api.iconify.design/mdi/pulse.svg?color=%23FF00FF',
  'Star Warp': 'https://api.iconify.design/mdi/star-four-points.svg?color=%2300FFFF',
};

const closeInventoryOnBackdrop = (event) => {
  if (event.target === event.currentTarget) emit('close');
};

const closeEquipPopupOnBackdrop = (event) => {
  if (event.target === event.currentTarget) showEquipPopup.value = false;
};

const getItemIconUrl = (item) => {
  if (item.icon_url && !item.iconFailed) {
    return item.icon_url;
  }
  return fallbackIcons[item.name] || fallbackImage;
};

const handleImageError = (event, item) => {
  console.warn(`Image failed to load for ${item.name} (ID: ${item.id}): ${item.icon_url}`);
  item.iconFailed = true;
  event.target.src = fallbackIcons[item.name] || fallbackImage;
};

const updateEquipped = async (item, type, isUnequip = false) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    if (!props.user?.id) throw new Error('User ID is missing');

    const typeToEndpointMap = {
      background: '/api/user/update-equipped-background',
      custom_banner: '/api/user/update-equipped-banner',
      profile_icon: '/api/user/update-equipped-profile-icon',
      name_effect: '/api/user/update-equipped-name-effect',
      profile_font: '/api/user/update-equipped-profile-font',
      badge: '/api/user/update-equipped-badge',
    };

    const endpoint = typeToEndpointMap[type] || `/api/user/update-equipped-${type.replace('_', '-')}`;

    let payload;
    if (type === 'custom_banner') {
      payload = isUnequip
        ? { userId: props.user.id, equipped_banner_photo_path: null }
        : { userId: props.user.id, equipped_banner_photo_path: item.icon_url };
    } else if (['name_effect', 'profile_font'].includes(type)) {
      payload = isUnequip
        ? { userId: props.user.id, item_id: null }
        : { userId: props.user.id, item_id: item.item_id };
    } else {
      payload = isUnequip
        ? { userId: props.user.id, [`equipped_${type}_path`]: null }
        : { userId: props.user.id, [`equipped_${type}_path`]: item.icon_url };
    }

    console.log(`Sending payload to ${endpoint}:`, payload);

    const response = await apiClient.post(endpoint, payload, {
      headers: { Authorization: `Bearer ${token}` },
    });

    console.log(`${isUnequip ? 'Unequipped' : 'Equipped'} ${type} response:`, response.data);
    return response.data;
  } catch (error) {
    console.error(`Failed to ${isUnequip ? 'unequip' : 'equip'} ${type}:`, error.response?.data || error.message);
    throw error;
  }
};

const equipItem = async (item) => {
  try {
    console.log('Attempting to equip item:', item);
    const response = await updateEquipped(item, item.type);
    console.log('Equip response:', response);

    // Update local inventory state
    inventory.value.forEach(i => {
      if (i.type === item.type) i.isEquipped = (i.id === item.id);
    });
    inventory.value = [...inventory.value];
    inventoryCategories.value = categorizeInventory(inventory.value);

    // Determine the field to update based on item type
    const fieldMap = {
      custom_banner: 'equipped_banner_photo_path',
      name_effect: 'equipped_name_effect_path',
      profile_font: 'equipped_profile_font_path',
      profile_icon: 'equipped_profile_icon_path',
      background: 'equipped_background_path',
      badge: 'equipped_badge_path',
    };
    const field = fieldMap[item.type] || `equipped_${item.type}_path`;
    const value = ['name_effect', 'profile_font'].includes(item.type) ? item.name : item.icon_url;

    console.log(`Emitting update for ${field}: ${value}`);
    emit('update-user', { [field]: value });

    equippedItemMessage.value = `${item.name} has been equipped`;
    showEquipPopup.value = true;
    console.log('Equipped item:', item);
  } catch (error) {
    equippedItemMessage.value = `Failed to equip ${item.name}. Please try again.`;
    showEquipPopup.value = true;
    console.error('Equip failed:', error.response?.data || error.message);
  }
};

const unequipItem = async (item) => {
  try {
    console.log('Attempting to unequip item:', item);
    const response = await updateEquipped(item, item.type, true);
    console.log('Unequip response:', response);

    // Update local inventory state
    item.isEquipped = false;
    inventory.value = [...inventory.value];
    inventoryCategories.value = categorizeInventory(inventory.value);

    // Determine the field to update based on item type
    const fieldMap = {
      custom_banner: 'equipped_banner_photo_path',
      name_effect: 'equipped_name_effect_path',
      profile_font: 'equipped_profile_font_path',
      profile_icon: 'equipped_profile_icon_path',
      background: 'equipped_background_path',
      badge: 'equipped_badge_path',
    };
    const field = fieldMap[item.type] || `equipped_${item.type}_path`;

    console.log(`Emitting update for ${field}: null`);
    emit('update-user', { [field]: null });

    equippedItemMessage.value = `${item.name} has been unequipped`;
    showEquipPopup.value = true;
    console.log('Unequipped item:', item);
  } catch (error) {
    equippedItemMessage.value = `Failed to unequip ${item.name}. Please try again.`;
    showEquipPopup.value = true;
    console.error('Unequip failed:', error.response?.data || error.message);
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
    { title: 'Custom Banners', description: 'Enhance your profile header with vibrant animated banners', items: items.filter(item => item.type === 'custom_banner') },
    { title: 'Profile Badges', description: 'Show off your status', items: items.filter(item => item.type === 'badge') },
    { title: 'Profile Fonts', description: 'Customize your text style', items: items.filter(item => item.type === 'profile_font') },
    { title: 'Other Items', description: 'Miscellaneous items', items: items.filter(item => !['profile_icon', 'background', 'name_effect', 'custom_banner', 'badge', 'profile_font'].includes(item.type)) },
  ].filter(category => category.items.length > 0).map(category => ({
    ...category,
    isOpen: categoryStates.value[category.title] ?? false, // Default to collapsed
  }));
  return categorized;
};

// Toggle category open/close state
const toggleCategory = (title) => {
  categoryStates.value[title] = !categoryStates.value[title];
  inventoryCategories.value = categorizeInventory(inventory.value);
};

// Placeholder for total items per category
const getTotalItems = (categoryTitle) => {
  // Replace with actual logic to fetch total items (e.g., from API or static data)
  const totalItemsMap = {
    'Profile Icons': 16, // Based on determineItemType
    'Backgrounds': 16,
    'Name Effects': 16,
    'Custom Banners': 16,
    'Profile Badges': 16,
    'Profile Fonts': 16,
    'Other Items': 0,
  };
  return totalItemsMap[categoryTitle] || 0;
};

const loadInventory = async () => {
  if (!props.user?.id) return;
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No authentication token found.');
    const response = await apiClient.get(`/api/user/${props.user.id}/inventory`);
    console.log('Raw inventory API response:', response.data);
    const mappedInventory = response.data.map(item => {
      const itemData = item.item || item;
      return {
        id: item.id,
        item_id: item.item_id,
        name: itemData.name,
        price: itemData.price || 0,
        icon_url: itemData.icon_url || itemData.iconUrl,
        type: itemData.type || determineItemType(itemData.name),
        isEquipped: checkIfEquipped(itemData),
      };
    });
    inventory.value = mappedInventory;
    inventoryCategories.value = categorizeInventory(mappedInventory);
    console.log('Loaded inventory:', mappedInventory);
  } catch (error) {
    console.error('Failed to load inventory:', error.response?.data || error.message);
    equippedItemMessage.value = 'Failed to load inventory. Please try again.';
    showEquipPopup.value = true;
  }
};

const determineItemType = (itemName) => {
  if (['Mini Crown', 'Shining Star', 'Glowing Heart', 'Ghostly Aura', 'Crystal Gem', 'Thunder Bolt', 'Moon Glow', 'Sun Flare',
    'Flame Crest', 'Snowflake Spark', 'Leaf Whisper', 'Wave Ripple', 'Cloud Drift', 'Gear Spin', 'Anchor Drop', 'Feather Light'].includes(itemName)) return 'profile_icon';
  if (['Soft Gradient', 'Starry Night', 'Minimal Waves', 'Pastel Sky', 'Urban Glow', 'Forest Mist', 'Ocean Depth', 'Desert Dunes',
    'Mountain Peak', 'Pollution', 'Lush Valley', 'Dusk Metropolis', 'Golden Fields', 'Volcanic Ash', 'Nebula Cloud', 'Twilight Horizon'].includes(itemName)) return 'background';
  if (['Gradient Fade', 'Golden Outline', 'Dark Pulse', 'Cosmic Shine', 'Neon Edge', 'Frost Glow', 'Fire Flicker',
    'Emerald Sheen', 'Phantom Haze', 'Electric Glow', 'Solar Flare', 'Wave Shimmer', 'Crystal Pulse', 'Mystic Aura', 'Shadow Veil', 'Digital Pulse'].includes(itemName)) return 'name_effect';
  if (['Misty Peaks', 'Cascading Falls', 'Stormy Waves', 'Desert Sunset', 'Northern Lights', 'Gentle Waterfall', 'Autumn Drift',
    'Tech Grid', 'Particle Flow', 'Circuit Pulse', 'Matrix Rain', 'Cyber Skyline', 'Code Rainfall', 'Holo Waves', 'Neon Pulse', 'Star Warp'].includes(itemName)) return 'custom_banner';
  if (['Verified Badge', 'Founder Badge', 'VIP Badge', 'Creator Badge', 'Explorer Badge', 'Legend Badge', 'Pioneer Badge', 'Guardian Badge',
    'Warrior Badge', 'Sage Badge', 'Star Gazer Badge', 'Trailblazer Badge', 'Elementalist Badge', 'Innovator Badge', 'Nomad Badge', 'Champion Badge'].includes(itemName)) return 'badge';
  if (['Pixel Art', 'Comic Sans', 'Gothic', 'Cursive', 'Typewriter', 'Bubble', 'Neon', 'Graffiti', 'Retro', 'Cyberpunk',
    'Western', 'Chalkboard', 'Horror', 'Futuristic', 'Handwritten', 'Bold Script'].includes(itemName)) return 'profile_font';
  return 'other';
};

const checkIfEquipped = (item) => {
  const iconUrl = item.icon_url || item.iconUrl;
  if (item.type === 'profile_icon') return iconUrl === props.user.equipped_profile_icon_path;
  if (item.type === 'background') return iconUrl === props.user.equipped_background_path;
  if (item.type === 'name_effect') return item.name === props.user.equipped_name_effect_path;
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

.category-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  margin-bottom: 0.5rem;
}

.category-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #000;
  margin-right: 0.5rem;
}

.category-count {
  font-size: 1.2rem;
  color: #666;
  margin-right: auto;
}

.toggle-icon {
  color: #666;
  transition: transform 0.3s;
}

.category-description {
  color: #666;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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
  height: 120px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  background-color: #e0e0e0;
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
  padding: 0.5rem 1rem;
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

@media (max-width: 768px) {
  .items-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  }
  .cosmetic-item.background-item .cosmetic-icon,
  .cosmetic-item.banner-item .cosmetic-icon {
    height: 100px;
  }
  .category-title {
    font-size: 1.3rem;
  }
  .category-count {
    font-size: 1rem;
  }
}
</style>
