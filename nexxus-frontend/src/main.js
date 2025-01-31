/**
 * main.js
 *
 * Bootstraps Vuetify and other plugins then mounts the App`
 */

// Plugins
import { registerPlugins } from '@/plugins'

// Components
import App from './App.vue'

// Composables
import { createApp } from 'vue'

import router from './router'

import axios from 'axios'

createApp(App).use(axios).use(router).use(registerPlugins).mount('#app')
