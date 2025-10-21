<template>
  <div>
    <h1 class="text-3xl font-bold mb-6">Moja Putovanja</h1>
    <div class="empty-state">
      <p>Još uvijek niste imali putovanja.</p>
      <router-link to="/search" class="btn btn-primary mt-4">Pronađi smještaj</router-link>
    </div>
  </div>
</template>

<style scoped>
.empty-state {
  text-align: center;
  padding: 4rem;
  border: 2px dashed #e9ecef;
  border-radius: 0.5rem;
}
</style>
