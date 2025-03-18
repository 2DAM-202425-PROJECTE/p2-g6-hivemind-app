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
        <div class="font-size-selection">
          <label>Font Size:</label>
          <input type="range" v-model.number="fontSize" min="12" max="24">
        </div>
      </section>
      <section v-if="selectedTab === 'accessibility'" class="settings-section">
        <h1 class="section-title">Accessibility</h1>
        <label>
          <input type="checkbox" v-model="reducedMotion"> Reduce Motion
        </label>
        <label>
          <input type="checkbox" v-model="highContrast"> High Contrast Mode
        </label>
      </section>
      <section v-if="selectedTab === 'notifications'" class="settings-section">
        <h1 class="section-title">Notifications</h1>
        <label>
          <input type="checkbox" v-model="notificationsEnabled"> Enable Notifications
        </label>
        <label>
          <input type="checkbox" v-model="emailNotifications"> Email Notifications
        </label>
      </section>
      <section v-if="selectedTab === 'voice'" class="settings-section">
        <h1 class="section-title">Voice & Video</h1>
        <label>Microphone Volume:</label>
        <input type="range" v-model.number="micVolume" min="0" max="100">
        <label>Speaker Volume:</label>
        <input type="range" v-model.number="speakerVolume" min="0" max="100">
      </section>
      <section v-if="selectedTab === 'text'" class="settings-section">
        <h1 class="section-title">Text & Images</h1>
        <label>
          <input type="checkbox" v-model="showEmbeds"> Show Embeds
        </label>
        <label>
          <input type="checkbox" v-model="autoPlayGifs"> Auto-play GIFs
        </label>
        <div v-if="showEmbeds" class="embed-preview">
          <p>This is a sample embed content</p>
        </div>
        <div v-if="autoPlayGifs" class="gif-preview">
          <p>Sample GIF would play here</p>
        </div>
      </section>
      <section v-if="selectedTab === 'security'" class="settings-section">
        <h1 class="section-title">Security</h1>
        <label>
          <input type="checkbox" v-model="twoFactorAuth"> Enable Two-Factor Authentication
        </label>
        <label>
          <input type="checkbox" v-model="loginAlerts"> Login Alerts
        </label>
        <div v-if="twoFactorAuth" class="security-info">
          <p>2FA is enabled</p>
        </div>
        <div v-if="loginAlerts" class="security-info">
          <p>Login alerts are active</p>
        </div>
      </section>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'
import { ref, watch, onMounted } from "vue";

export default {
  name: 'AppSettingsPage',
  components: { NavBar, AppFooter },
  setup() {
    const selectedTab = ref('appearance')
    const selectedTheme = ref(localStorage.getItem('theme') || 'light')
    const fontSize = ref(Number(localStorage.getItem('fontSize')) || 16)
    const reducedMotion = ref(localStorage.getItem('reducedMotion') === 'true')
    const highContrast = ref(localStorage.getItem('highContrast') === 'true')
    const notificationsEnabled = ref(localStorage.getItem('notificationsEnabled') === 'true')
    const emailNotifications = ref(localStorage.getItem('emailNotifications') === 'true')
    const micVolume = ref(Number(localStorage.getItem('micVolume')) || 50)
    const speakerVolume = ref(Number(localStorage.getItem('speakerVolume')) || 50)
    const showEmbeds = ref(localStorage.getItem('showEmbeds') !== 'false')
    const autoPlayGifs = ref(localStorage.getItem('autoPlayGifs') === 'true')
    const twoFactorAuth = ref(localStorage.getItem('twoFactorAuth') === 'true')
    const loginAlerts = ref(localStorage.getItem('loginAlerts') === 'true')

    onMounted(() => {
      applySettings()
    })

    // Watch and apply settings
    watch(selectedTheme, (newTheme) => {
      localStorage.setItem('theme', newTheme)
      document.documentElement.classList.toggle('dark', newTheme === 'dark')
    })

    watch(fontSize, (newSize) => {
      localStorage.setItem('fontSize', newSize)
      document.documentElement.style.setProperty('--font-size', `${newSize}px`)
    })

    watch([reducedMotion, highContrast], () => {
      localStorage.setItem('reducedMotion', reducedMotion.value)
      localStorage.setItem('highContrast', highContrast.value)
      document.documentElement.classList.toggle('reduced-motion', reducedMotion.value)
      document.documentElement.classList.toggle('high-contrast', highContrast.value)
    })

    watch([notificationsEnabled, emailNotifications], () => {
      localStorage.setItem('notificationsEnabled', notificationsEnabled.value)
      localStorage.setItem('emailNotifications', emailNotifications.value)
      document.documentElement.classList.toggle('notifications-enabled', notificationsEnabled.value)
      document.documentElement.classList.toggle('email-notifications', emailNotifications.value)
    })

    watch([micVolume, speakerVolume], () => {
      localStorage.setItem('micVolume', micVolume.value)
      localStorage.setItem('speakerVolume', speakerVolume.value)
    })

    watch([showEmbeds, autoPlayGifs], () => {
      localStorage.setItem('showEmbeds', showEmbeds.value)
      localStorage.setItem('autoPlayGifs', autoPlayGifs.value)
    })

    watch([twoFactorAuth, loginAlerts], () => {
      localStorage.setItem('twoFactorAuth', twoFactorAuth.value)
      localStorage.setItem('loginAlerts', loginAlerts.value)
    })

    function applySettings() {
      document.documentElement.classList.toggle('dark', selectedTheme.value === 'dark')
      document.documentElement.style.setProperty('--font-size', `${fontSize.value}px`)
      document.documentElement.classList.toggle('reduced-motion', reducedMotion.value)
      document.documentElement.classList.toggle('high-contrast', highContrast.value)
      document.documentElement.classList.toggle('notifications-enabled', notificationsEnabled.value)
      document.documentElement.classList.toggle('email-notifications', emailNotifications.value)
    }

    return {
      selectedTab,
      selectedTheme,
      fontSize,
      reducedMotion,
      highContrast,
      notificationsEnabled,
      emailNotifications,
      micVolume,
      speakerVolume,
      showEmbeds,
      autoPlayGifs,
      twoFactorAuth,
      loginAlerts
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

.settings-sidebar li:hover,
.settings-sidebar li.active {
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

.theme-selection {
  margin-bottom: 1rem;
}

.theme-selection label {
  margin-right: 1rem;
}

.font-size-selection {
  margin-bottom: 1rem;
}

.settings-section label {
  display: block;
  margin-bottom: 0.5rem;
}

/* Additional styles for functional settings */
:root {
  --font-size: 16px;
}

body {
  font-size: var(--font-size);
}

.dark {
  background-color: #1a1d21;
}

.dark .settings-container {
  background-color: #1a1d21;
}

.dark .settings-section {
  background: #2c2f33;
  color: white;
}

.reduced-motion {
  transition: none !important;
  animation: none !important;
}

.high-contrast {
  filter: contrast(1.2);
}

.notifications-enabled::after {
  content: "Notifications Enabled";
  position: fixed;
  bottom: 10px;
  right: 10px;
  padding: 5px 10px;
  background: #4f545c;
  color: white;
  border-radius: 3px;
}

.email-notifications::after {
  content: "Email Notifications Enabled";
  position: fixed;
  bottom: 40px;
  right: 10px;
  padding: 5px 10px;
  background: #4f545c;
  color: white;
  border-radius: 3px;
}

.embed-preview,
.gif-preview,
.security-info {
  margin-top: 1rem;
  padding: 1rem;
  background: #f0f2f5;
  border-radius: 4px;
}

.dark .embed-preview,
.dark .gif-preview,
.dark .security-info {
  background: #4f545c;
}
</style>
