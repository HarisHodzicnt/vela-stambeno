<template>
  <div class="property-detail-page">
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Učitavam detalje...</p>
    </div>
    <div v-else-if="!property" class="empty-state">
      <h3>Nekretnina nije pronađena</h3>
      <p>Tražena nekretnina ne postoji ili je uklonjena.</p>
      <router-link to="/" class="btn btn-primary">Vrati se na početnu</router-link>
    </div>
    <div v-else class="container">
      <!-- Header -->
      <div class="property-header">
        <h1 class="property-title">{{ property.title }}</h1>
        <div class="property-meta">
          <div class="meta-item rating">
            <i class="pi pi-star-fill"></i>
            <span>{{ property.averageRating?.toFixed(1) || 'Novo' }} ({{ property.totalReviews || 0 }} reviews)</span>
          </div>
          <div class="meta-divider">·</div>
          <div class="meta-item location">
            <i class="pi pi-map-marker"></i>
            <span>{{ property.city }}, {{ property.country }}</span>
          </div>
          <div class="property-actions">
            <button v-if="isOwner" class="action-btn" @click="router.push({ name: 'PropertyEdit', params: { id: property.id } })">
              <i class="pi pi-pencil"></i> Uredi
            </button>
            <button class="action-btn"><i class="pi pi-share-alt"></i> Podijeli</button>
            <button class="action-btn"><i class="pi pi-heart"></i> Sačuvaj</button>
          </div>
        </div>
      </div>

      <!-- Image Gallery -->
      <div class="image-gallery">
        <div class="main-image">
          <img v-if="property.images && property.images.length > 0" :src="getImageUrl(property.images[0].url)" :alt="property.title" />
        </div>
        <div class="thumbnail-grid">
          <div v-if="property.images && property.images.length > 1" v-for="(image, index) in property.images.slice(1, 5)" :key="index" class="thumbnail">
            <img :src="getImageUrl(image.url)" :alt="`${property.title} ${index + 2}`" />
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="property-layout">
        <!-- Left Column -->
        <div class="property-content">
          <div class="host-info">
            <h2 v-if="property.owner">{{ property.propertyType }} u vlasništvu: {{ property.owner.firstName }}</h2>
            <div class="property-specs">
              <span>{{ property.maxGuests }} gosti</span>
              <span>·</span>
              <span>{{ property.bedrooms }} sobe</span>
              <span>·</span>
              <span>{{ property.beds }} kreveta</span>
              <span>·</span>
              <span>{{ property.bathrooms }} kupatila</span>
            </div>
          </div>

          <div class="divider"></div>

          <div class="section">
            <h3 class="section-title">Opis</h3>
            <p>{{ property.description }}</p>
          </div>

          <div class="divider"></div>

          <div class="section">
            <h3 class="section-title">Pogodnosti</h3>
            <div class="amenities-grid">
              <div v-for="amenity in property.amenities" :key="amenity.name" class="amenity-item">
                <i :class="amenity.icon"></i>
                <span>{{ amenity.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column (Booking Widget) -->
        <aside class="booking-widget-wrapper">
          <div class="booking-widget card">
            <div class="price-header">
              <span class="price-amount">{{ formatPrice(property.pricePerNight || property.salePrice) }} BAM</span>
              <span class="price-period">/ noć</span>
            </div>

            <div class="booking-form">
              <div class="date-inputs">
                <div class="date-field">
                  <label for="checkin">DOLAZAK</label>
                  <input type="date" id="checkin" v-model="booking.checkIn" />
                </div>
                <div class="date-field">
                  <label for="checkout">ODLAZAK</label>
                  <input type="date" id="checkout" v-model="booking.checkOut" />
                </div>
              </div>
              <div class="guest-field">
                <label for="guests">GOSTI</label>
                <select id="guests" v-model="booking.guests">
                  <option v-for="n in property.maxGuests" :key="n" :value="n">{{ n }} {{ n === 1 ? 'gost' : 'gosta' }}</option>
                </select>
              </div>
            </div>

            <button class="btn btn-primary booking-btn" @click="handleBooking">Rezerviši</button>

            <div v-if="numberOfNights > 0" class="price-breakdown">
              <div class="price-item">
                <span>{{ formatPrice(property.pricePerNight) }} x {{ numberOfNights }} noći</span>
                <span>{{ formatPrice(basePrice) }} BAM</span>
              </div>
              <div class="price-item">
                <span>Servisna taksa</span>
                <span>{{ formatPrice(serviceFee) }} BAM</span>
              </div>
              <div class="price-total">
                <span>Ukupno</span>
                <span>{{ formatPrice(totalPrice) }} BAM</span>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '@/api/client'
import { useAuthStore } from '@/stores/auth'
import type { Property } from '@/types'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const property = ref<Property | null>(null);
const loading = ref(true);

const booking = ref({
  checkIn: '',
  checkOut: '',
  guests: 1
})

const isOwner = computed(() => {
  if (!authStore.user || !property.value) {
    return false
  }
  
  // Check if owner is an object with id
  const ownerId = property.value.owner?.id || property.value.owner
  const userId = authStore.user.id
  
  // Handle both full owner object and just ID string
  return ownerId === userId || 
         (typeof ownerId === 'string' && ownerId.includes(userId)) ||
         (typeof property.value.owner === 'string' && property.value.owner.includes(userId))
})

const numberOfNights = computed(() => {
  if (booking.value.checkIn && booking.value.checkOut) {
    const start = new Date(booking.value.checkIn)
    const end = new Date(booking.value.checkOut)
    if (end > start) {
      const diffTime = Math.abs(end.getTime() - start.getTime())
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      return diffDays
    }
  }
  return 0
})

const basePrice = computed(() => {
  if (property.value && numberOfNights.value > 0) {
    return (property.value.pricePerNight || 0) * numberOfNights.value
  }
  return 0
})

const serviceFee = computed(() => {
  return basePrice.value * 0.10 // Example: 10% service fee
})

const totalPrice = computed(() => {
  return basePrice.value + serviceFee.value
})

const handleBooking = () => {
  if (booking.value.checkIn && booking.value.checkOut && property.value) {
    router.push({
      name: 'BookingConfirmation',
      params: { id: property.value.id },
      query: {
        checkIn: booking.value.checkIn,
        checkOut: booking.value.checkOut,
        guests: booking.value.guests
      }
    })
  } else {
    alert('Molimo odaberite datume.')
  }
}

const formatPrice = (price: number | undefined) => {
  if (price === undefined) return '0'
  return new Intl.NumberFormat('bs-BA').format(price)
}

const getImageUrl = (url: string) => {
  if (url.startsWith('http')) return url;
  return `http://localhost:8010${url}`;
}

onMounted(async () => {
  const propertyId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id;

  if (!propertyId) {
    loading.value = false;
    return;
  }

  try {
    // Fetch property with owner data included
    const response = await api.get<any>(`/properties/${propertyId}`)
    
    // If owner is an IRI string, fetch the owner data
    if (response.owner && typeof response.owner === 'string') {
      try {
        const ownerData = await api.get(response.owner)
        response.owner = ownerData
      } catch (err) {
        console.error('Failed to fetch owner data:', err)
      }
    }
    
    property.value = response as Property
  } catch (error) {
    console.error('Failed to fetch property details:', error);
    property.value = null;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.property-detail-page { padding: var(--space-8) 0; }
.container { max-width: 1120px; margin: 0 auto; padding: 0 var(--space-4); }

.loading-state, .empty-state { text-align: center; padding: var(--space-16); }
.spinner { width: 48px; height: 48px; border: 4px solid var(--color-border-light); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto var(--space-4); }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state h3 { font-size: var(--font-size-2xl); font-weight: 700; margin-bottom: var(--space-2); }
.empty-state p { color: var(--color-text-secondary); margin-bottom: var(--space-6); }

.property-header { margin-bottom: var(--space-6); }
.property-title { font-size: var(--font-size-3xl); font-weight: 700; margin-bottom: var(--space-2); }
.property-meta { display: flex; align-items: center; gap: var(--space-3); font-size: var(--font-size-sm); font-weight: 600; }
.meta-item { display: flex; align-items: center; gap: var(--space-2); }
.meta-divider { color: var(--color-text-secondary); }
.property-actions { margin-left: auto; display: flex; gap: var(--space-4); }
.action-btn { background: none; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: var(--space-2); text-decoration: underline; }

.image-gallery { display: grid; grid-template-columns: 2fr 1fr; grid-template-rows: repeat(2, 225px); gap: var(--space-2); height: 450px; border-radius: var(--radius-lg); overflow: hidden; margin-bottom: var(--space-8); }
.main-image { grid-row: 1 / -1; }
.thumbnail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-2); }
.image-gallery img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s; }
.image-gallery > div:hover img { transform: scale(1.02); filter: brightness(0.9); }

.property-layout { display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-16); align-items: start; }

.property-content { max-width: 650px; }
.host-info { padding-bottom: var(--space-6); }
.host-info h2 { font-size: var(--font-size-2xl); font-weight: 600; margin-bottom: var(--space-2); }
.property-specs { display: flex; gap: var(--space-3); color: var(--color-text-secondary); }

.divider { height: 1px; background: var(--color-border-light); margin: var(--space-6) 0; }

.section { padding: var(--space-6) 0; }
.section-title { font-size: var(--font-size-xl); font-weight: 700; margin-bottom: var(--space-4); }

.amenities-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-4); }
.amenity-item { display: flex; align-items: center; gap: var(--space-4); font-size: var(--font-size-base); }
.amenity-item i { font-size: 20px; color: var(--color-text-primary); }

.booking-widget-wrapper { position: sticky; top: 120px; }
.booking-widget { padding: var(--space-6); box-shadow: var(--shadow-lg); border: 1px solid var(--color-border-light); border-radius: var(--radius-lg); }

.price-header { display: flex; align-items: baseline; gap: var(--space-2); margin-bottom: var(--space-6); }
.price-amount { font-size: var(--font-size-2xl); font-weight: 700; }
.price-period { color: var(--color-text-secondary); }

.booking-form { border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden; margin-bottom: var(--space-4); }
.date-inputs { display: flex; }
.date-field, .guest-field { padding: var(--space-3); }
.date-field { flex: 1; border-bottom: 1px solid var(--color-border); }
.date-inputs .date-field:first-child { border-right: 1px solid var(--color-border); }
.booking-form label { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: var(--space-1); }
.booking-form input, .booking-form select { border: none; width: 100%; font-size: var(--font-size-sm); background: transparent; }

.booking-btn { width: 100%; height: 48px; font-size: var(--font-size-lg); }

.price-breakdown { margin-top: var(--space-6); display: flex; flex-direction: column; gap: var(--space-3); }
.price-item { display: flex; justify-content: space-between; }
.price-total { display: flex; justify-content: space-between; font-weight: 700; border-top: 1px solid var(--color-border-light); padding-top: var(--space-3); margin-top: var(--space-3); }

@media (max-width: 960px) {
  .property-layout { grid-template-columns: 1fr; }
  .booking-widget-wrapper { position: static; margin-top: var(--space-8); }
}

@media (max-width: 768px) {
  .image-gallery { height: auto; grid-template-columns: 1fr; }
  .thumbnail-grid { display: none; }
}
</style>
