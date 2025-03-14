/**
 * main.js
 *
 * Bootstraps Vuetify and other plugins then mounts the App
 */

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import './styles/styles.css'

const vuetify = createVuetify({
  components,
  directives,
})

const app = createApp(App)
app.use(router)
app.use(vuetify)
app.config.globalProperties.$axios = axios  // Make axios available globally
app.mount('#app')
