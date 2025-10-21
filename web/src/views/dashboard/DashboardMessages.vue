<template>
  <div class="messages-view">
    <div class="container">
      <h1 class="page-title">Poruke</h1>
      <div class="empty-state card">
        <i class="pi pi-envelope empty-icon"></i>
        <h3>Nemate poruka</h3>
        <p>Kada rezervišete nekretninu ili vam neko pošalje poruku, ona će se pojaviti ovdje.</p>
      </div>
    </div>
  </div>
</template>
<style scoped>
.messages-view { padding: 2rem; }
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
}
</style>
