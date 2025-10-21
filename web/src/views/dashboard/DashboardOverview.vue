<template>
  <div>
    <h1 class="page-title">Pregled</h1>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card card">
        <h3 class="stat-label">Ukupni Prihod</h3>
        <p class="stat-value">0 BAM</p>
      </div>
      <div class="stat-card card">
        <h3 class="stat-label">Nove Rezervacije</h3>
        <p class="stat-value">0</p>
      </div>
      <div class="stat-card card">
        <h3 class="stat-label">Nove Ponude</h3>
        <p class="stat-value">0</p>
      </div>
      <div class="stat-card card">
        <h3 class="stat-label">Novi Review-ovi</h3>
        <p class="stat-value">0</p>
      </div>
    </div>

    <!-- My Properties Section -->
    <div class="section">
      <div class="section-header">
        <h2 class="section-title">Moje Nekretnine</h2>
        <router-link to="/properties/new" class="btn btn-primary">
          <i class="pi pi-plus"></i>
          Dodaj Nekretninu
        </router-link>
      </div>
      
      <div v-if="loading" class="loading-state">
        <i class="pi pi-spinner pi-spin"></i>
        <p>Učitavanje...</p>
      </div>
      
      <div v-else-if="properties.length === 0" class="empty-state card">
        <i class="pi pi-home empty-icon"></i>
        <h3>Nemate objavljenih nekretnina</h3>
        <p>Započnite sa iznajmljivanjem ili prodajom vaše prve nekretnine.</p>
        <router-link to="/properties/new" class="btn btn-primary mt-4">
          <i class="pi pi-plus"></i>
          Dodaj Prvu Nekretninu
        </router-link>
      </div>
      
      <div v-else class="properties-grid">
        <router-link 
          v-for="property in properties" 
          :key="property.id" 
          :to="`/properties/${property.id}`"
          class="property-card card"
        >
          <div class="property-image">
            <img 
              v-if="property.images && property.images.length > 0" 
              :src="getImageUrl(property.images[0].url)" 
              :alt="property.title" 
            />
            <div v-else class="no-image">
              <i class="pi pi-image"></i>
            </div>
            <span class="property-status" :class="`status-${property.status}`">
              {{ property.status }}
            </span>
          </div>
          <div class="property-details">
            <h3 class="property-title">{{ property.title }}</h3>
            <p class="property-location">
              <i class="pi pi-map-marker"></i>
              {{ property.city }}, {{ property.country }}
            </p>
            <p class="property-price">{{ formatPrice(property.pricePerNight || property.salePrice) }} BAM</p>
          </div>
        </router-link>
      </div>

      <router-link v-if="properties.length > 0" to="/dashboard/properties" class="view-all-link">
        Pogledaj sve nekretnine →
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from '@/api/client';
import type { Property } from '@/types';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const properties = ref<Property[]>([]);
const loading = ref(true);

const getImageUrl = (url: string) => {
  if (url.startsWith('http')) return url;
  return `http://localhost:8010${url}`;
};

const formatPrice = (price: number | undefined) => {
  if (price === undefined) return '0';
  return new Intl.NumberFormat('bs-BA').format(price);
};

onMounted(async () => {
  if (!authStore.user) return;
  try {
    const response = await api.get('/properties');
    // Show only first 4 properties on overview
    const allProperties = response['hydra:member'] || response;
    properties.value = allProperties.slice(0, 4);
  } catch (error) {
    console.error('Failed to fetch properties:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.page-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  padding: 1.5rem;
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.section {
  margin-top: 3rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.property-card {
  text-decoration: none;
  color: inherit;
  transition: all 0.2s;
  overflow: hidden;
}

.property-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.property-image {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f8f9fa;
}

.property-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
  font-size: 3rem;
}

.property-status {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  background: white;
  box-shadow: var(--shadow);
}

.status-draft {
  color: #6c757d;
  background: #f8f9fa;
}

.status-active {
  color: #28a745;
  background: #d4edda;
}

.status-sold {
  color: #dc3545;
  background: #f8d7da;
}

.property-details {
  padding: 1rem;
}

.property-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.property-location {
  font-size: 0.875rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.property-price {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-primary);
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
}

.loading-state i {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-icon {
  font-size: 4rem;
  color: #adb5bd;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #6c757d;
  margin-bottom: 1rem;
}

.mt-4 {
  margin-top: 1.5rem;
}

.view-all-link {
  display: inline-block;
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
  margin-top: 1rem;
}

.view-all-link:hover {
  text-decoration: underline;
}
</style>
