<template>
  <div class="settings-container">
    <NavBar />
    <div class="settings-content container">
      <!-- Existing sections here -->
      <section class="theme-section">
        <h1 class="section-title">Settings</h1>
        <div class="theme-selection">
          <label>Themes:</label>
          <div class="theme-options">
            <input type="radio" id="light-mode" value="light" v-model="selectedTheme">
            <label for="light-mode">Light Mode</label>
            <input type="radio" id="dark-mode" value="dark" v-model="selectedTheme">
            <label for="dark-mode">Dark Mode</label>
          </div>
        </div>
      </section>
      <!-- Other sections here -->
      <section class="language-section">
        <h2 class="section-title">Language</h2>
        <select v-model="selectedLanguage" @change="applyLanguage">
          <option value="en">English</option>
          <option value="es">Spanish</option>
          <option value="fr">French</option>
        </select>
      </section>
      <section class="notifications-section">
        <h2 class="section-title">Notifications</h2>
        <label>
          <input type="checkbox" v-model="notificationsEnabled" @change="toggleNotifications">
          Enable Notifications
        </label>
      </section>
      <section class="font-size-section">
        <h2 class="section-title">Font Size</h2>
        <select v-model="selectedFontSize" @change="applyFontSize">
          <option value="small">Small</option>
          <option value="medium">Medium</option>
          <option value="large">Large</option>
        </select>
      </section>
      <section class="privacy-mode-section">
        <h2 class="section-title">Privacy Mode</h2>
        <label>
          <input type="checkbox" v-model="privacyModeEnabled" @change="togglePrivacyMode">
          Enable Privacy Mode
        </label>
      </section>
      <div class="settings-actions">
        <button @click="applySettings">Apply</button>
        <button @click="resetSettings">Reset</button>
      </div>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'
import { ref, watch, onMounted } from "vue";

const selectedTheme = ref("light");

onMounted(() => {
  const savedTheme = localStorage.getItem("theme") || "light";
  selectedTheme.value = savedTheme;
  document.documentElement.classList.toggle("dark", savedTheme === "dark");
});

watch(selectedTheme, (newTheme) => {
  if (newTheme === "dark") {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
  localStorage.setItem("theme", newTheme);
});

export default {
  name: 'SettingsPage',
  components: {
    NavBar,
    AppFooter
  },
  data() {
    return {
      selectedTheme: 'light',
      selectedLanguage: 'en',
      notificationsEnabled: false,
      selectedFontSize: 'medium',
      privacyModeEnabled: false
    }
  },
  mounted() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      this.selectedTheme = savedTheme;
      this.applyTheme();
    }
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage) {
      this.selectedLanguage = savedLanguage;
    }
    const savedNotifications = localStorage.getItem('notificationsEnabled');
    if (savedNotifications) {
      this.notificationsEnabled = JSON.parse(savedNotifications);
    }
    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
      this.selectedFontSize = savedFontSize;
      this.applyFontSize();
    }
    const savedPrivacyMode = localStorage.getItem('privacyModeEnabled');
    if (savedPrivacyMode) {
      this.privacyModeEnabled = JSON.parse(savedPrivacyMode);
    }
  },
  methods: {
    applyTheme() {
      if (this.selectedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
      } else {
        document.body.classList.add('light-mode');
        document.body.classList.remove('dark-mode');
      }
      localStorage.setItem('theme', this.selectedTheme);
    },
    applyLanguage() {
      localStorage.setItem('language', this.selectedLanguage);
    },
    toggleNotifications() {
      localStorage.setItem('notificationsEnabled', this.notificationsEnabled);
    },
    applyFontSize() {
      document.body.style.fontSize = this.selectedFontSize;
      localStorage.setItem('fontSize', this.selectedFontSize);
    },
    togglePrivacyMode() {
    },
    applySettings() {
      this.applyTheme();
      this.applyLanguage();
      this.toggleNotifications();
      this.applyFontSize();
      this.togglePrivacyMode();
    },
    resetSettings() {
      this.selectedTheme = 'light';
      this.selectedLanguage = 'en';
      this.notificationsEnabled = false;
      this.selectedFontSize = 'medium';
      this.privacyModeEnabled = false;
      this.applySettings();
    }
  }
}
</script>

<style scoped>
.settings-container {
  min-height: 100vh;
  background-color: #f0f2f5;
  padding-top: 60px;
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.settings-content {
  padding: 1rem 0.5rem;
}

.theme-section, .language-section, .notifications-section, .font-size-section, .privacy-mode-section {
  background: white;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  color: #000;
}

.section-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #000;
}

.theme-selection, .language-section, .notifications-section, .font-size-section, .privacy-mode-section {
  display: flex;
  flex-direction: column;
  color: #000;
}

.theme-options {
  display: flex;
  gap: 10px;
  margin-bottom: 0.5rem;
  color: #000;
}

.language-section select, .notifications-section label, .font-size-section select, .privacy-mode-section label {
  margin-bottom: 0.5rem;
  color: #000;
}

.settings-actions {
  display: flex;
  gap: 10px;
  margin-top: 1rem;
  justify-content: flex-end;
}

button {
  background-color: #7e7e7e;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
}

button:hover {
  background-color: #1d4ed8;
}
</style>
