<template>
  <div class="flex flex-col min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 p-4">
    <NavBar />
    <div class="flex gap-5 flex-1 mt-16 mb-16 overflow-hidden px-4">
      <!-- Sidebar -->
      <div class="w-64 bg-white rounded-xl shadow-lg p-6">
        <ul class="space-y-2">
          <!-- Account Settings Tabs -->
          <li
            v-for="tab in accountTabs"
            :key="tab.id"
            :class="{ 'bg-amber-100 text-amber-800': activeTab === tab.id }"
            class="p-3 rounded-lg cursor-pointer hover:bg-amber-50 transition-all duration-200"
            @click="activeTab = tab.id"
          >
            <span class="flex items-center gap-2">
              <component :is="tab.icon" class="h-5 w-5 text-amber-600" />
              {{ tab.label }}
            </span>
          </li>
          <!-- App Settings Tabs -->
          <li
            v-for="tab in appTabs"
            :key="tab.id"
            :class="{ 'bg-amber-100 text-amber-800': activeTab === tab.id }"
            class="p-3 rounded-lg cursor-pointer hover:bg-amber-50 transition-all duration-200"
            @click="activeTab = tab.id"
          >
            <span class="flex items-center gap-2">
              <component :is="tab.icon" class="h-5 w-5 text-amber-600" />
              {{ tab.label }}
            </span>
          </li>
          <!-- Logout -->
          <li
            class="p-3 rounded-lg cursor-pointer hover:bg-amber-50 transition-all duration-200 text-red-600"
            @click="showLogoutModal = true"
          >
            <span class="flex items-center gap-2">
              <ArrowRightOnRectangleIcon class="h-5 w-5" />
              Log Out
            </span>
          </li>
        </ul>
      </div>

      <!-- Content Area -->
      <div class="flex-1 overflow-y-auto">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-4xl mx-auto">
          <!-- My Account -->
          <section v-if="activeTab === 'myAccount'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">My Account</h1>
            <v-text-field
              v-model="name"
              label="Name"
              placeholder="Your name"
              class="bg-gray-100 rounded-lg focus:ring-amber-500"
              @blur="updateUserInfo"
            />
            <v-text-field
              v-model="email"
              label="Email"
              placeholder="your@example.com"
              class="bg-gray-100 rounded-lg focus:ring-amber-500"
              @blur="updateUserInfo"
            />
            <v-img :src="profilePicture" class="w-24 h-24 rounded-full mx-auto" />
            <v-btn
              class="bg-amber-500 hover:bg-amber-600 text-white"
              @click="changePassword"
            >
              Change Password
            </v-btn>
          </section>

          <!-- Profiles -->
          <section v-if="activeTab === 'profiles'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Profiles</h1>
            <p class="text-amber-600">Profile Picture:</p>
            <v-img :src="profilePicture" class="w-24 h-24 rounded-full mx-auto" />
            <v-btn
              class="bg-amber-500 hover:bg-amber-600 text-white"
              @click="uploadProfilePicture"
            >
              Upload New Picture
            </v-btn>
          </section>

          <!-- Devices -->
          <section v-if="activeTab === 'devices'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Devices</h1>
            <ul class="space-y-2">
              <li
                v-for="device in devices"
                :key="device.id"
                class="flex justify-between items-center p-2 bg-gray-100 rounded-lg"
              >
                <span>{{ device.name }} - Last active: {{ device.lastActive }}</span>
                <v-btn
                  color="red"
                  small
                  class="hover:bg-red-600 text-white"
                  @click="removeDevice(device.id)"
                >
                  Remove
                </v-btn>
              </li>
            </ul>
          </section>

          <!-- Privacy & Safety -->
          <section v-if="activeTab === 'privacy'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Privacy & Safety</h1>
            <p class="text-amber-600">Last Login: {{ lastLoginIP }} on {{ lastLoginDate }}</p>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="twoFactorAuth"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Enable Two-Factor Authentication</span>
            </label>
          </section>

          <!-- Account Notifications -->
          <section v-if="activeTab === 'accountNotifications'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Notification Preferences</h1>
            <div class="bg-gray-100 rounded-lg p-4 space-y-2">
              <v-checkbox
                v-model="accountEmailNotifications"
                label="Email Notifications"
                class="text-amber-800"
              />
              <v-checkbox
                v-model="pushNotifications"
                label="Push Notifications"
                class="text-amber-800"
              />
            </div>
          </section>

          <!-- Connections -->
          <section v-if="activeTab === 'connections'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Connections</h1>
            <p class="text-amber-600">Linked Accounts:</p>
            <ul class="space-y-2">
              <li
                v-for="(account, index) in linkedAccounts"
                :key="account.id"
                class="flex items-center gap-2 p-2 bg-gray-100 rounded-lg"
              >
                <v-img :src="account.logo" class="w-6 h-6" />
                <span>{{ account.username }}</span>
                <v-btn icon @click="editConnection(account, index)">
                  <PencilIcon class="h-5 w-5 text-amber-600" />
                </v-btn>
              </li>
            </ul>
            <v-btn
              icon
              class="bg-amber-500 hover:bg-amber-600 text-white"
              @click="showAddSocialMediaModal = true"
            >
              <PlusIcon class="h-5 w-5" />
            </v-btn>
          </section>

          <!-- Billing -->
          <section v-if="activeTab === 'billing'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Billing</h1>
            <p class="text-amber-600">Subscription Plan: {{ subscriptionPlan }}</p>
            <v-btn
              class="bg-amber-500 hover:bg-amber-600 text-white"
              @click="manageSubscription"
            >
              Manage Subscription
            </v-btn>
          </section>

          <!-- Appearance -->
          <section v-if="activeTab === 'appearance'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Appearance</h1>
            <div class="theme-selection">
              <label class="text-amber-800">Themes:</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input
                    type="radio"
                    value="light"
                    v-model="selectedTheme"
                    class="text-amber-600"
                  />
                  Light Mode
                </label>
                <label class="flex items-center gap-2">
                  <input
                    type="radio"
                    value="dark"
                    v-model="selectedTheme"
                    class="text-amber-600"
                  />
                  Dark Mode
                </label>
              </div>
            </div>
            <div class="font-size-selection">
              <label class="text-amber-800">Font Size:</label>
              <input
                type="range"
                v-model.number="fontSize"
                min="12"
                max="24"
                class="w-full"
              />
            </div>
          </section>

          <!-- Accessibility -->
          <section v-if="activeTab === 'accessibility'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Accessibility</h1>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="reducedMotion"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Reduce Motion</span>
            </label>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="highContrast"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">High Contrast Mode</span>
            </label>
          </section>

          <!-- App Notifications -->
          <section v-if="activeTab === 'appNotifications'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Notifications</h1>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="notificationsEnabled"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Enable Notifications</span>
            </label>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="appEmailNotifications"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Email Notifications</span>
            </label>
          </section>

          <!-- Voice & Video -->
          <section v-if="activeTab === 'voice'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Voice & Video</h1>
            <label class="text-amber-800">Microphone Volume:</label>
            <input
              type="range"
              v-model.number="micVolume"
              min="0"
              max="100"
              class="w-full"
            />
            <label class="text-amber-800">Speaker Volume:</label>
            <input
              type="range"
              v-model.number="speakerVolume"
              min="0"
              max="100"
              class="w-full"
            />
          </section>

          <!-- Text & Images -->
          <section v-if="activeTab === 'text'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Text & Images</h1>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="showEmbeds"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Show Embeds</span>
            </label>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="autoPlayGifs"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Auto-play GIFs</span>
            </label>
            <div v-if="showEmbeds" class="p-4 bg-gray-100 rounded-lg">
              <p class="text-amber-600">This is a sample embed content</p>
            </div>
            <div v-if="autoPlayGifs" class="p-4 bg-gray-100 rounded-lg">
              <p class="text-amber-600">Sample GIF would play here</p>
            </div>
          </section>

          <!-- Security -->
          <section v-if="activeTab === 'security'" class="space-y-4">
            <h1 class="text-2xl font-bold text-amber-800">Security</h1>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="twoFactorAuth"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Enable Two-Factor Authentication</span>
            </label>
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="loginAlerts"
                class="h-4 w-4 text-amber-600 rounded"
              />
              <span class="text-amber-800">Login Alerts</span>
            </label>
            <div v-if="twoFactorAuth" class="p-4 bg-gray-100 rounded-lg">
              <p class="text-amber-600">2FA is enabled</p>
            </div>
            <div v-if="loginAlerts" class="p-4 bg-gray-100 rounded-lg">
              <p class="text-amber-600">Login alerts are active</p>
            </div>
          </section>
        </div>
      </div>
    </div>

    <!-- Development Warning Snackbar -->
    <v-snackbar
      v-model="showDevWarning"
      :timeout="-1"
      color="black"
      class="text-white fixed bottom-4 right-4 z-50 max-w-xs"
    >
      <v-icon color="yellow" class="mr-2">mdi-alert</v-icon>
      Many features are in development and may not function as expected.
    </v-snackbar>

    <!-- Logout Confirmation Modal -->
    <v-dialog v-model="showLogoutModal" max-width="500">
      <v-card class="rounded-xl">
        <v-card-title class="text-amber-800 font-bold">Confirm Logout</v-card-title>
        <v-card-text class="text-amber-600">Are you sure you want to log out?</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="red"
            text
            class="hover:bg-red-100"
            @click="showLogoutModal = false"
          >
            No
          </v-btn>
          <v-btn
            color="amber"
            text
            class="hover:bg-amber-100"
            @click="logout"
          >
            Yes
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Add Social Media Modal -->
    <v-dialog v-model="showAddSocialMediaModal" max-width="500">
      <v-card class="rounded-xl">
        <v-card-title class="text-amber-800 font-bold">
          {{ editMode ? 'Edit' : 'Add' }} Social Media
        </v-card-title>
        <v-card-text>
          <v-form @submit.prevent="editMode ? updateSocialMedia() : addSocialMedia">
            <v-select
              v-model="newSocialMedia.platform"
              :items="socialMediaPlatforms"
              label="Select Platform"
              class="bg-gray-100 rounded-lg focus:ring-amber-500"
              required
            />
            <v-text-field
              v-model="newSocialMedia.username"
              label="Username"
              class="bg-gray-100 rounded-lg focus:ring-amber-500"
              required
            />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="amber"
            text
            class="hover:bg-amber-100"
            @click="showAddSocialMediaModal = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="amber"
            text
            class="hover:bg-amber-100"
            @click="editMode ? updateSocialMedia() : addSocialMedia"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue';
import AppFooter from '../components/AppFooter.vue';
import {
  UserIcon,
  UserCircleIcon,
  DevicePhoneMobileIcon,
  ShieldCheckIcon,
  BellIcon,
  LinkIcon,
  CreditCardIcon,
  PaintBrushIcon,
  EyeIcon,
  MicrophoneIcon,
  ChatBubbleLeftIcon,
  LockClosedIcon,
  ArrowRightOnRectangleIcon,
  PencilIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline';
import apiClient from '@/axios.js';
import { ref, watch, onMounted } from 'vue';

export default {
  name: 'SettingsPage',
  components: {
    NavBar,
    AppFooter,
    UserIcon,
    UserCircleIcon,
    DevicePhoneMobileIcon,
    ShieldCheckIcon,
    BellIcon,
    LinkIcon,
    CreditCardIcon,
    PaintBrushIcon,
    EyeIcon,
    MicrophoneIcon,
    ChatBubbleLeftIcon,
    LockClosedIcon,
    ArrowRightOnRectangleIcon,
    PencilIcon,
    PlusIcon,
  },
  setup() {
    const activeTab = ref('myAccount');
    const name = ref('');
    const email = ref('');
    const profilePicture = ref('');
    const devices = ref([]);
    const twoFactorAuth = ref(localStorage.getItem('twoFactorAuth') === 'true');
    const lastLoginIP = ref('');
    const lastLoginDate = ref('');
    const accountEmailNotifications = ref(localStorage.getItem('accountEmailNotifications') === 'true');
    const pushNotifications = ref(localStorage.getItem('pushNotifications') === 'true');
    const linkedAccounts = ref([]);
    const subscriptionPlan = ref('');
    const showLogoutModal = ref(false);
    const showAddSocialMediaModal = ref(false);
    const showDevWarning = ref(false);
    const newSocialMedia = ref({ platform: '', username: '' });
    const socialMediaPlatforms = ref(['Facebook', 'Instagram', 'Twitter', 'Discord']);
    const socialMediaLogos = ref({
      Facebook: 'path/to/facebook-logo.png',
      Instagram: 'path/to/instagram-logo.png',
      Twitter: 'path/to/twitter-logo.png',
      Discord: 'path/to/discord-logo.png',
    });
    const editMode = ref(false);
    const currentEditingIndex = ref(null);
    const selectedTheme = ref(localStorage.getItem('theme') || 'light');
    const fontSize = ref(Number(localStorage.getItem('fontSize')) || 16);
    const reducedMotion = ref(localStorage.getItem('reducedMotion') === 'true');
    const highContrast = ref(localStorage.getItem('highContrast') === 'true');
    const notificationsEnabled = ref(localStorage.getItem('notificationsEnabled') === 'true');
    const appEmailNotifications = ref(localStorage.getItem('appEmailNotifications') === 'true');
    const micVolume = ref(Number(localStorage.getItem('micVolume')) || 50);
    const speakerVolume = ref(Number(localStorage.getItem('speakerVolume') || 50));
    const showEmbeds = ref(localStorage.getItem('showEmbeds') !== 'false');
    const autoPlayGifs = ref(localStorage.getItem('autoPlayGifs') === 'true');
    const loginAlerts = ref(localStorage.getItem('loginAlerts') === 'true');

    const accountTabs = [
      { id: 'myAccount', label: 'My Account', icon: UserIcon },
      { id: 'profiles', label: 'Profiles', icon: UserCircleIcon },
      { id: 'devices', label: 'Devices', icon: DevicePhoneMobileIcon },
      { id: 'privacy', label: 'Privacy & Safety', icon: ShieldCheckIcon },
      { id: 'accountNotifications', label: 'Notifications', icon: BellIcon },
      { id: 'connections', label: 'Connections', icon: LinkIcon },
      { id: 'billing', label: 'Billing', icon: CreditCardIcon },
    ];

    const appTabs = [
      { id: 'appearance', label: 'Appearance', icon: PaintBrushIcon },
      { id: 'accessibility', label: 'Accessibility', icon: EyeIcon },
      { id: 'appNotifications', label: 'Notifications', icon: BellIcon },
      { id: 'voice', label: 'Voice & Video', icon: MicrophoneIcon },
      { id: 'text', label: 'Text & Images', icon: ChatBubbleLeftIcon },
      { id: 'security', label: 'Security', icon: LockClosedIcon },
    ];

    onMounted(() => {
      fetchUserDetails();
      applySettings();
      showDevWarning.value = true; // Show the warning snackbar on mount
    });

    // Watch and apply settings
    watch(selectedTheme, (newTheme) => {
      localStorage.setItem('theme', newTheme);
      document.documentElement.classList.toggle('dark', newTheme === 'dark');
    });

    watch(fontSize, (newSize) => {
      localStorage.setItem('fontSize', newSize);
      document.documentElement.style.setProperty('--font-size', `${newSize}px`);
    });

    watch([reducedMotion, highContrast], () => {
      localStorage.setItem('reducedMotion', reducedMotion.value);
      localStorage.setItem('highContrast', highContrast.value);
      document.documentElement.classList.toggle('reduced-motion', reducedMotion.value);
      document.documentElement.classList.toggle('high-contrast', highContrast.value);
    });

    watch([notificationsEnabled, appEmailNotifications], () => {
      localStorage.setItem('notificationsEnabled', notificationsEnabled.value);
      localStorage.setItem('appEmailNotifications', appEmailNotifications.value);
      document.documentElement.classList.toggle('notifications-enabled', notificationsEnabled.value);
      document.documentElement.classList.toggle('app-email-notifications', appEmailNotifications.value);
    });

    watch([micVolume, speakerVolume], () => {
      localStorage.setItem('micVolume', micVolume.value);
      localStorage.setItem('speakerVolume', speakerVolume.value);
    });

    watch([showEmbeds, autoPlayGifs], () => {
      localStorage.setItem('showEmbeds', showEmbeds.value);
      localStorage.setItem('autoPlayGifs', autoPlayGifs.value);
    });

    watch([twoFactorAuth, loginAlerts], () => {
      localStorage.setItem('twoFactorAuth', twoFactorAuth.value);
      localStorage.setItem('loginAlerts', loginAlerts.value);
    });

    watch([accountEmailNotifications, pushNotifications], () => {
      localStorage.setItem('accountEmailNotifications', accountEmailNotifications.value);
      localStorage.setItem('pushNotifications', pushNotifications.value);
    });

    function applySettings() {
      document.documentElement.classList.toggle('dark', selectedTheme.value === 'dark');
      document.documentElement.style.setProperty('--font-size', `${fontSize.value}px`);
      document.documentElement.classList.toggle('reduced-motion', reducedMotion.value);
      document.documentElement.classList.toggle('high-contrast', highContrast.value);
      document.documentElement.classList.toggle('notifications-enabled', notificationsEnabled.value);
      document.documentElement.classList.toggle('app-email-notifications', appEmailNotifications.value);
    }

    function fetchUserDetails() {
      apiClient
        .get('/api/user')
        .then((response) => {
          const user = response.data;
          name.value = user.name;
          email.value = user.email;
          profilePicture.value = user.profilePicture;
          devices.value = user.devices;
          twoFactorAuth.value = user.twoFactorAuth;
          linkedAccounts.value = user.linkedAccounts;
          subscriptionPlan.value = user.subscriptionPlan;
          lastLoginIP.value = user.lastLoginIP;
          lastLoginDate.value = user.lastLoginDate;
        })
        .catch((error) => {
          console.error('Error fetching user details:', error);
        });
    }

    function updateUserInfo() {
      // Call API to update user info
    }

    function removeDevice(deviceId) {
      devices.value = devices.value.filter((device) => device.id !== deviceId);
    }

    function changePassword() {
      // Implement change password logic
    }

    function uploadProfilePicture() {
      // Implement upload profile picture logic
    }

    function manageSubscription() {
      // Implement manage subscription logic
    }

    function logout() {
      apiClient
        .post('/api/logout')
        .then(() => {
          window.location.href = '/login';
        })
        .catch((error) => {
          console.error('Error logging out:', error);
        });
    }

    function addSocialMedia() {
      const logo = socialMediaLogos.value[newSocialMedia.value.platform];
      linkedAccounts.value.push({ ...newSocialMedia.value, logo, id: Date.now() });
      resetForm();
      showAddSocialMediaModal.value = false;
    }

    function updateSocialMedia() {
      const logo = socialMediaLogos.value[newSocialMedia.value.platform];
      const updatedAccount = {
        ...newSocialMedia.value,
        logo,
        id: linkedAccounts.value[currentEditingIndex.value].id,
      };
      linkedAccounts.value.splice(currentEditingIndex.value, 1, updatedAccount);
      resetForm();
      showAddSocialMediaModal.value = false;
    }

    function resetForm() {
      newSocialMedia.value.platform = '';
      newSocialMedia.username = '';
      editMode.value = false;
      currentEditingIndex.value = null;
    }

    function editConnection(account, index) {
      newSocialMedia.value = { ...account };
      editMode.value = true;
      currentEditingIndex.value = index;
      showAddSocialMediaModal.value = true;
    }

    return {
      activeTab,
      name,
      email,
      profilePicture,
      devices,
      twoFactorAuth,
      lastLoginIP,
      lastLoginDate,
      accountEmailNotifications,
      pushNotifications,
      linkedAccounts,
      subscriptionPlan,
      showLogoutModal,
      showAddSocialMediaModal,
      showDevWarning,
      newSocialMedia,
      socialMediaPlatforms,
      editMode,
      currentEditingIndex,
      selectedTheme,
      fontSize,
      reducedMotion,
      highContrast,
      notificationsEnabled,
      appEmailNotifications,
      micVolume,
      speakerVolume,
      showEmbeds,
      autoPlayGifs,
      loginAlerts,
      accountTabs,
      appTabs,
      fetchUserDetails,
      updateUserInfo,
      removeDevice,
      changePassword,
      uploadProfilePicture,
      manageSubscription,
      logout,
      addSocialMedia,
      updateSocialMedia,
      resetForm,
      editConnection,
    };
  },
};
</script>

<style scoped>
:root {
  --font-size: 16px;
}

body {
  font-size: var(--font-size);
}

.dark {
  background: linear-gradient(to bottom right, #1a1d21, #2c2f33);
}

.dark .bg-white {
  background: #2c2f33;
  color: white;
}

.dark .bg-gray-100 {
  background: #4f545c;
}

.dark .text-amber-800 {
  color: #f59e0b;
}

.dark .text-amber-600 {
  color: #d97706;
}

.reduced-motion {
  transition: none !important;
  animation: none !important;
}

.high-contrast {
  filter: contrast(1.2);
}

.notifications-enabled::after {
  content: 'Notifications Enabled';
  position: fixed;
  bottom: 10px;
  right: 10px;
  padding: 5px 10px;
  background: #4f545c;
  color: white;
  border-radius: 3px;
}

.app-email-notifications::after {
  content: 'Email Notifications Enabled';
  position: fixed;
  bottom: 40px;
  right: 10px;
  padding: 5px 10px;
  background: #4f545c;
  color: white;
  border-radius: 3px;
}
</style>
