<template>
  <div class="account-settings-container">
    <NavBar />
    <div class="account-settings-content container">
      <section class="account-info-section">
        <h1 class="section-title">Account Information</h1>
        <!-- Add account information fields here -->
      </section>
      <section class="password-section">
        <h2 class="section-title">Change Password</h2>
        <!-- Add password change fields here -->
      </section>
      <div class="settings-actions">
        <button @click="saveAccountSettings">Save</button>
        <button @click="resetAccountSettings">Reset</button>
      </div>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import NavBar from '../components/NavBar.vue'
import AppFooter from '../components/AppFooter.vue'

export default {
  name: 'AccountSettingsPage',
  components: {
    NavBar,
    AppFooter
  },
  data() {
    return {
      // Add data properties for account settings here
    }
  },
  methods: {
    saveAccountSettings() {
      // Implement save logic here
    },
    resetAccountSettings() {
      // Implement reset logic here
    }
  }
}
</script>

<style scoped>
.account-settings-container {
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

.account-settings-content {
  padding: 1rem 0.5rem;
}

.account-info-section, .password-section {
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
