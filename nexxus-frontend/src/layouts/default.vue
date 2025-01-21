<template>
  <NavBar />
  <v-main>
    <router-view />
  </v-main>
  <AppFooter />
</template>

<script setup>
import AppFooter from '@/components/AppFooter.vue';
</script>
