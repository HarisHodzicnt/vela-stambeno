<template>
  <div class="saved-properties-view">
    <div class="container">
      <h1 class="page-title">Sačuvane nekretnine</h1>
      <div class="empty-state card">
        <i class="pi pi-heart empty-icon"></i>
        <h3>Nema sačuvanih nekretnina</h3>
        <p>Kliknite na ikonu srca na bilo kojoj nekretnini da je dodate na listu.</p>
        <router-link to="/search" class="btn btn-primary mt-4">
          <i class="pi pi-search"></i>
          Pretraži nekretnine
        </router-link>
      </div>
    </div>
  </div>
</template>
<style scoped>
.saved-properties-view { padding: 2rem; }
.container { max-width: 1200px; margin: 0 auto; }
.page-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
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
</style>
