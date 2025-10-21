<template>
  <div class="search-view">
    <!-- Search Bar -->
    <div class="search-header">
      <div class="container">
        <div class="search-bar-compact">
          <div class="search-field">
            <input 
              v-model="filters.location"
              type="text" 
              placeholder="Lokacija"
              class="search-input"
              @input="handleSearch"
            />
          </div>
          
          <div v-if="filters.listingType === 'rent'" class="search-field">
            <input 
              v-model="filters.checkIn"
              type="date" 
              class="search-input"
              @change="handleSearch"
            />
          </div>
          
          <div v-if="filters.listingType === 'rent'" class="search-field">
            <input 
              v-model="filters.checkOut"
              type="date" 
              class="search-input"
              @change="handleSearch"
            />
          </div>
          
          <button class="filter-toggle-btn" @click="showFilters = !showFilters">
            <i class="pi pi-sliders-h"></i>
            Filteri
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="search-content">
      <div class="container">
        <div class="search-layout">
          <!-- Filters Sidebar -->
          <aside :class="['filters-sidebar', { 'filters-open': showFilters }]">
            <div class="filters-header">
              <h3>Filteri</h3>
              <button class="close-filters" @click="showFilters = false">
                <i class="pi pi-times"></i>
              </button>
            </div>
            
            <div class="filters-content">
              <!-- Listing Type -->
              <div class="filter-group">
                <label for="listingType" class="filter-label">Tip oglasa</label>
                <div class="filter-pills">
                  <button 
                    v-for="type in listingTypes"
                    :key="type.value"
                    :class="['pill', { active: filters.listingType === type.value }]"
                    @click="filters.listingType = type.value; handleSearch()"
                  >
                    {{ type.label }}
                  </button>
                </div>
              </div>

              <!-- Property Type -->
              <div class="filter-group">
                <label for="propertyTypes" class="filter-label">Tip nekretnine</label>
                <div class="filter-checkboxes">
                  <label 
                    v-for="type in propertyTypes"
                    :key="type.value"
                    class="checkbox-label"
                  >
                    <input 
                      v-model="filters.propertyTypes" 
                      type="checkbox" 
                      :value="type.value"
                      @change="handleSearch"
                    />
                    <span>{{ type.label }}</span>
                  </label>
                </div>
              </div>

              <!-- Price Range -->
              <div class="filter-group">
                <label for="priceMin" class="filter-label">
                  Cijena {{ filters.listingType === 'rent' ? '(po noći)' : '' }}
                </label>
                <div class="price-inputs">
                  <input 
                    v-model.number="filters.priceMin"
                    type="number" 
                    placeholder="Min"
                    class="price-input"
                    @input="handleSearch"
                  />
                  <span>-</span>
                  <input 
                    v-model.number="filters.priceMax"
                    type="number" 
                    placeholder="Max"
                    class="price-input"
                    @input="handleSearch"
                  />
                </div>
              </div>

              <!-- Bedrooms -->
              <div class="filter-group">
                <label for="bedrooms" class="filter-label">Broj soba</label>
                <div class="filter-pills">
                  <button 
                    v-for="num in [1, 2, 3, 4, 5]"
                    :key="num"
                    :class="['pill', { active: filters.bedrooms === num }]"
                    @click="filters.bedrooms = filters.bedrooms === num ? null : num; handleSearch()"
                  >
                    {{ num }}{{ num === 5 ? '+' : '' }}
                  </button>
                </div>
              </div>

              <!-- Bathrooms -->
              <div class="filter-group">
                <label for="bathrooms" class="filter-label">Broj kupatila</label>
                <div class="filter-pills">
                  <button 
                    v-for="num in [1, 2, 3]"
                    :key="num"
                    :class="['pill', { active: filters.bathrooms === num }]"
                    @click="filters.bathrooms = filters.bathrooms === num ? null : num; handleSearch()"
                  >
                    {{ num }}{{ num === 3 ? '+' : '' }}
                  </button>
                </div>
              </div>

              <!-- Amenities -->
              <div class="filter-group">
                <label for="amenities" class="filter-label">Pogodnosti</label>
                <div class="filter-checkboxes">
                  <label 
                    v-for="amenity in amenities"
                    :key="amenity.value"
                    class="checkbox-label"
                  >
                    <input 
                      v-model="filters.amenities" 
                      type="checkbox" 
                      :value="amenity.value"
                      @change="handleSearch"
                    />
                    <span>
                      <i :class="amenity.icon"></i>
                      {{ amenity.label }}
                    </span>
                  </label>
                </div>
              </div>

              <!-- Instant Book -->
              <div v-if="filters.listingType === 'rent'" class="filter-group">
                <label class="checkbox-label special">
                  <input 
                    v-model="filters.instantBook" 
                    type="checkbox"
                    @change="handleSearch"
                  />
                  <span>
                    <i class="pi pi-bolt"></i>
                    Instant booking
                  </span>
                </label>
              </div>

              <!-- Reset Button -->
              <button class="btn-reset" @click="resetFilters">
                <i class="pi pi-refresh"></i>
                Resetuj filtere
              </button>
            </div>
          </aside>

          <!-- Results Section -->
          <main class="results-section">
            <!-- Results Header -->
            <div class="results-header">
              <div class="results-count">
                <h2>{{ totalResults }} {{ totalResults === 1 ? 'nekretnina' : 'nekretnina' }}</h2>
                <p v-if="filters.location">u {{ filters.location }}</p>
              </div>
              
              <div class="results-controls">
                <select v-model="sortBy" @change="handleSort" class="sort-select">
                  <option value="recommended">Preporučeno</option>
                  <option value="price_asc">Cijena: Najniža</option>
                  <option value="price_desc">Cijena: Najviša</option>
                  <option value="rating">Ocjena</option>
                  <option value="newest">Najnovije</option>
                </select>
                
                <div class="view-toggle">
                  <button 
                    :class="['view-btn', { active: viewMode === 'grid' }]"
                    @click="viewMode = 'grid'"
                  >
                    <i class="pi pi-th-large"></i>
                  </button>
                  <button 
                    :class="['view-btn', { active: viewMode === 'map' }]"
                    @click="viewMode = 'map'"
                  >
                    <i class="pi pi-map"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <div class="spinner"></div>
              <p>Učitavam nekretnine...</p>
            </div>

            <!-- Results Grid -->
            <div v-else-if="properties.length > 0" :class="['results-grid', viewMode]">
              <PropertyCard 
                v-for="property in properties"
                :key="property.id"
                :property="property"
              />
            </div>

            <!-- Empty State -->
            <div v-else class="empty-state">
              <i class="pi pi-search"></i>
              <h3>Nema rezultata</h3>
              <p>Pokušajte promijeniti filtere ili pretragu</p>
              <button class="btn btn-primary" @click="resetFilters">
                Resetuj filtere
              </button>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="pagination">
              <button 
                class="page-btn"
                :disabled="currentPage === 1"
                @click="goToPage(currentPage - 1)"
              >
                <i class="pi pi-chevron-left"></i>
              </button>
              
              <button 
                v-for="page in visiblePages"
                :key="page"
                :class="['page-btn', { active: page === currentPage }]"
                @click="goToPage(page)"
              >
                {{ page }}
              </button>
              
              <button 
                class="page-btn"
                :disabled="currentPage === totalPages"
                @click="goToPage(currentPage + 1)"
              >
                <i class="pi pi-chevron-right"></i>
              </button>
            </div>
          </main>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '@/api/client'
import PropertyCard from '@/components/properties/PropertyCard.vue'
import type { Property } from '@/types'

const route = useRoute()
const router = useRouter()

// State
const showFilters = ref(false)
const viewMode = ref<'grid' | 'map'>('grid')
const loading = ref(false)
const sortBy = ref('recommended')
const currentPage = ref(1)
const totalResults = ref(0)
const totalPages = ref(0)
const properties = ref<Property[]>([])

// Filter options
const listingTypes = [
  { value: 'rent', label: 'Iznajmljivanje' },
  { value: 'sale', label: 'Prodaja' },
  { value: 'both', label: 'Sve' }
]

const propertyTypes = [
  { value: 'apartment', label: 'Stan' },
  { value: 'house', label: 'Kuća' },
  { value: 'room', label: 'Soba' },
  { value: 'studio', label: 'Studio' },
  { value: 'villa', label: 'Vila' }
]

const amenities = [
  { value: 'wifi', label: 'WiFi', icon: 'pi pi-wifi' },
  { value: 'parking', label: 'Parking', icon: 'pi pi-car' },
  { value: 'ac', label: 'Klima', icon: 'pi pi-sun' },
  { value: 'heating', label: 'Grijanje', icon: 'pi pi-fire' },
  { value: 'kitchen', label: 'Kuhinja', icon: 'pi pi-home' },
  { value: 'washer', label: 'Veš mašina', icon: 'pi pi-circle' },
  { value: 'tv', label: 'TV', icon: 'pi pi-desktop' },
  { value: 'balcony', label: 'Balkon', icon: 'pi pi-th-large' }
]

// Filters
const filters = ref({
  location: route.query.location as string || '',
  listingType: route.query.type as string || 'both',
  checkIn: route.query.checkIn as string || '',
  checkOut: route.query.checkOut as string || '',
  propertyTypes: [] as string[],
  priceMin: null as number | null,
  priceMax: null as number | null,
  bedrooms: null as number | null,
  bathrooms: null as number | null,
  amenities: [] as string[],
  instantBook: false
})

// Computed
const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const handleSearch = async () => {
  loading.value = true
  currentPage.value = 1
  
  // Update URL with filters
  router.push({
    query: {
      ...route.query,
      location: filters.value.location || undefined,
      type: filters.value.listingType,
      checkIn: filters.value.checkIn || undefined,
      checkOut: filters.value.checkOut || undefined
    }
  })
  
  try {
    // Fetch real properties from the API
    const data = await api.get('/properties')
    properties.value = data['hydra:member'] || data || []
    totalResults.value = properties.value.length
    totalPages.value = Math.ceil(totalResults.value / 12)
  } catch (error) {
    console.error('Failed to fetch properties:', error)
    properties.value = []
    totalResults.value = 0
    totalPages.value = 0
  } finally {
    loading.value = false
  }
}

const handleSort = () => {
  // TODO: Implement sorting
  handleSearch()
}

const resetFilters = () => {
  filters.value = {
    location: '',
    listingType: 'both',
    checkIn: '',
    checkOut: '',
    propertyTypes: [],
    priceMin: null,
    priceMax: null,
    bedrooms: null,
    bathrooms: null,
    amenities: [],
    instantBook: false
  }
  handleSearch()
}

const goToPage = (page: number) => {
  currentPage.value = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
  handleSearch()
}

// Lifecycle
onMounted(() => {
  handleSearch()
})

// Watch for route changes
watch(() => route.query, () => {
  if (route.query.location) {
    filters.value.location = route.query.location as string
  }
})
</script>

<style scoped>
.search-view {
  min-height: 100vh;
  background: var(--color-bg-secondary);
}

/* Search Header */
.search-header {
  background: white;
  border-bottom: 1px solid var(--color-border-light);
  padding: var(--space-4) 0;
  position: sticky;
  top: 80px;
  z-index: 50;
}

.search-bar-compact {
  display: flex;
  gap: var(--space-2);
  align-items: center;
}

.search-field {
  flex: 1;
  min-width: 0;
}

.search-input {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  font-size: var(--font-size-sm);
}

.filter-toggle-btn {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-4);
  background: white;
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  cursor: pointer;
  font-weight: 600;
  transition: all var(--transition-base);
}

.filter-toggle-btn:hover {
  border-color: var(--color-text-primary);
}

/* Main Layout */
.search-content {
  padding: var(--space-6) 0;
}

.search-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: var(--space-6);
  align-items: start;
}

/* Filters Sidebar */
.filters-sidebar {
  background: white;
  border-radius: var(--radius-md);
  padding: var(--space-6);
  position: sticky;
  top: 160px;
  max-height: calc(100vh - 180px);
  overflow-y: auto;
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-6);
}

.filters-header h3 {
  font-size: var(--font-size-xl);
  font-weight: 700;
}

.close-filters {
  display: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: var(--color-bg-secondary);
  cursor: pointer;
  align-items: center;
  justify-content: center;
}

.filter-group {
  margin-bottom: var(--space-6);
  padding-bottom: var(--space-6);
  border-bottom: 1px solid var(--color-border-light);
}

.filter-group:last-child {
  border-bottom: none;
}

.filter-label {
  display: block;
  font-weight: 600;
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-3);
  color: var(--color-text-primary);
}

.filter-pills {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
}

.pill {
  padding: var(--space-2) var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-full);
  background: white;
  cursor: pointer;
  font-size: var(--font-size-sm);
  font-weight: 500;
  transition: all var(--transition-base);
}

.pill:hover {
  border-color: var(--color-text-primary);
}

.pill.active {
  background: var(--color-text-primary);
  color: white;
  border-color: var(--color-text-primary);
}

.filter-checkboxes {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  cursor: pointer;
  font-size: var(--font-size-sm);
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkbox-label.special {
  padding: var(--space-3);
  background: var(--color-bg-secondary);
  border-radius: var(--radius);
  font-weight: 600;
}

.price-inputs {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.price-input {
  flex: 1;
  padding: var(--space-2) var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  font-size: var(--font-size-sm);
}

.btn-reset {
  width: 100%;
  padding: var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  background: white;
  cursor: pointer;
  font-weight: 600;
  transition: all var(--transition-base);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
}

.btn-reset:hover {
  background: var(--color-bg-secondary);
}

/* Results Section */
.results-section {
  min-height: 600px;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-6);
}

.results-count h2 {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  margin-bottom: var(--space-1);
}

.results-count p {
  color: var(--color-text-secondary);
}

.results-controls {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.sort-select {
  padding: var(--space-2) var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  font-size: var(--font-size-sm);
  font-weight: 500;
  cursor: pointer;
}

.view-toggle {
  display: flex;
  gap: var(--space-1);
  background: var(--color-bg-secondary);
  padding: var(--space-1);
  border-radius: var(--radius);
}

.view-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: transparent;
  border-radius: var(--radius-sm);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition-base);
}

.view-btn.active {
  background: white;
  box-shadow: var(--shadow-sm);
}

/* Results Grid */
.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--space-6);
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: var(--space-16);
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid var(--color-border-light);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto var(--space-4);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: var(--space-16);
  background: white;
  border-radius: var(--radius-md);
}

.empty-state i {
  font-size: 64px;
  color: var(--color-text-tertiary);
  margin-bottom: var(--space-4);
}

.empty-state h3 {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  margin-bottom: var(--space-2);
}

.empty-state p {
  color: var(--color-text-secondary);
  margin-bottom: var(--space-6);
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: var(--space-2);
  margin-top: var(--space-8);
}

.page-btn {
  min-width: 40px;
  height: 40px;
  padding: 0 var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  background: white;
  cursor: pointer;
  font-weight: 600;
  transition: all var(--transition-base);
}

.page-btn:hover:not(:disabled) {
  border-color: var(--color-text-primary);
}

.page-btn.active {
  background: var(--color-text-primary);
  color: white;
  border-color: var(--color-text-primary);
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1024px) {
  .search-layout {
    grid-template-columns: 1fr;
  }
  
  .filters-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 320px;
    max-width: 90%;
    z-index: 100;
    transform: translateX(-100%);
    transition: transform var(--transition-base);
    max-height: 100vh;
  }
  
  .filters-sidebar.filters-open {
    transform: translateX(0);
  }
  
  .close-filters {
    display: flex;
  }
  
  .filter-toggle-btn {
    display: flex;
  }
}

@media (max-width: 768px) {
  .search-bar-compact {
    flex-wrap: wrap;
  }
  
  .results-header {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-4);
  }
  
  .results-controls {
    width: 100%;
    justify-content: space-between;
  }
  
  .results-grid {
    grid-template-columns: 1fr;
  }
}
</style>
