<template>
  <div class="property-card card card-hover" @click="goToProperty">
    <div class="property-image-wrapper">
      <img 
        :src="property.coverImageUrl || placeholderImage" 
        :alt="property.title"
        class="property-image"
      />
      <button class="favorite-btn" @click.stop="toggleFavorite">
        <i :class="isFavorite ? 'pi pi-heart-fill' : 'pi pi-heart'"></i>
      </button>
      <div v-if="property.listingType" class="property-badge">
        {{ listingTypeBadge }}
      </div>
    </div>
    
    <div class="property-content">
      <div class="property-header">
        <h3 class="property-title">{{ property.title }}</h3>
        <div v-if="property.averageRating" class="property-rating">
          <i class="pi pi-star-fill"></i>
          <span>{{ property.averageRating.toFixed(1) }}</span>
        </div>
      </div>
      
      <p class="property-location">
        <i class="pi pi-map-marker"></i>
        {{ property.city }}{{ property.municipality ? `, ${property.municipality}` : '' }}
      </p>
      
      <div class="property-features">
        <span v-if="property.bedrooms">
          <i class="pi pi-home"></i> {{ property.bedrooms }} {{ property.bedrooms === 1 ? 'soba' : 'sobe' }}
        </span>
        <span v-if="property.sizeSqm">
          <i class="pi pi-circle"></i> {{ property.sizeSqm }} m²
        </span>
        <span v-if="property.bathrooms">
          <i class="pi pi-circle"></i> {{ property.bathrooms }} {{ property.bathrooms === 1 ? 'kupatilo' : 'kupatila' }}
        </span>
      </div>
      
      <div class="property-price">
        <span class="price">
          <span class="price-amount">{{ formatPrice(displayPrice) }} BAM</span>
          <span v-if="property.listingType === 'rent' && property.pricePerNight" class="price-period">
            / noć
          </span>
          <span v-else-if="property.listingType === 'rent' && property.pricePerMonth" class="price-period">
            / mjesec
          </span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import type { Property } from '@/types'

const props = defineProps<{
  property: Property
}>()

const router = useRouter()
const isFavorite = ref(false)
const placeholderImage = 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800'

const listingTypeBadge = computed(() => {
  switch (props.property.listingType) {
    case 'rent': return 'Iznajmljivanje'
    case 'sale': return 'Prodaja'
    case 'both': return 'Prodaja / Iznajmljivanje'
    default: return ''
  }
})

const displayPrice = computed(() => {
  if (props.property.pricePerNight) return props.property.pricePerNight
  if (props.property.pricePerMonth) return props.property.pricePerMonth
  if (props.property.salePrice) return props.property.salePrice
  return 0
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('bs-BA').format(price)
}

const goToProperty = () => {
  router.push(`/properties/${props.property.id}`)
}

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  // TODO: Implement API call to save/unsave property
}
</script>

<style scoped>
.property-card {
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  height: 100%;
  display: flex;
  flex-direction: column;
}

.property-image-wrapper {
  position: relative;
  height: 240px;
  overflow: hidden;
  flex-shrink: 0;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.property-card:hover .property-image {
  transform: scale(1.05);
}

.favorite-btn {
  position: absolute;
  top: var(--space-3);
  right: var(--space-3);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.95);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition-base);
  box-shadow: var(--shadow-sm);
  color: var(--color-text-primary);
  z-index: 2;
}

.favorite-btn:hover {
  background: white;
  transform: scale(1.1);
  box-shadow: var(--shadow);
}

.favorite-btn .pi-heart-fill {
  color: var(--color-primary);
}

.property-badge {
  position: absolute;
  top: var(--space-3);
  left: var(--space-3);
  padding: var(--space-2) var(--space-3);
  background: rgba(0, 0, 0, 0.75);
  color: white;
  font-size: var(--font-size-xs);
  font-weight: 600;
  border-radius: var(--radius);
  backdrop-filter: blur(10px);
  z-index: 1;
}

.property-content {
  padding: var(--space-4);
  flex: 1;
  display: flex;
  flex-direction: column;
}

.property-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: var(--space-2);
  margin-bottom: var(--space-2);
}

.property-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-text-primary);
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  line-height: 1.4;
  min-height: 2.8em;
}

.property-rating {
  display: flex;
  align-items: center;
  gap: var(--space-1);
  color: var(--color-text-primary);
  font-weight: 600;
  font-size: var(--font-size-sm);
  flex-shrink: 0;
}

.property-rating i {
  color: #FFD700;
  font-size: 12px;
}

.property-location {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-3);
}

.property-location i {
  color: var(--color-text-tertiary);
}

.property-features {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-3);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-3);
}

.property-features span {
  display: flex;
  align-items: center;
  gap: var(--space-1);
}

.property-features i {
  color: var(--color-text-tertiary);
  font-size: 10px;
}

.property-price {
  border-top: 1px solid var(--color-border-light);
  padding-top: var(--space-3);
  margin-top: auto;
}

.price {
  display: flex;
  align-items: baseline;
  gap: var(--space-1);
}

.price-amount {
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--color-text-primary);
}

.price-period {
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
  font-weight: 500;
}
</style>
