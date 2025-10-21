<template>
  <div class="property-list-item card">
    <img :src="property.coverImageUrl || 'https://via.placeholder.com/150'" :alt="property.title" class="property-image" @click="viewProperty" style="cursor: pointer" />
    <div class="property-info" @click="viewProperty" style="cursor: pointer">
      <h3 class="property-title">{{ property.title }}</h3>
      <p class="property-location">{{ property.city }}, {{ property.country }}</p>
      <div class="property-status" :class="`status-${property.status}`">
        {{ property.status }}
      </div>
    </div>
    <div class="property-actions">
      <button class="btn btn-sm btn-outline-secondary" @click="editProperty">
        <i class="pi pi-pencil"></i> Uredi
      </button>
      <button class="btn btn-sm btn-outline-primary" @click="viewProperty">
        <i class="pi pi-eye"></i> Pogledaj
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import type { Property } from '@/types';

const props = defineProps<{ property: Property }>();
const router = useRouter();

const editProperty = () => {
  router.push({ name: 'PropertyEdit', params: { id: props.property.id } });
};

const viewProperty = () => {
  router.push({ name: 'PropertyDetail', params: { id: props.property.id } });
};
</script>

<style scoped>
.property-list-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  gap: 1rem;
}

.property-image {
  width: 100px;
  height: 75px;
  object-fit: cover;
  border-radius: 0.5rem;
}

.property-info {
  flex-grow: 1;
}

.property-title {
  font-weight: 600;
}

.property-location {
  font-size: 0.875rem;
  color: #6c757d;
}

.property-status {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  display: inline-block;
  margin-top: 0.5rem;
}

.status-active {
  color: #28a745;
  background-color: rgba(40, 167, 69, 0.1);
}

.status-draft {
  color: #6c757d;
  background-color: rgba(108, 117, 125, 0.1);
}

.status-sold {
  color: #dc3545;
  background-color: rgba(220, 53, 69, 0.1);
}

.property-actions {
  display: flex;
  gap: 0.5rem;
}
</style>
