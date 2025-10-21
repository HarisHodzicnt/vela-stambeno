<template>
  <div>
    <div class="header">
      <h1 class="page-title">Moje Nekretnine</h1>
      <router-link to="/properties/new" class="btn btn-primary add-property-btn">
        <i class="pi pi-plus"></i> Dodaj Nekretninu
      </router-link>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Učitavanje nekretnina...</p>
    </div>
    <div v-else-if="properties.length === 0" class="empty-state">
      <i class="pi pi-home" style="font-size: 64px; color: #dee2e6; margin-bottom: 1rem;"></i>
      <h3>Nema unesenih nekretnina</h3>
      <p>Dodajte svoju prvu nekretninu da počnete.</p>
      <router-link to="/properties/new" class="btn btn-primary">Dodaj prvu nekretninu</router-link>
    </div>
    <div v-else>
      <p class="properties-count">Pogledaj sve nekretnine →</p>
      <div class="properties-grid">
        <DashboardPropertyCard v-for="property in properties" :key="property.id" :property="property" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from '@/api/client';
import type { Property } from '@/types';
import DashboardPropertyCard from '@/components/dashboard/DashboardPropertyCard.vue';

import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const properties = ref<Property[]>([]);
const loading = ref(true);

onMounted(async () => {
  if (!authStore.user) return;
  try {
    const response = await api.get('/properties');
    properties.value = response['hydra:member'] || response;
  } catch (error) {
    console.error('Failed to fetch properties:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #212529;
}

.add-property-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  font-weight: 600;
}

.properties-count {
  color: #e91e63;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 1.5rem;
  cursor: pointer;
}

.properties-count:hover {
  text-decoration: underline;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 4rem 2rem;
  border: 2px dashed #e9ecef;
  border-radius: 0.5rem;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #f1f3f5;
  border-top-color: #5a55ea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #495057;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #6c757d;
  margin-bottom: 2rem;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .properties-grid {
    grid-template-columns: 1fr;
  }
}
</style>
