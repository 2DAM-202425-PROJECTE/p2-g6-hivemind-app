<template>
  <div class="account-settings-container">
    <NavBar />
    <div class="settings-sidebar">
      <ul>
        <li :class="{ active: activeTab === 'myAccount' }" @click="activeTab = 'myAccount'">My Account</li>
        <li :class="{ active: activeTab === 'profiles' }" @click="activeTab = 'profiles'">Profiles</li>
        <li :class="{ active: activeTab === 'devices' }" @click="activeTab = 'devices'">Devices</li>
        <li :class="{ active: activeTab === 'privacy' }" @click="activeTab = 'privacy'">Privacy & Safety</li>
        <li :class="{ active: activeTab === 'connections' }" @click="activeTab = 'connections'">Connections</li>
        <li :class="{ active: activeTab === 'billing' }" @click="activeTab = 'billing'">Billing</li>
        <li :class="{ active: activeTab === 'hypesquad' }" @click="activeTab = 'hypesquad'">HypeSquad</li>
        <li :class="{ active: activeTab === 'logout' }" @click="logout">Log Out</li>
      </ul>
    </div>
    <div class="account-settings-content container">
      <section v-if="activeTab === 'myAccount'" class="account-info-section">
        <h1 class="section-title">My Account</h1>
        <p>Username: {{ username }}</p>
        <p>Email: {{ email }}</p>
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
          <li v-for="device in devices" :key="device.id">{{ device.name }} - {{ device.lastActive }}</li>
        </ul>
      </section>
      <section v-if="activeTab === 'privacy'" class="privacy-section">
        <h1 class="section-title">Privacy & Safety</h1>
        <label>
          <input type="checkbox" v-model="twoFactorAuth"> Enable Two-Factor Authentication
        </label>
      </section>
      <section v-if="activeTab === 'connections'" class="connections-section">
        <h1 class="section-title">Connections</h1>
        <p>Linked Accounts:</p>
        <ul>
          <li v-for="account in linkedAccounts" :key="account.id">{{ account.platform }} - {{ account.username }}</li>
        </ul>
      </section>
      <section v-if="activeTab === 'billing'" class="billing-section">
        <h1 class="section-title">Billing</h1>
        <p>Subscription Plan: {{ subscriptionPlan }}</p>
        <v-btn color="primary" @click="manageSubscription">Manage Subscription</v-btn>
      </section>
      <section v-if="activeTab === 'hypesquad'" class="hypesquad-section">
        <h1 class="section-title">HypeSquad</h1>
        <p>Current House: {{ hypesquadHouse }}</p>
        <v-btn color="primary" @click="changeHouse">Change House</v-btn>
      </section>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue';
import AppFooter from '../components/AppFooter.vue';

export default {
  name: 'AccountSettingsPage',
  components: {
    NavBar,
    AppFooter,
  },
  data() {
    return {
      activeTab: 'myAccount', // Default to My Account tab
      username: 'JohnDoe',
      email: 'john.doe@example.com',
      profilePicture: 'path/to/profile-picture.jpg',
      devices: [
        { id: 1, name: 'Laptop', lastActive: '2023-10-01' },
        { id: 2, name: 'Phone', lastActive: '2023-10-02' },
      ],
      twoFactorAuth: false,
      linkedAccounts: [
        { id: 1, platform: 'GitHub', username: 'john_doe' },
        { id: 2, platform: 'Twitter', username: 'johndoe' },
      ],
      subscriptionPlan: 'Pro',
      hypesquadHouse: 'Bravery',
    };
  },
  methods: {
    changePassword() {
      // Implement change password logic here
    },
    uploadProfilePicture() {
      // Implement upload profile picture logic here
    },
    manageSubscription() {
      // Implement manage subscription logic here
    },
    changeHouse() {
      // Implement change house logic here
    },
    logout() {
      // Implement logout logic here
    },
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

.settings-sidebar {
  width: 250px;
  background: #2c2f33;
  padding: 20px;
  color: white;
  min-height: 100vh;
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

.account-info-section, .profile-section, .devices-section, .privacy-section, .connections-section, .billing-section, .hypesquad-section {
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
</style>
