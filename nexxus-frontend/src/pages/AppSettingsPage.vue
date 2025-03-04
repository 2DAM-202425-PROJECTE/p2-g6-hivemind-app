<template>
  <div class="settings-container">
    <NavBar />
    <div class="settings-sidebar">
      <ul>
        <li :class="{ active: selectedTab === 'appearance' }" @click="selectedTab = 'appearance'">Appearance</li>
        <li :class="{ active: selectedTab === 'accessibility' }" @click="selectedTab = 'accessibility'">Accessibility</li>
        <li :class="{ active: selectedTab === 'notifications' }" @click="selectedTab = 'notifications'">Notifications</li>
        <li :class="{ active: selectedTab === 'voice' }" @click="selectedTab = 'voice'">Voice & Video</li>
        <li :class="{ active: selectedTab === 'text' }" @click="selectedTab = 'text'">Text & Images</li>
        <li :class="{ active: selectedTab === 'security' }" @click="selectedTab = 'security'">Security</li>
      </ul>
    </div>
    <div class="settings-content container">
      <section v-if="selectedTab === 'appearance'" class="settings-section">
        <h1 class="section-title">Appearance</h1>
        <div class="theme-selection">
          <label>Themes:</label>
          <input type="radio" id="light-mode" value="light" v-model="selectedTheme">
          <label for="light-mode">Light Mode</label>
          <input type="radio" id="dark-mode" value="dark" v-model="selectedTheme">
          <label for="dark-mode">Dark Mode</label>
        </div>
      </section>
      <section v-if="selectedTab === 'accessibility'" class="settings-section">
        <h1 class="section-title">Accessibility</h1>
        <label>
          <input type="checkbox" v-model="reducedMotion"> Reduce Motion
        </label>
      </section>
      <section v-if="selectedTab === 'notifications'" class="settings-section">
        <h1 class="section-title">Notifications</h1>
        <label>
          <input type="checkbox" v-model="notificationsEnabled"> Enable Notifications
        </label>
      </section>
      <section v-if="selectedTab === 'voice'" class="settings-section">
        <h1 class="section-title">Voice & Video</h1>
        <label>Microphone Volume:</label>
        <input type="range" v-model="micVolume" min="0" max="100">
      </section>
      <section v-if="selectedTab === 'text'" class="settings-section">
        <h1 class="section-title">Text & Images</h1>
        <label>
          <input type="checkbox" v-model="showEmbeds"> Show Embeds
        </label>
      </section>
      <section v-if="selectedTab === 'security'" class="settings-section">
        <h1 class="section-title">Security</h1>
        <label>
          <input type="checkbox" v-model="twoFactorAuth"> Enable Two-Factor Authentication
        </label>
      </section>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'

export default {
  name: 'AppSettingsPage',
  components: { NavBar, AppFooter },
  data() {
    return {
      selectedTab: 'appearance',
      selectedTheme: 'light',
      reducedMotion: false,
      notificationsEnabled: false,
      micVolume: 50,
      showEmbeds: true,
      twoFactorAuth: false,
    }
  }
}
</script>

<style scoped>
.settings-container {
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

.settings-content {
  flex: 1;
  padding: 2rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #000;
}

.settings-section {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  color: black;
}
</style>
