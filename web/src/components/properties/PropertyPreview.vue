<template>
  <div class="property-preview">
    <div class="preview-section">
      <h3 class="section-title">Naslov i Opis</h3>
      <p><strong>Naslov:</strong> {{ property.title }}</p>
      <p><strong>Opis:</strong> {{ property.description }}</p>
    </div>

    <div class="preview-section">
      <h3 class="section-title">Lokacija</h3>
      <p>{{ property.addressLine1 }}, {{ property.municipality }}, {{ property.city }}, {{ property.country }}</p>
    </div>

    <div class="preview-section">
      <h3 class="section-title">Detalji</h3>
      <p>{{ property.bedrooms }} spavaće sobe • {{ property.bathrooms }} kupatila • {{ property.sizeSqm }} m²</p>
    </div>

    <div class="preview-section">
      <h3 class="section-title">Sadržaji</h3>
      <ul class="amenities-list">
        <li v-for="amenity in property.amenities" :key="amenity">{{ formatAmenity(amenity) }}</li>
      </ul>
    </div>

    <div class="preview-section">
      <h3 class="section-title">Cijena</h3>
      <p v-if="property.listingType === 'rent'"><strong>Cijena po noći:</strong> {{ property.pricePerNight }} BAM</p>
      <p v-if="property.listingType === 'sale'"><strong>Prodajna cijena:</strong> {{ property.salePrice }} BAM</p>
    </div>

    <div class="preview-section" v-if="imagePreviews && imagePreviews.length > 0">
      <h3 class="section-title">Slike</h3>
      <div class="image-preview-grid">
        <div v-for="(image, index) in imagePreviews" :key="index" class="image-preview-item">
          <img :src="image" :alt="`Slika ${index + 1}`" />
        </div>
      </div>
      <p class="image-count-text">{{ imagePreviews.length }} slika/e</p>
    </div>
    <div class="preview-section" v-else>
      <h3 class="section-title">Slike</h3>
      <p class="text-muted">Nijedna slika nije dodana</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ 
  property: any;
  imagePreviews?: string[];
}>();

const formatAmenity = (amenity: string) => {
  return amenity.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<style scoped>
.property-preview { font-size: 1rem; }
.preview-section { margin-bottom: 2rem; }
.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e9ecef;
  padding-bottom: 0.5rem;
}
.amenities-list { list-style: disc; padding-left: 1.5rem; }
.image-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 1rem;
}
.image-preview-item img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 0.5rem;
  border: 2px solid #e9ecef;
}
.image-count-text {
  margin-top: 1rem;
  color: #6c757d;
  font-size: 0.875rem;
}
.text-muted {
  color: #6c757d;
}
</style>
