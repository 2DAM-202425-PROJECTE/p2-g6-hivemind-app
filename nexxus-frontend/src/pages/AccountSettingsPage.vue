<template>
  <div :class="{ 'dark-mode': darkMode }" class="account-settings-container">
    <NavBar />
    <div class="settings-sidebar">
      <ul>
        <li :class="{ active: activeTab === 'myAccount' }" @click="activeTab = 'myAccount'">My Account</li>
        <li :class="{ active: activeTab === 'profiles' }" @click="activeTab = 'profiles'">Profiles</li>
        <li :class="{ active: activeTab === 'devices' }" @click="activeTab = 'devices'">Devices</li>
        <li :class="{ active: activeTab === 'privacy' }" @click="activeTab = 'privacy'">Privacy & Safety</li>
        <li :class="{ active: activeTab === 'notifications' }" @click="activeTab = 'notifications'">Notifications</li>
        <li :class="{ active: activeTab === 'connections' }" @click="activeTab = 'connections'">Connections</li>
        <li :class="{ active: activeTab === 'billing' }" @click="activeTab = 'billing'">Billing</li>
        <li :class="{ active: activeTab === 'logout' }" @click="showLogoutModal = true">Log Out</li>
      </ul>
      <v-btn @click="toggleDarkMode">Toggle Dark Mode</v-btn>
    </div>
    <div class="account-settings-content container">
      <section v-if="activeTab === 'myAccount'" class="account-info-section">
        <h1 class="section-title">My Account</h1>
        <v-text-field v-model="name" label="Name" @blur="updateUserInfo" />
        <v-text-field v-model="email" label="Email" @blur="updateUserInfo" />
        <v-img :src="profilePicture" class="profile-picture"></v-img>
        <v-btn color="primary" @click="changePassword">Change Password</v-btn>
      </section>
      <section v-if="activeTab === 'profiles'" class="profile-section">
        <h1 class="section-title">Profiles</h1>
        <p>Profile Picture:</p>
        <v-img :src="profilePicture" class="profile-picture"></v-img>
        <v-btn color="primary" @click="uploadProfilePicture">Upload New Picture</v-btn>
      </section>
      <section v-if="activeTab === 'devices'" class="devices-section">
        <h1 class="section-title">Devices</h1>
        <ul>
          <li v-for="device in devices" :key="device.id">
            {{ device.name }} - Last active: {{ device.lastActive }}
            <v-btn color="red" small @click="removeDevice(device.id)">Remove</v-btn>
          </li>
        </ul>
      </section>
      <section v-if="activeTab === 'privacy'" class="privacy-section">
        <h1 class="section-title">Privacy & Safety</h1>
        <p>Last Login: {{ lastLoginIP }} on {{ lastLoginDate }}</p>
        <label>
          <input type="checkbox" v-model="twoFactorAuth"> Enable Two-Factor Authentication
        </label>
      </section>
      <section v-if="activeTab === 'notifications'" class="notifications-section">
        <h1 class="section-title">Notification Preferences</h1>
        <div class="notifications-container">
          <v-checkbox v-model="emailNotifications" label="Email Notifications" />
          <v-checkbox v-model="pushNotifications" label="Push Notifications" />
        </div>
      </section>
      <section v-if="activeTab === 'connections'" class="connections-section">
        <h1 class="section-title">Connections</h1>
        <p>Linked Accounts:</p>
        <ul>
          <li v-for="(account, index) in linkedAccounts" :key="account.id">
            <v-img :src="account.logo" class="social-media-logo"></v-img>
            {{ account.username }}
            <v-btn icon @click="editConnection(account, index)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </li>
        </ul>
        <v-btn icon @click="showAddSocialMediaModal = true" style="margin-top: 20px;">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </section>
      <section v-if="activeTab === 'billing'" class="billing-section">
        <h1 class="section-title">Billing</h1>
        <p>Subscription Plan: {{ subscriptionPlan }}</p>
        <v-btn color="primary" @click="manageSubscription">Manage Subscription</v-btn>
      </section>
    </div>
    <AppFooter />
    <!-- Logout Confirmation Modal -->
    <v-dialog v-model="showLogoutModal" max-width="500">
      <v-card>
        <v-card-title class="headline">Confirm Logout</v-card-title>
        <v-card-text>Are you sure you want to log out?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" text @click="showLogoutModal = false">No</v-btn>
          <v-btn color="blue darken-1" text @click="logout">Yes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Add Social Media Modal -->
    <v-dialog v-model="showAddSocialMediaModal" max-width="500">
      <v-card>
        <v-card-title class="headline">{{ editMode ? 'Edit' : 'Add' }} Social Media</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="editMode ? updateSocialMedia() : addSocialMedia">
            <v-select
              v-model="newSocialMedia.platform"
              :items="socialMediaPlatforms"
              label="Select Platform"
              required
            ></v-select>
            <v-text-field v-model="newSocialMedia.username" label="Username" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="showAddSocialMediaModal = false">Cancel</v-btn>
          <v-btn color="blue darken-1" text @click="editMode ? updateSocialMedia() : addSocialMedia">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue';
import AppFooter from '../components/AppFooter.vue';
import apiClient from "@/axios.js";

export default {
  name: 'AccountSettingsPage',
  components: {
    NavBar,
    AppFooter,
  },
  data() {
    return {
      activeTab: 'myAccount',
      name: '',
      email: '',
      profilePicture: '',
      devices: [],
      twoFactorAuth: false,
      lastLoginIP: '',
      lastLoginDate: '',
      emailNotifications: true,
      pushNotifications: true,
      linkedAccounts: [],
      subscriptionPlan: '',
      showLogoutModal: false,
      showAddSocialMediaModal: false,
      newSocialMedia: {
        platform: '',
        username: ''
      },
      socialMediaPlatforms: ['Facebook', 'Instagram', 'Twitter', 'Discord'],
      socialMediaLogos: {
        Facebook: 'path/to/facebook-logo.png',
        Instagram: 'path/to/instagram-logo.png',
        Twitter: 'path/to/twitter-logo.png',
        Discord: 'path/to/discord-logo.png'
      },
      editMode: false,
      currentEditingIndex: null,
      darkMode: localStorage.getItem('darkMode') === 'true'
    };
  },
  methods: {
    fetchUserDetails() {
      apiClient.get('/api/user')
        .then(response => {
          const user = response.data;
          this.name = user.name;
          this.email = user.email;
          this.profilePicture = user.profilePicture;
          this.devices = user.devices;
          this.twoFactorAuth = user.twoFactorAuth;
          this.linkedAccounts = user.linkedAccounts;
          this.subscriptionPlan = user.subscriptionPlan;
          this.lastLoginIP = user.lastLoginIP;
          this.lastLoginDate = user.lastLoginDate;
        })
        .catch(error => {
          console.error('Error fetching user details:', error);
        });
    },
    updateUserInfo() {
      // Call API to update user info
    },
    removeDevice(deviceId) {
      this.devices = this.devices.filter(device => device.id !== deviceId);
    },
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('darkMode', this.darkMode);
    },
    changePassword() {
      // Implement change password logic here
    },
    uploadProfilePicture() {
      // Implement upload profile picture logic here
    },
    manageSubscription() {
      // Implement manage subscription logic here
    },
    logout() {
      apiClient.post('/api/logout')
        .then(() => {
          window.location.href = '/login';
        })
        .catch(error => {
          console.error('Error logging out:', error);
        });
    },
    addSocialMedia() {
      const logo = this.socialMediaLogos[this.newSocialMedia.platform];
      this.linkedAccounts.push({ ...this.newSocialMedia, logo, id: Date.now() });
      this.resetForm();
      this.showAddSocialMediaModal = false;
    },
    updateSocialMedia() {
      const logo = this.socialMediaLogos[this.newSocialMedia.platform];
      const updatedAccount = { ...this.newSocialMedia, logo, id: this.linkedAccounts[this.currentEditingIndex].id };

      this.linkedAccounts.splice(this.currentEditingIndex, 1, updatedAccount);

      this.resetForm();
      this.showAddSocialMediaModal = false;
    },
    resetForm() {
      this.newSocialMedia.platform = '';
      this.newSocialMedia.username = '';
      this.editMode = false;
      this.currentEditingIndex = null;
    },
    editConnection(account, index) {
      this.newSocialMedia = { ...account };
      this.editMode = true;
      this.currentEditingIndex = index;
      this.showAddSocialMediaModal = true;
    }
  },
  mounted() {
    this.fetchUserDetails();
  },
};
</script>

<style scoped>
.account-settings-container {
  display: flex;
  min-height: 100vh;
  background-color: #f0f2f5;
  padding-top: 60px;
}
.notifications-container {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  color: black;
}
.settings-sidebar {
  width: 250px;
  background: #2c2f33;
  padding: 20px;
  color: white;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.settings-sidebar ul {
  list-style: none;
  padding: 0;
}

.settings-sidebar li {
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 5px;
  margin-bottom: 5px;
  transition: 0.2s;
}

.settings-sidebar li:hover, .settings-sidebar li.active {
  background: #4f545c;
}

.logout {
  background: #ff4d4d;
  color: white;
}

.logout:hover {
  background: #ff3333;
}

.account-settings-content {
  flex: 1;
  padding: 2rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #000;
}

.account-info-section, .profile-section, .devices-section, .privacy-section, .connections-section, .billing-section {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  color: black;
}

.profile-picture {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 1rem;
}

.social-media-logo {
  width: 24px;
  height: 24px;
  margin-right: 8px;
  vertical-align: middle;
}

.dark-mode {
  background-color: #121212;
  color: white;
}
</style>
