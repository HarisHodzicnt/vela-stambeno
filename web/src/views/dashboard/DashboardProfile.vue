<template>
  <div class="profile-view">
    <div class="container">
      <h1 class="page-title">Postavke profila</h1>
      
      <div class="profile-card card">
        <div class="profile-header">
          <div class="avatar-section">
            <div class="avatar">
              <i class="pi pi-user"></i>
            </div>
            <button class="btn btn-secondary">Promijeni sliku</button>
          </div>
        </div>

        <form class="profile-form" v-if="user">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Ime</label>
              <input type="text" class="form-input" v-model="user.firstName" disabled />
            </div>
            <div class="form-group">
              <label class="form-label">Prezime</label>
              <input type="text" class="form-input" v-model="user.lastName" disabled />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-input" v-model="user.email" disabled />
          </div>

          <div class="form-group">
            <label class="form-label">Telefon</label>
            <input type="tel" class="form-input" :value="user.phone || 'Nije postavljeno'" disabled />
          </div>

          <div class="info-message">
            <i class="pi pi-info-circle"></i>
            <p>Za promjenu ličnih podataka, molimo kontaktirajte podršku.</p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const user = computed(() => authStore.user);
</script>

<style scoped>
.profile-view { padding: 2rem; }
.container { max-width: 800px; margin: 0 auto; }
.page-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
}
.profile-card {
  padding: 2rem;
}
.profile-header {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e9ecef;
}
.avatar-section {
  display: flex;
  align-items: center;
  gap: 2rem;
}
.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
.info-message {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #e7f3ff;
  border-radius: 0.5rem;
  color: #0066cc;
  margin-top: 2rem;
}
.info-message i {
  font-size: 1.25rem;
}
</style>
