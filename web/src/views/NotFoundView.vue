<template>
  <div class="not-found-page">
    <div class="container">
      <h1>404</h1>
      <h2>Stranica nije pronađena</h2>
      <p>Izvinjavamo se, stranica koju tražite ne postoji ili je premještena.</p>
      <router-link to="/" class="btn btn-primary">Vrati se na početnu</router-link>
    </div>
  </div>
</template>

<style scoped>
.not-found-page { text-align: center; padding: var(--space-16) 0; }
.container { max-width: 600px; }
h1 { font-size: 8rem; font-weight: 800; color: var(--color-primary); margin-bottom: var(--space-4); }
h2 { font-size: var(--font-size-3xl); font-weight: 700; margin-bottom: var(--space-4); }
p { color: var(--color-text-secondary); margin-bottom: var(--space-8); }
</style>
