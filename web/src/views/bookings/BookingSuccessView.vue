<template>
  <div class="booking-success-page">
    <div class="container">
      <div class="success-card card">
        <div class="success-icon">
          <i class="pi pi-check-circle"></i>
        </div>
        <h1>Rezervacija uspješna!</h1>
        <p>Hvala vam! Vaša rezervacija je potvrđena. Poslali smo vam email sa svim detaljima.</p>
        <router-link to="/bookings" class="btn btn-primary">Pogledaj moje rezervacije</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.booking-success-page { display: flex; align-items: center; justify-content: center; min-height: 80vh; }
.container { text-align: center; }
.success-card { max-width: 500px; margin: 0 auto; padding: var(--space-12); }
.success-icon { font-size: 64px; color: var(--color-success); margin-bottom: var(--space-6); }
h1 { font-size: var(--font-size-3xl); font-weight: 700; margin-bottom: var(--space-4); }
p { color: var(--color-text-secondary); margin-bottom: var(--space-8); }
</style>
