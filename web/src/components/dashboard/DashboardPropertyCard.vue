<template>
  <div class="dashboard-property-card card">
    <div class="property-image-wrapper" @click="viewProperty">
      <img 
        :src="getImageUrl(property.coverImageUrl || property.images?.[0]?.url) || placeholderImage" 
        :alt="property.title"
        class="property-image"
      />
      <div v-if="property.status" class="status-badge" :class="`status-${property.status}`">
        {{ statusLabel }}
      </div>
      <div class="actions-overlay">
        <button class="action-btn btn-edit" @click.stop="editProperty" title="Uredi">
          <i class="pi pi-pencil"></i> Uredi
        </button>
        <button class="action-btn btn-view" @click.stop="viewProperty" title="Pogledaj">
          <i class="pi pi-eye"></i> Pogledaj
        </button>
      </div>
    </div>
    
    <div class="property-content">
      <h3 class="property-title" @click="viewProperty">{{ property.title }}</h3>
      
      <p class="property-location">
        <i class="pi pi-map-marker"></i>
        {{ property.city }}{{ property.municipality ? `, ${property.municipality}` : '' }}
      </p>
      
      <div class="property-price">
        <span class="price-amount">{{ formatPrice(displayPrice) }} BAM</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import type { Property } from '@/types'

const props = defineProps<{
  property: Property
}>()

const router = useRouter()
const placeholderImage = 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800'

const statusLabel = computed(() => {
  switch (props.property.status) {
    case 'draft': return 'DRAFT'
    case 'active': return 'ACTIVE'
    case 'inactive': return 'INACTIVE'
    case 'sold': return 'SOLD'
    case 'rented_long_term': return 'RENTED'
    default: return props.property.status?.toUpperCase()
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

const getImageUrl = (url: string | undefined) => {
  if (!url) return placeholderImage
  if (url.startsWith('http')) return url
  return `http://localhost:8010${url}`
}

const editProperty = () => {
  router.push({ name: 'PropertyEdit', params: { id: props.property.id } })
}

const viewProperty = () => {
  router.push({ name: 'PropertyDetail', params: { id: props.property.id } })
}
</script>

<style scoped>
.dashboard-property-card {
  overflow: hidden;
  transition: all 0.2s;
}

.dashboard-property-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.property-image-wrapper {
  position: relative;
  height: 200px;
  overflow: hidden;
  cursor: pointer;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.dashboard-property-card:hover .property-image {
  transform: scale(1.05);
}

.status-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 6px 12px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  border-radius: 4px;
  backdrop-filter: blur(8px);
  z-index: 2;
}

.status-draft {
  background: rgba(108, 117, 125, 0.9);
  color: white;
}

.status-active {
  background: rgba(40, 167, 69, 0.9);
  color: white;
}

.status-inactive {
  background: rgba(255, 193, 7, 0.9);
  color: #333;
}

.status-sold {
  background: rgba(220, 53, 69, 0.9);
  color: white;
}

.actions-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  gap: 8px;
  padding: 12px;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
  opacity: 0;
  transform: translateY(100%);
  transition: all 0.3s;
}

.dashboard-property-card:hover .actions-overlay {
  opacity: 1;
  transform: translateY(0);
}

.action-btn {
  flex: 1;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s;
}

.btn-edit {
  background: rgba(255, 255, 255, 0.95);
  color: #333;
}

.btn-edit:hover {
  background: white;
  transform: scale(1.02);
}

.btn-view {
  background: rgba(90, 85, 234, 0.95);
  color: white;
}

.btn-view:hover {
  background: #5a55ea;
  transform: scale(1.02);
}

.property-content {
  padding: 16px;
}

.property-title {
  font-size: 16px;
  font-weight: 600;
  color: #212529;
  margin-bottom: 8px;
  cursor: pointer;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  line-height: 1.4;
  min-height: 2.8em;
}

.property-title:hover {
  color: #5a55ea;
}

.property-location {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #6c757d;
  font-size: 14px;
  margin-bottom: 12px;
}

.property-location i {
  font-size: 12px;
}

.property-price {
  border-top: 1px solid #e9ecef;
  padding-top: 12px;
}

.price-amount {
  font-size: 20px;
  font-weight: 700;
  color: #e91e63;
}
</style>
