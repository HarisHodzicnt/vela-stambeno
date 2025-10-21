<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card card">
        <div class="auth-header">
          <router-link to="/" class="logo">
            <i class="pi pi-home logo-icon"></i>
            <span class="logo-text">stambeno<span class="logo-accent">.ba</span></span>
          </router-link>
          <h1 class="auth-title">Kreirajte nalog</h1>
          <p class="auth-subtitle">Pridružite se našoj zajednici</p>
        </div>

        <form @submit.prevent="handleRegister" class="auth-form">
          <div class="form-row">
            <div class="form-group">
              <label for="firstName" class="form-label">Ime</label>
              <input v-model="form.firstName" type="text" id="firstName" class="form-input" required />
            </div>
            <div class="form-group">
              <label for="lastName" class="form-label">Prezime</label>
              <input v-model="form.lastName" type="text" id="lastName" class="form-input" required />
            </div>
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email adresa</label>
            <input v-model="form.email" type="email" id="email" class="form-input" placeholder="unesi@email.com" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="phone" class="form-label">Telefon</label>
              <input v-model="form.phone" type="tel" id="phone" class="form-input" placeholder="+387 61 123 456" required />
            </div>
            <div class="form-group">
              <label for="dateOfBirth" class="form-label">Datum rođenja</label>
              <input v-model="form.dateOfBirth" type="date" id="dateOfBirth" class="form-input" required />
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Lozinka</label>
            <input v-model="form.password" type="password" id="password" class="form-input" placeholder="Najmanje 8 karaktera" required />
          </div>

          <div class="form-group">
            <label for="confirmPassword" class="form-label">Potvrdite lozinku</label>
            <input v-model="form.confirmPassword" type="password" id="confirmPassword" class="form-input" required />
          </div>

          <button type="submit" class="btn btn-primary auth-btn" :disabled="loading">
            <span v-if="loading" class="spinner-sm"></span>
            <span v-else>Registruj se</span>
          </button>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>
        </form>

        <div class="auth-footer">
          <p>Već imate nalog? <router-link to="/login">Prijavite se</router-link></p>
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

const form = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  dateOfBirth: '',
  password: '',
  confirmPassword: ''
})

const loading = ref(false)
const error = ref<string | null>(null)

const handleRegister = async () => {
  if (form.value.password !== form.value.confirmPassword) {
    error.value = 'Lozinke se ne podudaraju.'
    return
  }
  
  loading.value = true
  error.value = null
  
  try {
    await authStore.register({
      firstName: form.value.firstName,
      lastName: form.value.lastName,
      email: form.value.email,
      phone: form.value.phone,
      dateOfBirth: form.value.dateOfBirth,
      password: form.value.password
    })
    router.push('/dashboard')
  } catch (err: any) {
    error.value = err.message || 'Došlo je do greške prilikom registracije.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Using shared styles from LoginView.vue */
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
  max-width: 480px;
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

.form-row {
  display: flex;
  gap: var(--space-4);
}

.form-group {
  margin-bottom: var(--space-5);
  flex: 1;
}

.form-label {
  display: block;
  font-weight: 600;
  margin-bottom: var(--space-2);
  font-size: var(--font-size-sm);
}

.form-input {
  width: 100%;
  height: 48px;
  padding: 0 var(--space-4);
}

.auth-btn {
  width: 100%;
  height: 48px;
  font-size: var(--font-size-lg);
  margin-top: var(--space-2);
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
