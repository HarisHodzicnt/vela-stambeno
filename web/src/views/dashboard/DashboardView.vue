<template>
  <div class="dashboard-layout">
    <aside class="sidebar">
      <router-link v-if="isOwner" to="/properties/new" class="btn btn-primary add-property-btn">
        <i class="pi pi-plus"></i>
        <span>Dodaj Nekretninu</span>
      </router-link>

      <nav v-if="isOwner" class="nav-group">
        <h3 class="nav-title">Vlasnik</h3>
        <router-link to="/dashboard" class="nav-item">
          <i class="pi pi-chart-bar"></i>
          <span>Pregled</span>
        </router-link>
        <router-link to="/dashboard/properties" class="nav-item">
          <i class="pi pi-home"></i>
          <span>Nekretnine</span>
        </router-link>
        <router-link to="/dashboard/bookings" class="nav-item">
          <i class="pi pi-calendar"></i>
          <span>Rezervacije</span>
        </router-link>
        <router-link to="/dashboard/offers" class="nav-item">
          <i class="pi pi-tags"></i>
          <span>Ponude</span>
        </router-link>
        <router-link to="/dashboard/payouts" class="nav-item">
          <i class="pi pi-wallet"></i>
          <span>Isplate</span>
        </router-link>
      </nav>

      <nav class="nav-group">
        <h3 class="nav-title">Korisnik</h3>
        <router-link to="/dashboard/bookings" class="nav-item">
          <i class="pi pi-calendar"></i>
          <span>Moje rezervacije</span>
        </router-link>
        <router-link to="/dashboard/offers" class="nav-item">
          <i class="pi pi-money-bill"></i>
          <span>Ponude</span>
        </router-link>
        <router-link to="/dashboard/messages" class="nav-item">
          <i class="pi pi-envelope"></i>
          <span>Poruke</span>
        </router-link>
        <router-link to="/dashboard/saved" class="nav-item">
          <i class="pi pi-heart"></i>
          <span>Sačuvano</span>
        </router-link>
        <router-link to="/dashboard/profile" class="nav-item">
          <i class="pi pi-cog"></i>
          <span>Postavke profila</span>
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <div class="credits-section">
          <h3 class="nav-title">Krediti</h3>
          <div class="credit-balance">
            <span>Stanje:</span>
            <span class="font-bold">0 kredita</span>
          </div>
          <router-link to="/dashboard/credits" class="btn btn-secondary w-full mt-2">Kupi Kredite</router-link>
        </div>
      </div>
    </aside>
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const isOwner = computed(() => authStore.user?.roles.includes('ROLE_OWNER'));
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: calc(100vh - 80px); /* Adjust for header height */
}

.sidebar {
  width: 260px;
  background: #f8f9fa;
  border-right: 1px solid #e9ecef;
  padding: 2rem;
  flex-shrink: 0;
}

.nav-group {
  margin-bottom: 2rem;
}

.nav-title {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #868e96;
  margin-bottom: 1rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  text-decoration: none;
  color: #495057;
  font-weight: 600;
  transition: background-color 0.2s;
}

.nav-item:hover {
  background-color: #e9ecef;
}

.nav-item.router-link-exact-active {
  background-color: #e8e7ff;
  color: #5a55ea;
}

.nav-item i {
  font-size: 1.25rem;
}

.main-content {
  flex-grow: 1;
  padding: 2rem;
  background-color: #fff;
}
</style>
