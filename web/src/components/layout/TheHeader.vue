<template>
  <header :class="['header', { 'header-scrolled': isScrolled }]">
    <div class="container">
      <div class="header-content">
        <!-- Logo -->
        <router-link to="/" class="logo">
          <i class="pi pi-home logo-icon"></i>
          <span class="logo-text">stambeno<span class="logo-accent">.ba</span></span>
        </router-link>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop">
          <router-link to="/search" class="nav-link">
            <i class="pi pi-search"></i>
            Pretraži
          </router-link>
          
          <template v-if="isAuthenticated">
            <router-link to="/properties/new" class="nav-link nav-link-highlight">
              <i class="pi pi-plus-circle"></i>
              Dodaj nekretninu
            </router-link>
            
            <!-- User Menu -->
            <div class="user-menu" @click="toggleUserMenu">
              <button class="user-menu-btn">
                <i class="pi pi-bars"></i>
                <div class="user-avatar">
                  <i class="pi pi-user"></i>
                </div>
              </button>
              
              <transition name="dropdown">
                <div v-if="showUserMenu" class="dropdown-menu" @click.stop>
                  <div class="dropdown-header">
                    <div class="user-info">
                      <div class="user-avatar-large">
                        <i class="pi pi-user"></i>
                      </div>
                      <div>
                        <div class="user-name">{{ userName }}</div>
                        <div class="user-email">{{ userEmail }}</div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="dropdown-section">
                    <router-link to="/dashboard" class="dropdown-item" @click="closeUserMenu">
                      <i class="pi pi-chart-bar"></i>
                      <span>Dashboard</span>
                    </router-link>
                    <router-link to="/dashboard/bookings" class="dropdown-item" @click="closeUserMenu">
                      <i class="pi pi-calendar"></i>
                      <span>Moje rezervacije</span>
                    </router-link>
                    <router-link to="/dashboard/offers" class="dropdown-item" @click="closeUserMenu">
                      <i class="pi pi-money-bill"></i>
                      <span>Ponude</span>
                    </router-link>
                  </div>
                  
                  <div class="dropdown-section">
                    <router-link to="/dashboard/messages" class="dropdown-item" @click="closeUserMenu">
                      <i class="pi pi-envelope"></i>
                      <span>Poruke</span>
                      <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
                    </router-link>
                    <router-link to="/dashboard/saved" class="dropdown-item" @click="closeUserMenu">
                      <i class="pi pi-heart"></i>
                      <span>Sačuvano</span>
                    </router-link>
                  </div>
                  
                  <div class="dropdown-section">
                    <router-link to="/dashboard/profile" class="dropdown-item" @click="closeUserMenu">
                      <i class="pi pi-cog"></i>
                      <span>Postavke profila</span>
                    </router-link>
                    <button @click="handleLogout" class="dropdown-item">
                      <i class="pi pi-sign-out"></i>
                      <span>Odjavi se</span>
                    </button>
                  </div>
                </div>
              </transition>
            </div>
          </template>
          
          <template v-else>
            <router-link to="/login" class="nav-link">
              Prijavi se
            </router-link>
            <router-link to="/register" class="btn btn-primary">
              Registruj se
            </router-link>
          </template>
        </nav>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" @click="toggleMobileMenu">
          <i :class="showMobileMenu ? 'pi pi-times' : 'pi pi-bars'"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <transition name="mobile-slide">
      <div v-if="showMobileMenu" class="mobile-menu" @click.stop>
        <div class="mobile-menu-content">
          <template v-if="!isAuthenticated">
            <router-link to="/login" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-sign-in"></i>
              Prijavi se
            </router-link>
            <router-link to="/register" class="mobile-menu-item mobile-menu-highlight" @click="closeMobileMenu">
              <i class="pi pi-user-plus"></i>
              Registruj se
            </router-link>
            <div class="mobile-divider"></div>
          </template>
          
          <router-link to="/search" class="mobile-menu-item" @click="closeMobileMenu">
            <i class="pi pi-search"></i>
            Pretraži nekretnine
          </router-link>
          
          <template v-if="isAuthenticated">
            <div class="mobile-divider"></div>
            
            <router-link to="/properties/new" class="mobile-menu-item mobile-menu-highlight" @click="closeMobileMenu">
              <i class="pi pi-plus-circle"></i>
              Dodaj nekretninu
            </router-link>
            
            <router-link to="/dashboard" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-chart-bar"></i>
              Dashboard
            </router-link>
            <router-link to="/dashboard/bookings" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-calendar"></i>
              Moje rezervacije
            </router-link>
            <router-link to="/dashboard/offers" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-money-bill"></i>
              Ponude
            </router-link>
            <router-link to="/dashboard/messages" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-envelope"></i>
              Poruke
              <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
            </router-link>
            <router-link to="/dashboard/saved" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-heart"></i>
              Sačuvano
            </router-link>
            
            <div class="mobile-divider"></div>
            
            <router-link to="/dashboard/profile" class="mobile-menu-item" @click="closeMobileMenu">
              <i class="pi pi-cog"></i>
              Postavke profila
            </router-link>
            <button @click="handleLogout" class="mobile-menu-item">
              <i class="pi pi-sign-out"></i>
              Odjavi se
            </button>
          </template>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// State
const showUserMenu = ref(false)
const showMobileMenu = ref(false)
const isScrolled = ref(false)
const unreadCount = ref(0)

// Computed
const isAuthenticated = computed(() => authStore.isAuthenticated)
const isOwner = computed(() => authStore.user?.isOwner || false)
const userName = computed(() => {
  const user = authStore.user
  return user ? `${user.firstName} ${user.lastName}` : ''
})
const userEmail = computed(() => authStore.user?.email || '')

// Methods
const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  if (showUserMenu.value) {
    showMobileMenu.value = false
  }
}

const closeUserMenu = () => {
  showUserMenu.value = false
}

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
  if (showMobileMenu.value) {
    showUserMenu.value = false
  }
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
}

const handleLogout = async () => {
  await authStore.logout()
  closeUserMenu()
  closeMobileMenu()
  router.push('/')
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('.user-menu') && !target.closest('.dropdown-menu')) {
    showUserMenu.value = false
  }
  if (!target.closest('.mobile-menu') && !target.closest('.mobile-menu-btn')) {
    showMobileMenu.value = false
  }
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: white;
  border-bottom: 1px solid transparent;
  transition: all var(--transition-base);
}

.header-scrolled {
  border-bottom-color: var(--color-border-light);
  box-shadow: var(--shadow-sm);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 80px;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  text-decoration: none;
  color: var(--color-primary);
  font-weight: 800;
  font-size: var(--font-size-2xl);
  transition: all var(--transition-base);
}

.logo:hover {
  transform: translateY(-1px);
}

.logo-icon {
  font-size: 28px;
}

.logo-text {
  color: var(--color-text-primary);
}

.logo-accent {
  color: var(--color-primary);
}

/* Desktop Navigation */
.nav-desktop {
  display: none;
  align-items: center;
  gap: var(--space-2);
}

@media (min-width: 768px) {
  .nav-desktop {
    display: flex;
  }
}

.nav-link {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-4);
  color: var(--color-text-primary);
  text-decoration: none;
  font-weight: 600;
  font-size: var(--font-size-sm);
  border-radius: var(--radius-full);
  transition: all var(--transition-base);
}

.nav-link:hover {
  background: var(--color-bg-secondary);
}

.nav-link-highlight {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.nav-link-highlight:hover {
  background: var(--color-primary);
  color: white;
}

/* User Menu */
.user-menu {
  position: relative;
}

.user-menu-btn {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-2);
  background: white;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-full);
  cursor: pointer;
  transition: all var(--transition-base);
}

.user-menu-btn:hover {
  box-shadow: var(--shadow);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

/* Dropdown Menu */
.dropdown-menu {
  position: absolute;
  top: calc(100% + var(--space-2));
  right: 0;
  width: 280px;
  background: white;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-xl);
  border: 1px solid var(--color-border-light);
  overflow: hidden;
}

.dropdown-header {
  padding: var(--space-4);
  border-bottom: 1px solid var(--color-border-light);
}

.user-info {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.user-avatar-large {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.user-name {
  font-weight: 700;
  color: var(--color-text-primary);
  font-size: var(--font-size-base);
}

.user-email {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
}

.dropdown-section {
  padding: var(--space-2) 0;
  border-bottom: 1px solid var(--color-border-light);
}

.dropdown-section:last-child {
  border-bottom: none;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  color: var(--color-text-primary);
  text-decoration: none;
  font-size: var(--font-size-sm);
  font-weight: 500;
  transition: all var(--transition-base);
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}

.dropdown-item:hover {
  background: var(--color-bg-secondary);
}

.dropdown-item i {
  color: var(--color-text-secondary);
  width: 16px;
}

.badge {
  margin-left: auto;
  padding: 2px 8px;
  background: var(--color-primary);
  color: white;
  border-radius: var(--radius-full);
  font-size: var(--font-size-xs);
  font-weight: 700;
}

/* Mobile Menu Button */
.mobile-menu-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  background: white;
  cursor: pointer;
  transition: all var(--transition-base);
  font-size: 20px;
  color: var(--color-text-primary);
}

.mobile-menu-btn:hover {
  background: var(--color-bg-secondary);
}

@media (min-width: 768px) {
  .mobile-menu-btn {
    display: none;
  }
}

/* Mobile Menu */
.mobile-menu {
  position: fixed;
  top: 81px;
  left: 0;
  right: 0;
  bottom: 0;
  background: white;
  z-index: 99;
  overflow-y: auto;
}

.mobile-menu-content {
  padding: var(--space-4);
}

.mobile-menu-item {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-4);
  color: var(--color-text-primary);
  text-decoration: none;
  font-weight: 600;
  font-size: var(--font-size-base);
  border-radius: var(--radius);
  transition: all var(--transition-base);
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  margin-bottom: var(--space-2);
}

.mobile-menu-item:hover {
  background: var(--color-bg-secondary);
}

.mobile-menu-highlight {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.mobile-divider {
  height: 1px;
  background: var(--color-border-light);
  margin: var(--space-4) 0;
}

/* Transitions */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all var(--transition-base);
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.mobile-slide-enter-active,
.mobile-slide-leave-active {
  transition: all var(--transition-base);
}

.mobile-slide-enter-from,
.mobile-slide-leave-to {
  transform: translateX(100%);
}
</style>
