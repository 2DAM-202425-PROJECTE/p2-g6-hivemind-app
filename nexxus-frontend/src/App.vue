<template>
  <v-app>
    <RouterView />
  </v-app>
</template>

<script setup>
function setTheme() { // Cambia el tema de la página de clar a fosc.
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }}
</script>
