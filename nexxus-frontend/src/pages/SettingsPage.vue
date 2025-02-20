<template>
  <div class="settings-container">
    <NavBar />
    <div class="settings-content container">
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
          <button @click="applyTheme">Apply</button>
        </div>
      </section>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'

export default {
  name: 'SettingsPage',
  components: {
    NavBar,
    AppFooter
  },
  data() {
    return {
      selectedTheme: 'light'
    }
  },
  mounted() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      this.selectedTheme = savedTheme;
      this.applyTheme();
    } else { /* empty */ }
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
      localStorage.setItem('theme', this.selectedTheme); // Save the selected theme
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
  padding: 2rem 1rem;
}

.theme-section {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  color: #000;
}

.section-title {
  font-size: 1.75rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  color: #000;
}

.theme-selection {
  display: flex;
  flex-direction: column;
  color: #000;
}

.theme-options {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  color: #000;
}

.theme-options input {
  margin-right: 0.5rem;
}

.theme-options label {
  margin-right: 1rem;
  color: #000;
}

button {
  background-color: #2563eb;
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
