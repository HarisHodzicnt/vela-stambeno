<template>
  <div class="home-view">
    <!-- Hero Section with Background -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-background"></div>
      
      <div class="hero-content">
        <div class="container">
          <div class="hero-text">
            <h1 class="hero-title">
              Pronađite savršeno mjesto za življenje
            </h1>
            <p class="hero-subtitle">
              Iznajmite ili kupite nekretninu u Bosni i Hercegovini
            </p>
          </div>
          
          <!-- Modern Search Bar -->
          <div class="search-container">
            <div class="search-tabs">
              <button 
                v-for="type in searchTypes" 
                :key="type.value"
                :class="['search-tab', { active: searchType === type.value }]"
                @click="searchType = type.value"
              >
                <i :class="type.icon"></i>
                {{ type.label }}
              </button>
            </div>
            
            <div class="search-bar">
              <div class="search-field">
                <label class="search-label">Lokacija</label>
                <input 
                  v-model="searchForm.location"
                  type="text" 
                  placeholder="Sarajevo, Banja Luka..."
                  class="search-input"
                />
              </div>
              
              <div class="search-divider"></div>
              
              <div v-if="searchType === 'rent'" class="search-field">
                <label class="search-label">Dolazak</label>
                <input 
                  v-model="searchForm.checkIn"
                  type="date" 
                  class="search-input"
                />
              </div>
              
              <div v-if="searchType === 'rent'" class="search-divider"></div>
              
              <div v-if="searchType === 'rent'" class="search-field">
                <label class="search-label">Odlazak</label>
                <input 
                  v-model="searchForm.checkOut"
                  type="date" 
                  class="search-input"
                />
              </div>
              
              <div class="search-divider"></div>
              
              <div class="search-field">
                <label class="search-label">Gosti</label>
                <select v-model="searchForm.guests" class="search-input">
                  <option value="1">1 gost</option>
                  <option value="2">2 gosta</option>
                  <option value="3">3 gosta</option>
                  <option value="4">4 gosta</option>
                  <option value="5">5+ gostiju</option>
                </select>
              </div>
              
              <button class="search-btn" @click="handleSearch">
                <i class="pi pi-search"></i>
                Pretraži
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Inspiracija Section -->
    <section class="inspiration-section">
      <div class="container">
        <h2 class="section-title">Inspiracija za vaš sljedeći odmor</h2>
        
        <div class="inspiration-grid">
          <div 
            v-for="destination in popularDestinations" 
            :key="destination.id"
            class="destination-card"
            @click="searchDestination(destination.city)"
          >
            <img :src="destination.image" :alt="destination.city" />
            <div class="destination-info">
              <h3>{{ destination.city }}</h3>
              <p>{{ destination.properties }} nekretnina</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Properties -->
    <section class="featured-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Izdvojene nekretnine</h2>
          <router-link to="/search" class="link-btn">
            Vidi sve <i class="pi pi-arrow-right"></i>
          </router-link>
        </div>

        <div class="properties-grid">
          <div 
            v-for="property in featuredProperties" 
            :key="property.id"
            class="property-card card card-hover"
            @click="viewProperty(property.id)"
          >
            <div class="property-image">
              <img :src="property.coverImageUrl || placeholderImage" :alt="property.title" />
              <button class="favorite-btn">
                <i class="pi pi-heart"></i>
              </button>
              <div class="property-badge">{{ property.listingType === 'rent' ? 'Iznajmljivanje' : 'Prodaja' }}</div>
            </div>
            
            <div class="property-content">
              <div class="property-header">
                <h3 class="property-title">{{ property.title }}</h3>
                <div class="property-rating">
                  <i class="pi pi-star-fill"></i>
                  <span>{{ property.averageRating || '5.0' }}</span>
                </div>
              </div>
              
              <p class="property-location">
                <i class="pi pi-map-marker"></i>
                {{ property.city }}
              </p>
              
              <div class="property-features">
                <span><i class="pi pi-home"></i> {{ property.bedrooms }} soba</span>
                <span><i class="pi pi-circle"></i> {{ property.sizeSqm }} m²</span>
              </div>
              
              <div class="property-price">
                <span class="price">
                  <span class="amount">{{ formatPrice(property.pricePerNight || property.salePrice) }} BAM</span>
                  <span v-if="property.listingType === 'rent'" class="period">/ noć</span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="container">
        <h2 class="section-title centered">Zašto stambeno.ba?</h2>
        
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="pi pi-verified"></i>
            </div>
            <h3>Provjereni vlasnici</h3>
            <p>Sve nekretnine su verificirane i provjerene</p>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">
              <i class="pi pi-shield"></i>
            </div>
            <h3>Sigurna plaćanja</h3>
            <p>Stripe integracija za potpunu sigurnost</p>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">
              <i class="pi pi-comments"></i>
            </div>
            <h3>24/7 Podrška</h3>
            <p>Tu smo za vas u svakom trenutku</p>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">
              <i class="pi pi-star"></i>
            </div>
            <h3>Najbolje cijene</h3>
            <p>Bez skrivenih naknada, transparentno</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-card">
          <div class="cta-content">
            <h2>Imate nekretninu za iznajmljivanje ili prodaju?</h2>
            <p>Pridružite se stambeno.ba i započnite zarađivati već danas</p>
            <button class="btn btn-primary" @click="$router.push('/register')">
              Dodaj nekretninu
              <i class="pi pi-arrow-right"></i>
            </button>
          </div>
          <div class="cta-image">
            <i class="pi pi-home" style="font-size: 120px; color: var(--color-primary);"></i>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/api/client'
import type { Property } from '@/types'

const router = useRouter()

// Search state
const searchType = ref('rent')
const searchTypes = [
  { value: 'rent', label: 'Iznajmi', icon: 'pi pi-calendar' },
  { value: 'sale', label: 'Kupi', icon: 'pi pi-home' },
  { value: 'both', label: 'Sve', icon: 'pi pi-list' }
]

const searchForm = ref({
  location: '',
  checkIn: '',
  checkOut: '',
  guests: '2'
})

// Featured properties (mock data for now)
const featuredProperties = ref<Property[]>([])
const placeholderImage = 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800'

// Popular destinations
const popularDestinations = ref([
  { id: 1, city: 'Sarajevo', properties: 450, image: 'https://images.unsplash.com/photo-1580416588375-bd716b962e5e?w=500' },
  { id: 2, city: 'Banja Luka', properties: 320, image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=500' },
  { id: 3, city: 'Mostar', properties: 180, image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=500' },
  { id: 4, city: 'Tuzla', properties: 210, image: 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=500' }
])

// Methods
const handleSearch = () => {
  router.push({
    path: '/search',
    query: {
      type: searchType.value,
      location: searchForm.value.location,
      checkIn: searchForm.value.checkIn,
      checkOut: searchForm.value.checkOut,
      guests: searchForm.value.guests
    }
  })
}

const searchDestination = (city: string) => {
  router.push({ path: '/search', query: { location: city } })
}

const viewProperty = (id: string) => {
  router.push(`/properties/${id}`)
}

const formatPrice = (price: number | undefined) => {
  if (!price) return '0'
  return new Intl.NumberFormat('bs-BA').format(price)
}

// Load featured properties
onMounted(async () => {
  try {
    // Fetch real properties from the API
    const data = await api.get('/properties')
    featuredProperties.value = data['hydra:member'] || data || []
  } catch (error) {
    console.error('Failed to fetch featured properties:', error)
    featuredProperties.value = []
  }
})
</script>

<style scoped>
.home-view {
  min-height: 100vh;
}

/* Hero Section */
.hero-section {
  position: relative;
  height: 70vh;
  min-height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920');
  background-size: cover;
  background-position: center;
  z-index: 1;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%, rgba(118, 75, 162, 0.85) 100%);
  z-index: 2;
}

.hero-content {
  position: relative;
  z-index: 3;
  width: 100%;
  padding: var(--space-8) 0;
}

.hero-text {
  text-align: center;
  margin-bottom: var(--space-8);
}

.hero-title {
  font-size: var(--font-size-5xl);
  font-weight: 800;
  color: white;
  margin-bottom: var(--space-4);
  line-height: 1.1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-subtitle {
  font-size: var(--font-size-xl);
  color: rgba(255, 255, 255, 0.95);
  font-weight: 400;
}

/* Search Container */
.search-container {
  max-width: 900px;
  margin: 0 auto;
}

.search-tabs {
  display: flex;
  gap: var(--space-2);
  margin-bottom: var(--space-4);
  justify-content: center;
}

.search-tab {
  padding: var(--space-3) var(--space-6);
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--radius-full);
  color: white;
  font-weight: 600;
  font-size: var(--font-size-base);
  cursor: pointer;
  transition: all var(--transition-base);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.search-tab:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
}

.search-tab.active {
  background: white;
  color: var(--color-primary);
  border-color: white;
}

.search-bar {
  background: white;
  border-radius: var(--radius-xl);
  padding: var(--space-4);
  display: flex;
  align-items: center;
  gap: var(--space-2);
  box-shadow: var(--shadow-xl);
}

.search-field {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  min-width: 0;
}

.search-label {
  font-size: var(--font-size-xs);
  font-weight: 700;
  color: var(--color-text-primary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.search-input {
  border: none;
  outline: none;
  font-size: var(--font-size-base);
  color: var(--color-text-primary);
  background: transparent;
  width: 100%;
}

.search-input::placeholder {
  color: var(--color-text-tertiary);
}

.search-divider {
  width: 1px;
  height: 40px;
  background: var(--color-border-light);
}

.search-btn {
  padding: var(--space-4) var(--space-8);
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
  color: white;
  border: none;
  border-radius: var(--radius-lg);
  font-weight: 700;
  font-size: var(--font-size-lg);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: var(--space-2);
  transition: all var(--transition-base);
  box-shadow: var(--shadow);
  white-space: nowrap;
}

.search-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* Inspiration Section */
.inspiration-section {
  padding: var(--space-16) 0;
  background: var(--color-bg-secondary);
}

.section-title {
  font-size: var(--font-size-3xl);
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: var(--space-8);
}

.section-title.centered {
  text-align: center;
}

.inspiration-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-6);
}

.destination-card {
  position: relative;
  border-radius: var(--radius-lg);
  overflow: hidden;
  cursor: pointer;
  transition: all var(--transition-base);
  height: 300px;
}

.destination-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

.destination-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.destination-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: var(--space-6);
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  color: white;
}

.destination-info h3 {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  margin-bottom: var(--space-1);
}

.destination-info p {
  font-size: var(--font-size-sm);
  opacity: 0.9;
}

/* Featured Section */
.featured-section {
  padding: var(--space-16) 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-8);
}

.link-btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
  transition: all var(--transition-base);
}

.link-btn:hover {
  gap: var(--space-3);
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--space-6);
}

.property-card {
  cursor: pointer;
  box-shadow: var(--shadow-sm);
}

.property-image {
  position: relative;
  height: 240px;
  overflow: hidden;
}

.property-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.property-card:hover .property-image img {
  transform: scale(1.05);
}

.favorite-btn {
  position: absolute;
  top: var(--space-3);
  right: var(--space-3);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition-base);
}

.favorite-btn:hover {
  background: white;
  transform: scale(1.1);
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
}

.property-content {
  padding: var(--space-4);
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
}

.property-rating {
  display: flex;
  align-items: center;
  gap: var(--space-1);
  color: var(--color-text-primary);
  font-weight: 600;
  font-size: var(--font-size-sm);
}

.property-rating i {
  color: #FFD700;
}

.property-location {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-3);
}

.property-features {
  display: flex;
  gap: var(--space-4);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-3);
}

.property-features span {
  display: flex;
  align-items: center;
  gap: var(--space-1);
}

.property-price {
  border-top: 1px solid var(--color-border-light);
  padding-top: var(--space-3);
}

.price {
  display: flex;
  align-items: baseline;
  gap: var(--space-1);
}

.amount {
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--color-text-primary);
}

.period {
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
}

/* Features Section */
.features-section {
  padding: var(--space-16) 0;
  background: var(--color-bg-secondary);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--space-8);
  margin-top: var(--space-12);
}

.feature-card {
  text-align: center;
}

.feature-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto var(--space-4);
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: white;
  box-shadow: var(--shadow-md);
}

.feature-card h3 {
  font-size: var(--font-size-xl);
  font-weight: 700;
  margin-bottom: var(--space-2);
  color: var(--color-text-primary);
}

.feature-card p {
  color: var(--color-text-secondary);
  line-height: 1.6;
}

/* CTA Section */
.cta-section {
  padding: var(--space-16) 0;
}

.cta-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: var(--radius-xl);
  padding: var(--space-12);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-8);
  box-shadow: var(--shadow-xl);
}

.cta-content {
  flex: 1;
  color: white;
}

.cta-content h2 {
  font-size: var(--font-size-4xl);
  font-weight: 800;
  margin-bottom: var(--space-4);
}

.cta-content p {
  font-size: var(--font-size-lg);
  margin-bottom: var(--space-6);
  opacity: 0.95;
}

.cta-content .btn {
  background: white;
  color: var(--color-primary);
}

.cta-content .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.cta-image {
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.2;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-title {
    font-size: var(--font-size-3xl);
  }

  .search-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .search-divider {
    display: none;
  }

  .search-btn {
    width: 100%;
    justify-content: center;
  }

  .cta-card {
    flex-direction: column;
    text-align: center;
  }

  .cta-image {
    display: none;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-4);
  }
}
</style>
