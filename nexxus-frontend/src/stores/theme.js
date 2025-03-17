// src/stores/theme.js
import { ref, watch } from 'vue';

export const theme = ref(localStorage.getItem('theme') || 'light');

export function toggleTheme() {
  theme.value = theme.value === 'light' ? 'dark' : 'light';
  localStorage.setItem('theme', theme.value);
}

watch(theme, (newTheme) => {
  document.documentElement.classList.toggle('dark', newTheme === 'dark');
});
