<template>
  <div
    class="fixed inset-0 bg-amber-900/30 flex items-center justify-center z-50 animate-fade-in"
    @click="closeInventoryOnBackdrop"
  >
    <div
      class="bg-white rounded-xl p-8 w-full max-w-5xl max-h-[90vh] overflow-y-auto shadow-lg border border-amber-200"
      @click.stop
    >
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-bold text-amber-900">Inventory</h3>
        <button @click="emit('close')" class="text-amber-700 hover:text-amber-900">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
      <div v-if="inventoryCategories.length > 0" class="cosmetics-grid">
        <div v-for="category in inventoryCategories" :key="category.title" class="category-card">
          <div
            class="category-header"
            @click="toggleCategory(category.title)"
          >
            <div class="flex items-center gap-2">
              <h3 class="category-title text-2xl font-bold text-amber-900">{{ category.title }}</h3>
              <p class="category-count text-sm text-amber-700">
                (You own {{ category.items.length }}/{{ getTotalItems(category.title) }})
              </p>
            </div>
            <svg
              :class="['h-5 w-5 text-amber-700', category.isOpen ? 'rotate-180' : '']"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
          <p class="category-description text-amber-800 mb-4">{{ category.description }}</p>
          <div v-if="category.isOpen" class="items-grid">
            <div
              v-for="item in category.items"
              :key="item.id"
              class="cosmetic-item"
              :class="{
                'background-item': item.type === 'background',
                'banner-item': item.type === 'custom_banner',
                'subscription-item': item.type === 'subscription'
              }"
            >
              <img
                :src="getItemIconUrl(item)"
                :alt="item.name"
                class="cosmetic-icon"
                @error="handleImageError($event, item)"
              />
              <h4
                :class="[
                  'item-name font-medium text-amber-900 truncate',
                  item.type === 'name_effect' ? getNameEffectClass(item.name) : '',
                  item.type === 'profile_font' ? getProfileFontClass(item.name) : '',
                  item.type === 'name_effect' ? 'effect-active' : ''
                ]"
              >
                {{ item.name }}
              </h4>
              <p class="item-price text-amber-700 text-sm font-medium">{{ formatPrice(item.price) }}</p>
              <p v-if="item.type === 'subscription'" class="item-status text-sm font-medium">
                {{ item.isActive ? 'Active' : 'Inactive' }}
              </p>
              <button
                v-if="item.type !== 'subscription'"
                :class="[
                  'w-full text-sm py-2 rounded-lg font-medium transition-all duration-300',
                  item.isEquipped ? 'btn-owned' : 'btn-primary'
                ]"
                @click="item.isEquipped ? unequipItem(item) : equipItem(item)"
              >
                {{ item.isEquipped ? 'Unequip' : 'Equip' }}
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center text-amber-800">
        No items in inventory
      </div>
      <div
        v-if="showEquipPopup"
        class="fixed inset-0 bg-amber-900/30 flex items-center justify-center z-50"
        @click="closeEquipPopupOnBackdrop"
      >
        <div class="bg-white rounded-lg p-6 max-w-sm" @click.stop>
          <p class="text-amber-900 mb-4">{{ equippedItemMessage }}</p>
          <button
            @click="showEquipPopup = false"
            class="btn-primary w-full"
          >
            OK
          </button>
        </div>
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
  'Basic': 'https://api.iconify.design/mdi/crown.svg?color=%23FFD700',
  'Premium': 'https://api.iconify.design/mdi/star.svg?color=%23FFD700',
  'Elite': 'https://api.iconify.design/mdi/diamond.svg?color=%23FFD700',
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

const getNameEffectClass = (name) => {
  if (!name) return '';
  const effectMap = {
    'Gradient Fade': 'gradient-fade',
    'Golden Outline': 'golden-outline',
    'Dark Pulse': 'dark-pulse',
    'Cosmic Shine': 'cosmic-shine',
    'Neon Edge': 'neon-edge',
    'Frost Glow': 'frost-glow',
    'Fire Flicker': 'fire-flicker',
    'Emerald Sheen': 'emerald-sheen',
    'Phantom Haze': 'phantom-haze',
    'Electric Glow': 'electric-glow',
    'Solar Flare': 'solar-flare',
    'Wave Shimmer': 'wave-shimmer',
    'Crystal Pulse': 'crystal-pulse',
    'Mystic Aura': 'mystic-aura',
    'Shadow Veil': 'shadow-veil',
    'Digital Pulse': 'digital-pulse',
  };
  return effectMap[name] || '';
};

const getProfileFontClass = (name) => {
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
    const response = await updateEquipped(item, item.type);
    inventory.value.forEach(i => {
      if (i.type === item.type) i.isEquipped = (i.id === item.id);
    });
    inventory.value = [...inventory.value];
    inventoryCategories.value = categorizeInventory(inventory.value);

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

    emit('update-user', { [field]: value });

    equippedItemMessage.value = `${item.name} has been equipped`;
    showEquipPopup.value = true;
  } catch (error) {
    equippedItemMessage.value = `Failed to equip ${item.name}. Please try again.`;
    showEquipPopup.value = true;
  }
};

const unequipItem = async (item) => {
  try {
    const response = await updateEquipped(item, item.type, true);
    item.isEquipped = false;
    inventory.value = [...inventory.value];
    inventoryCategories.value = categorizeInventory(inventory.value);

    const fieldMap = {
      custom_banner: 'equipped_banner_photo_path',
      name_effect: 'equipped_name_effect_path',
      profile_font: 'equipped_profile_font_path',
      profile_icon: 'equipped_profile_icon_path',
      background: 'equipped_background_path',
      badge: 'equipped_badge_path',
    };
    const field = fieldMap[item.type] || `equipped_${item.type}_path`;

    emit('update-user', { [field]: null });

    equippedItemMessage.value = `${item.name} has been unequipped`;
    showEquipPopup.value = true;
  } catch (error) {
    equippedItemMessage.value = `Failed to unequip ${item.name}. Please try again.`;
    showEquipPopup.value = true;
  }
};

const formatPrice = (price) => {
  if (typeof price === 'string') return price;
  return price === 0 ? 'Free' : `${price} Credits`;
};

const categorizeInventory = (items) => {
  const categorized = [
    {
      title: 'Subscriptions',
      description: 'Your active and owned subscription tiers',
      items: items.filter(item => item.type === 'subscription')
    },
    { title: 'Profile Icons', description: 'Stand out with unique profile icons', items: items.filter(item => item.type === 'profile_icon') },
    { title: 'Backgrounds', description: 'Transform your profile with stunning themes', items: items.filter(item => item.type === 'background') },
    { title: 'Name Effects', description: 'Make your username pop', items: items.filter(item => item.type === 'name_effect') },
    { title: 'Custom Banners', description: 'Enhance your profile header with vibrant animated banners', items: items.filter(item => item.type === 'custom_banner') },
    { title: 'Profile Badges', description: 'Show off your status', items: items.filter(item => item.type === 'badge') },
    { title: 'Profile Fonts', description: 'Customize your text style', items: items.filter(item => item.type === 'profile_font') },
    { title: 'Other Items', description: 'Miscellaneous items', items: items.filter(item => !['subscription', 'profile_icon', 'background', 'name_effect', 'custom_banner', 'badge', 'profile_font'].includes(item.type)) },
  ].filter(category => category.items.length > 0).map(category => ({
    ...category,
    isOpen: categoryStates.value[category.title] ?? false,
  }));
  return categorized;
};

const toggleCategory = (title) => {
  categoryStates.value[title] = !categoryStates.value[title];
  inventoryCategories.value = categorizeInventory(inventory.value);
};

const getTotalItems = (categoryTitle) => {
  const totalItemsMap = {
    'Subscriptions': 3,
    'Profile Icons': 16,
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
    const mappedInventory = response.data.map(item => {
      const itemData = item.item || item;
      const isSubscription = [1, 2, 3].includes(item.item_id) || itemData.type === 'subscription';
      return {
        id: item.id,
        item_id: item.item_id,
        name: itemData.name,
        price: itemData.price || 0,
        icon_url: itemData.icon_url || itemData.iconUrl,
        type: isSubscription ? 'subscription' : (itemData.type || determineItemType(itemData.name)),
        isEquipped: isSubscription ? false : checkIfEquipped(itemData),
        isActive: isSubscription && itemData.name === props.user.subscription_tier,
      };
    });
    inventory.value = mappedInventory;
    inventoryCategories.value = categorizeInventory(mappedInventory);
  } catch (error) {
    console.error('Failed to load inventory:', error.response?.data || error.message);
    equippedItemMessage.value = 'Failed to load inventory. Please try again.';
    showEquipPopup.value = true;
  }
};

const determineItemType = (itemName) => {
  if (['Basic', 'Premium', 'Elite'].includes(itemName)) return 'subscription';
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
@import '@/styles/nameEffects.css';
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@700&family=Black+Ops+One&family=Dancing+Script:wght@700&family=Courier+Prime&family=Bungee&family=Orbitron:wght@700&family=Wallpoet&family=VT323&family=Monoton&family=Special+Elite&family=Creepster&family=Audiowide&family=Caveat:wght@700&family=Permanent+Marker&display=swap');

/* Animations */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

/* Modal container */
.cosmetics-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

.category-card {
  background: #ffffff;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
  color: #7c2d12; /* amber-900 */
}

.category-count {
  font-size: 0.875rem;
  color: #b45309; /* amber-700 */
}

.category-description {
  color: #92400e; /* amber-800 */
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
  background: #fefce8; /* amber-50 */
  border-radius: 0.5rem;
  text-align: center;
  transition: background-color 0.3s;
}

.cosmetic-item:hover {
  background: #fefcbf; /* amber-100 */
}

.cosmetic-item.background-item .cosmetic-icon,
.cosmetic-item.banner-item .cosmetic-icon {
  width: 100%;
  height: 96px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  background-color: #f3e8d3;
}

.cosmetic-item.subscription-item .cosmetic-icon {
  width: 64px;
  height: 64px;
  margin-bottom: 0.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
  background-color: #f3e8d3;
  padding: 8px;
  object-fit: contain;
}

.cosmetic-item:not(.background-item):not(.banner-item):not(.subscription-item) .cosmetic-icon {
  width: 64px;
  height: 64px;
  margin-bottom: 0.5rem;
  display: block;
  margin-left: auto;
  margin-right: auto;
  background-color: #f3e8d3;
  padding: 8px;
  object-fit: contain;
}

.item-name {
  font-size: 0.9rem;
  font-weight: 500;
  color: #7c2d12; /* amber-900 */
  margin-bottom: 0.25rem;
}

.item-price {
  font-size: 0.8rem;
  color: #b45309; /* amber-700 */
  margin-bottom: 0.5rem;
}

.item-status {
  font-size: 0.8rem;
  margin-bottom: 0.5rem;
  font-weight: medium;
}

.item-status.active {
  color: #4caf50;
}

.item-status.inactive {
  color: #92400e; /* amber-800 */
}

.btn-primary {
  background: linear-gradient(to right, #f59e0b, #d97706);
  color: #000000;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  background: linear-gradient(to right, #f59e0b, #b45309);
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
}

.btn-owned {
  background: #4caf50;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-owned:hover {
  background: #45a049;
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(76, 175, 80, 0.3);
}

.effect-active {
  display: inline-block;
  padding: 0 0.25rem;
}

/* Fallback Profile Font Styles */
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

/* Fallback Name Effect Styles */
.gradient-fade { background: linear-gradient(to right, #f59e0b, #d97706); -webkit-background-clip: text; color: transparent; }
.golden-outline { color: #f59e0b; text-shadow: 0 0 2px #d97706; }
.dark-pulse { color: #92400e; animation: pulse 2s infinite; }
.cosmic-shine { color: #f59e0b; text-shadow: 0 0 5px #fefcbf; }
.neon-edge { color: #d97706; text-shadow: 0 0 3px #f59e0b; }

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

@media (max-width: 768px) {
  .items-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  }
  .cosmetic-item.background-item .cosmetic-icon,
  .cosmetic-item.banner-item .cosmetic-icon {
    height: 64px;
  }
  .cosmetic-item.subscription-item .cosmetic-icon {
    width: 48px;
    height: 48px;
  }
  .cosmetic-item:not(.background-item):not(.banner-item):not(.subscription-item) .cosmetic-icon {
    width: 48px;
    height: 48px;
  }
  .category-title {
    font-size: 1.25rem;
  }
  .category-count {
    font-size: 0.75rem;
  }
}
</style>
