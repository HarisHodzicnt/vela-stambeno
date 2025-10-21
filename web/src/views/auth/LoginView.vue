<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card card">
        <div class="auth-header">
          <router-link to="/" class="logo">
            <i class="pi pi-home logo-icon"></i>
            <span class="logo-text">stambeno<span class="logo-accent">.ba</span></span>
          </router-link>
          <h1 class="auth-title">Dobrodošli nazad</h1>
          <p class="auth-subtitle">Prijavite se na svoj nalog</p>
        </div>

        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label for="email" class="form-label">Email adresa</label>
            <div class="input-wrapper">
              <i class="pi pi-envelope"></i>
              <input 
                v-model="email"
                type="email"
                id="email"
                class="form-input"
                placeholder="unesi@email.com"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Lozinka</label>
            <div class="input-wrapper">
              <i class="pi pi-lock"></i>
              <input 
                v-model="password"
                type="password"
                id="password"
                class="form-input"
                placeholder="••••••••"
                required
              />
            </div>
          </div>

          <div class="form-options">
            <label class="checkbox-label">
              <input type="checkbox" v-model="rememberMe" />
              <span>Zapamti me</span>
            </label>
            <router-link to="/forgot-password" class="forgot-password-link">
              Zaboravili ste lozinku?
            </router-link>
          </div>

          <button type="submit" class="btn btn-primary auth-btn" :disabled="loading">
            <span v-if="loading" class="spinner-sm"></span>
            <span v-else>Prijavi se</span>
          </button>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>
        </form>

        <div class="auth-footer">
          <p>Nemate nalog? <router-link to="/register">Registrujte se</router-link></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const loading = ref(false)
const error = ref<string | null>(null)

const handleLogin = async () => {
  loading.value = true
  error.value = null
  try {
    await authStore.login({ email: email.value, password: password.value })
    router.push('/dashboard')
  } catch (err: any) {
    error.value = err.message || 'Došlo je do greške prilikom prijave.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-color: var(--color-bg-secondary);
  padding: var(--space-6);
}

.auth-container {
  width: 100%;
  max-width: 450px;
}

.auth-card {
  padding: var(--space-8) var(--space-10);
  box-shadow: var(--shadow-lg);
}

.auth-header {
  text-align: center;
  margin-bottom: var(--space-8);
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  text-decoration: none;
  color: var(--color-primary);
  font-weight: 800;
  font-size: var(--font-size-2xl);
  margin-bottom: var(--space-6);
}

.logo-icon { font-size: 28px; }
.logo-text { color: var(--color-text-primary); }
.logo-accent { color: var(--color-primary); }

.auth-title {
  font-size: var(--font-size-3xl);
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: var(--space-2);
}

.auth-subtitle {
  color: var(--color-text-secondary);
  font-size: var(--font-size-base);
}

.form-group {
  margin-bottom: var(--space-5);
}

.form-label {
  display: block;
  font-weight: 600;
  margin-bottom: var(--space-2);
  font-size: var(--font-size-sm);
}

.input-wrapper {
  position: relative;
}

.input-wrapper i {
  position: absolute;
  left: var(--space-4);
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-text-tertiary);
}

.form-input {
  padding-left: var(--space-10);
  height: 48px;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-6);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-size: var(--font-size-sm);
  cursor: pointer;
}

.forgot-password-link {
  font-size: var(--font-size-sm);
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 600;
}

.auth-btn {
  width: 100%;
  height: 48px;
  font-size: var(--font-size-lg);
}

.error-message {
  margin-top: var(--space-4);
  color: var(--color-error);
  background: rgba(193, 53, 21, 0.1);
  padding: var(--space-3);
  border-radius: var(--radius);
  text-align: center;
  font-size: var(--font-size-sm);
}

.auth-footer {
  margin-top: var(--space-8);
  text-align: center;
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
}

.auth-footer a {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
}

.spinner-sm {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.5);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
