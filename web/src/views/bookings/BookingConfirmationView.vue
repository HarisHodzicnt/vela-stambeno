<template>
  <div class="booking-confirmation-page">
    <div class="container">
      <h1>Potvrdite svoju rezervaciju</h1>
      
      <div class="confirmation-layout">
        <!-- Left Column: Booking Details -->
        <div class="booking-details">
          <h2>Detalji vaše rezervacije</h2>
          
          <div class="property-summary">
            <img :src="property?.images[0]" :alt="property?.title" class="property-image" />
            <div class="property-info">
              <p>{{ property?.propertyType }}</p>
              <h3>{{ property?.title }}</h3>
              <div class="rating">
                <i class="pi pi-star-fill"></i>
                <span>{{ property?.averageRating?.toFixed(1) }}</span>
                <span>({{ property?.totalReviews }} reviews)</span>
              </div>
            </div>
          </div>

          <div class="divider"></div>

          <div class="trip-details">
            <h3>Vaše putovanje</h3>
            <div class="detail-item">
              <h4>Datumi</h4>
              <p>{{ formatDate(booking.checkIn) }} - {{ formatDate(booking.checkOut) }}</p>
            </div>
            <div class="detail-item">
              <h4>Gosti</h4>
              <p>{{ booking.guests }} {{ booking.guests === 1 ? 'gost' : 'gosta' }}</p>
            </div>
          </div>

          <div class="divider"></div>

          <div class="price-summary">
            <h3>Detalji o cijeni</h3>
            <div class="price-item">
              <span>{{ formatPrice(property?.pricePerNight) }} x {{ numberOfNights }} noći</span>
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

        <!-- Right Column: Payment -->
        <div class="payment-section card">
          <h3>Plaćanje</h3>
          <p>Sva plaćanja su sigurna i enkriptovana.</p>
          
          <!-- Stripe Payment Element will go here -->
          <div id="payment-element"></div>
          
          <button class="btn btn-primary payment-btn" @click="confirmBooking">
            Potvrdi i plati
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useStripe } from '@/composables/useStripe'
import { api } from '@/api/client'

const route = useRoute()
const property = ref<any>(null) // Replace with proper type

const booking = ref({
  checkIn: route.query.checkIn as string,
  checkOut: route.query.checkOut as string,
  guests: parseInt(route.query.guests as string)
})

const numberOfNights = computed(() => {
  const start = new Date(booking.value.checkIn)
  const end = new Date(booking.value.checkOut)
  const diffTime = Math.abs(end.getTime() - start.getTime())
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
})

const basePrice = computed(() => (property.value?.pricePerNight || 0) * numberOfNights.value)
const serviceFee = computed(() => basePrice.value * 0.1)
const totalPrice = computed(() => basePrice.value + serviceFee.value)

const formatPrice = (price: number) => new Intl.NumberFormat('bs-BA').format(price)
const formatDate = (dateString: string) => new Date(dateString).toLocaleDateString('bs-BA')

const { createElements, mountPaymentElement, confirmPayment } = useStripe()
const loading = ref(false)
const error = ref<string | null>(null)

const confirmBooking = async () => {
  loading.value = true
  error.value = null
  const { error: stripeError } = await confirmPayment(`${window.location.origin}/booking-success`)
  if (stripeError) {
    error.value = stripeError.message || 'Plaćanje nije uspjelo.'
    loading.value = false
  }
  // On success, Stripe redirects to the return_url
}


onMounted(async () => {
  // In a real app, you'd fetch property details by ID from the route params
  property.value = {
    id: route.params.id,
    title: 'Luksuzni apartman u srcu Sarajeva',
    propertyType: 'Cijeli apartman',
    averageRating: 4.92,
    totalReviews: 148,
    images: ['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800'],
    pricePerNight: 150,
  }

  try {
    // 1. Create Payment Intent on the backend
    const { clientSecret } = await api.post<{ clientSecret: string }>('/payments/create-intent', {
      amount: totalPrice.value * 100, // Stripe expects amount in cents
      currency: 'bam',
    })

    // 2. Create and mount Stripe Elements
    createElements(clientSecret)
    mountPaymentElement('#payment-element')

  } catch (err) {
    error.value = 'Greška prilikom pripreme plaćanja.'
  }
})
</script>

<style scoped>
.booking-confirmation-page { padding: var(--space-8) 0; }
.container { max-width: 1000px; }
h1 { font-size: var(--font-size-3xl); font-weight: 700; margin-bottom: var(--space-8); }

.confirmation-layout { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-12); align-items: start; }

.booking-details h2 { font-size: var(--font-size-2xl); font-weight: 600; margin-bottom: var(--space-6); }

.property-summary { display: flex; gap: var(--space-4); margin-bottom: var(--space-6); }
.property-image { width: 120px; height: 90px; object-fit: cover; border-radius: var(--radius-md); }
.property-info p { color: var(--color-text-secondary); font-size: var(--font-size-sm); }
.property-info h3 { font-weight: 600; }
.rating { display: flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-sm); }
.rating i { color: var(--color-primary); }

.divider { height: 1px; background: var(--color-border-light); margin: var(--space-6) 0; }

.trip-details h3, .price-summary h3 { font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4); }
.detail-item { display: flex; justify-content: space-between; margin-bottom: var(--space-2); }
.detail-item h4 { font-weight: 600; }

.price-item, .price-total { display: flex; justify-content: space-between; margin-bottom: var(--space-2); }
.price-total { font-weight: 700; font-size: var(--font-size-lg); border-top: 1px solid var(--color-border-light); padding-top: var(--space-3); margin-top: var(--space-3); }

.payment-section { padding: var(--space-8); }
.payment-section h3 { font-size: var(--font-size-2xl); font-weight: 600; margin-bottom: var(--space-2); }
.payment-section p { color: var(--color-text-secondary); margin-bottom: var(--space-6); }

#payment-element { min-height: 150px; margin-bottom: var(--space-6); }

.payment-btn { width: 100%; height: 48px; font-size: var(--font-size-lg); }

@media (max-width: 768px) {
  .confirmation-layout { grid-template-columns: 1fr; }
  .payment-section { order: -1; margin-bottom: var(--space-8); }
}
</style>
