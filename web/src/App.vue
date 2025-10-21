<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <TheHeader v-if="!isAuthPage" />
    
    <main class="main-content">
      <RouterView />
    </main>
    
    <TheFooter v-if="!isAuthPage" />
    
    <Toast position="top-right" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Toast from 'primevue/toast'
import TheHeader from '@/components/layout/TheHeader.vue'
import TheFooter from '@/components/layout/TheFooter.vue'

const route = useRoute()

const isAuthPage = computed(() => {
  return ['Login', 'Register', 'ForgotPassword'].includes(route.name as string)
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
}
</style>
