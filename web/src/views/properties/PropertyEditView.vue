<template>
  <div class="edit-property-container">
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Učitavam...</p>
    </div>
    <div v-else-if="!property" class="empty-state">
      <h3>Nekretnina nije pronađena</h3>
      <p>Tražena nekretnina ne postoji ili nemate pristup da je uređujete.</p>
      <router-link to="/dashboard/properties" class="btn btn-primary">Nazad na moje nekretnine</router-link>
    </div>
    <div v-else>
      <div class="page-header">
        <h1>Uredi Nekretninu</h1>
        <router-link to="/dashboard/properties" class="btn btn-secondary">Odustani</router-link>
      </div>

      <div class="stepper-header">
        <div v-for="(step, index) in steps" :key="index" class="step-item" :class="{ 'active': currentStep === index, 'completed': currentStep > index }">
          <div class="step-circle">{{ index + 1 }}</div>
          <div class="step-label">{{ step.title }}</div>
        </div>
      </div>

      <div class="step-content card">
        <h2 class="step-title">{{ steps[currentStep].title }}</h2>
        
        <!-- Step 1: Property Type -->
        <div v-if="currentStep === 0">
          <p class="step-description">Odaberite vrstu nekretnine.</p>
          <div class="property-type-grid">
            <div v-for="type in propertyTypes" :key="type.value" class="type-card" :class="{ 'selected': form.propertyType === type.value }" @click="form.propertyType = type.value">
              <i :class="`pi ${type.icon}`"></i>
              <span>{{ type.label }}</span>
            </div>
          </div>
        </div>

        <!-- Step 2: Location -->
        <div v-if="currentStep === 1">
          <p class="step-description">Unesite tačnu adresu vaše nekretnine.</p>
          <div class="form-group">
            <label for="address" class="form-label">Adresa</label>
            <input v-model="form.addressLine1" type="text" id="address" class="form-input" placeholder="Npr. Zmaja od Bosne bb" />
          </div>
          <div class="grid grid-cols-2 gap-6 mt-6">
            <div class="form-group">
              <label for="city" class="form-label">Grad</label>
              <input v-model="form.city" type="text" id="city" class="form-input" placeholder="Npr. Sarajevo" />
            </div>
            <div class="form-group">
              <label for="municipality" class="form-label">Općina</label>
              <input v-model="form.municipality" type="text" id="municipality" class="form-input" placeholder="Npr. Novo Sarajevo" />
            </div>
          </div>
        </div>

        <!-- Step 3: Details -->
        <div v-if="currentStep === 2">
          <p class="step-description">Unesite osnovne detalje o vašoj nekretnini.</p>
          <div class="details-grid">
            <CounterInput v-model="form.bedrooms" label="Spavaće sobe" :min="1" />
            <hr class="my-6" />
            <CounterInput v-model="form.bathrooms" label="Kupatila" :min="1" />
            <hr class="my-6" />
            <div class="form-group">
              <label for="size" class="form-label">Površina (m²)</label>
              <input v-model.number="form.sizeSqm" type="number" id="size" class="form-input" />
            </div>
          </div>
        </div>

        <!-- Step 4: Amenities -->
        <div v-if="currentStep === 3">
          <p class="step-description">Odaberite sve sadržaje koje vaša nekretnina nudi.</p>
          <div class="amenities-grid">
            <div v-for="amenity in availableAmenities" :key="amenity.value" class="amenity-item" :class="{ 'selected': form.amenities.includes(amenity.value) }" @click="toggleAmenity(amenity.value)">
              <i :class="`pi ${amenity.icon}`"></i>
              <span>{{ amenity.label }}</span>
            </div>
          </div>
        </div>

        <!-- Step 5: Photos -->
        <div v-if="currentStep === 4">
          <p class="step-description">Upravljajte slikama vaše nekretnine.</p>
          
          <!-- Existing Images -->
          <div v-if="existingImages.length > 0" class="existing-images-section">
            <h3 class="section-subtitle">Postojeće slike</h3>
            <div class="image-preview-grid">
              <div v-for="(image, index) in existingImages" :key="image.id" class="image-preview-item">
                <img :src="getImageUrl(image.url)" :alt="'Slika ' + (index + 1)" />
                <button type="button" @click="removeExistingImage(image.id)" class="remove-image-btn" title="Ukloni sliku">
                  <i class="pi pi-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Add New Images -->
          <div class="image-upload-container">
            <h3 class="section-subtitle">Dodaj nove slike</h3>
            <input
              type="file"
              ref="fileInput"
              @change="handleFileSelect"
              accept="image/jpeg,image/jpg,image/png,image/webp"
              multiple
              style="display: none"
            />
            <button type="button" @click="triggerFileInput" class="btn btn-secondary upload-btn">
              <i class="pi pi-cloud-upload"></i>
              Odaberite slike
            </button>
            
            <div v-if="newImages.length > 0" class="image-preview-grid mt-4">
              <div v-for="(image, index) in newImages" :key="index" class="image-preview-item">
                <img :src="image.preview" :alt="'Nova slika ' + (index + 1)" />
                <button type="button" @click="removeNewImage(index)" class="remove-image-btn">
                  <i class="pi pi-times"></i>
                </button>
              </div>
            </div>
            
            <p v-if="newImages.length > 0" class="image-count">{{ newImages.length }} nova/e slika/e</p>
          </div>
        </div>

        <!-- Step 6: Title & Description -->
        <div v-if="currentStep === 5">
          <p class="step-description">Uredite naslov i opis vaše nekretnine.</p>
          <div class="form-group">
            <label for="title" class="form-label">Naslov</label>
            <input v-model="form.title" type="text" id="title" class="form-input" placeholder="Npr. Moderan stan u centru grada" />
          </div>
          <div class="form-group mt-6">
            <label for="description" class="form-label">Opis</label>
            <textarea v-model="form.description" id="description" class="form-input" rows="6" placeholder="Npr. Prostran i svijetao stan sa prelijepim pogledom..."></textarea>
          </div>
        </div>

        <!-- Step 7: Price -->
        <div v-if="currentStep === 6">
          <p class="step-description">Uredite cijenu za vašu nekretninu.</p>
          <div class="listing-type-toggle">
            <button :class="{ 'active': form.listingType === 'rent' }" @click="form.listingType = 'rent'">Iznajmljivanje</button>
            <button :class="{ 'active': form.listingType === 'sale' }" @click="form.listingType = 'sale'">Prodaja</button>
          </div>

          <div v-if="form.listingType === 'rent'" class="form-group mt-6">
            <label for="pricePerNight" class="form-label">Cijena po noći (BAM)</label>
            <input v-model.number="form.pricePerNight" type="number" id="pricePerNight" class="form-input" />
          </div>

          <div v-if="form.listingType === 'sale'" class="form-group mt-6">
            <label for="salePrice" class="form-label">Prodajna cijena (BAM)</label>
            <input v-model.number="form.salePrice" type="number" id="salePrice" class="form-input" />
          </div>
        </div>

        <!-- Step 8: Review -->
        <div v-if="currentStep === 7">
          <p class="step-description">Pregledajte sve promjene prije spašavanja.</p>
          <PropertyPreview :property="form" :image-previews="allImagePreviews" />
        </div>
      </div>

      <div class="step-navigation">
        <button v-if="currentStep > 0" @click="prevStep" class="btn btn-secondary">Nazad</button>
        <button v-if="currentStep < steps.length - 1" @click="nextStep" class="btn btn-primary ml-auto">Dalje</button>
        <button v-if="currentStep === steps.length - 1" @click="saveChanges" class="btn btn-primary ml-auto" :disabled="saving">{{ saving ? 'Spašavam...' : 'Sačuvaj promjene' }}</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { api } from '@/api/client'
import { useAuthStore } from '@/stores/auth'
import type { Property, PropertyImage } from '@/types'
import CounterInput from '@/components/forms/CounterInput.vue'
import PropertyPreview from '@/components/properties/PropertyPreview.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const property = ref<Property | null>(null)
const loading = ref(true)
const saving = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)
const existingImages = ref<PropertyImage[]>([])
const newImages = ref<Array<{ file: File; preview: string }>>([])
const imagesToDelete = ref<string[]>([])

const currentStep = ref(0)
const steps = [
  { title: 'Vrsta Nekretnine' },
  { title: 'Lokacija' },
  { title: 'Detalji' },
  { title: 'Sadržaji' },
  { title: 'Slike' },
  { title: 'Naslov i Opis' },
  { title: 'Cijena' },
  { title: 'Pregled' },
]

const form = ref({
  propertyType: '',
  addressLine1: '',
  city: '',
  municipality: '',
  country: 'BA',
  bedrooms: 1,
  bathrooms: 1,
  sizeSqm: 50,
  amenities: [] as string[],
  title: '',
  description: '',
  listingType: 'rent' as 'rent' | 'sale',
  pricePerNight: null as number | null,
  salePrice: null as number | null,
})

const propertyTypes = [
  { label: 'Stan', value: 'apartment', icon: 'pi-building' },
  { label: 'Kuća', value: 'house', icon: 'pi-home' },
  { label: 'Studio', value: 'studio', icon: 'pi-key' },
  { label: 'Villa', value: 'villa', icon: 'pi-home' },
  { label: 'Poslovni Prostor', value: 'commercial', icon: 'pi-briefcase' },
]

const availableAmenities = [
  { label: 'Wi-Fi', value: 'wifi', icon: 'pi-wifi' },
  { label: 'Klima', value: 'ac', icon: 'pi-sun' },
  { label: 'Parking', value: 'parking', icon: 'pi-car' },
  { label: 'Balkon', value: 'balcony', icon: 'pi-th-large' },
  { label: 'Lift', value: 'elevator', icon: 'pi-sort-alt' },
  { label: 'Kuhinja', value: 'kitchen', icon: 'pi-box' },
  { label: 'TV', value: 'tv', icon: 'pi-desktop' },
  { label: 'Ljubimci Dozvoljeni', value: 'pets_allowed', icon: 'pi-prime' },
]

const allImagePreviews = computed(() => {
  const existing = existingImages.value.map(img => getImageUrl(img.url))
  const newPreviews = newImages.value.map(img => img.preview)
  return [...existing, ...newPreviews]
})

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

const toggleAmenity = (amenity: string) => {
  const index = form.value.amenities.indexOf(amenity)
  if (index === -1) {
    form.value.amenities.push(amenity)
  } else {
    form.value.amenities.splice(index, 1)
  }
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const files = target.files
  
  if (files) {
    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      const preview = URL.createObjectURL(file)
      newImages.value.push({ file, preview })
    }
  }
}

const removeNewImage = (index: number) => {
  URL.revokeObjectURL(newImages.value[index].preview)
  newImages.value.splice(index, 1)
}

const removeExistingImage = (imageId: string) => {
  imagesToDelete.value.push(imageId)
  existingImages.value = existingImages.value.filter(img => img.id !== imageId)
}

const getImageUrl = (url: string) => {
  if (url.startsWith('http')) return url
  return `http://localhost:8010${url}`
}

const saveChanges = async () => {
  saving.value = true
  try {
    const propertyId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id

    // Update property data
    const propertyData = {
      propertyType: form.value.propertyType,
      listingType: form.value.listingType,
      title: form.value.title,
      description: form.value.description,
      addressLine1: form.value.addressLine1,
      city: form.value.city,
      municipality: form.value.municipality,
      country: form.value.country,
      bedrooms: form.value.bedrooms,
      bathrooms: form.value.bathrooms.toString(),
      sizeSqm: form.value.sizeSqm.toString(),
      pricePerNight: form.value.listingType === 'rent' ? form.value.pricePerNight?.toString() : null,
      salePrice: form.value.listingType === 'sale' ? form.value.salePrice?.toString() : null,
    }

    await api.patch(`/properties/${propertyId}`, propertyData)

    // Delete marked images
    for (const imageId of imagesToDelete.value) {
      try {
        await api.delete(`/property_images/${imageId}`)
      } catch (error) {
        console.error('Failed to delete image:', error)
      }
    }

    // Upload new images
    if (newImages.value.length > 0) {
      const imageFormData = new FormData()
      newImages.value.forEach((img) => {
        imageFormData.append('images[]', img.file)
      })
      
      try {
        const token = localStorage.getItem('token')
        await fetch(`http://localhost:8010/api/properties/${propertyId}/images`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
          body: imageFormData,
        })
      } catch (error) {
        console.error('Failed to upload images:', error)
      }
    }
    
    // Redirect to property detail page
    router.push({ name: 'PropertyDetail', params: { id: propertyId } })
  } catch (error) {
    console.error('Failed to update property:', error)
    alert('Greška prilikom ažuriranja nekretnine. Molimo pokušajte ponovo.')
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  const propertyId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id

  if (!propertyId) {
    loading.value = false
    return
  }

  try {
    property.value = await api.get<Property>(`/properties/${propertyId}`)
    
    // Check if user is owner
    if (!authStore.user || property.value.owner.id !== authStore.user.id) {
      property.value = null
      loading.value = false
      return
    }

    // Populate form with existing data
    form.value = {
      propertyType: property.value.propertyType,
      addressLine1: property.value.addressLine1,
      city: property.value.city,
      municipality: property.value.municipality || '',
      country: property.value.country,
      bedrooms: property.value.bedrooms,
      bathrooms: property.value.bathrooms,
      sizeSqm: property.value.sizeSqm,
      amenities: [], // You'd need to parse amenities from the property
      title: property.value.title,
      description: property.value.description,
      listingType: property.value.listingType,
      pricePerNight: property.value.pricePerNight || null,
      salePrice: property.value.salePrice || null,
    }

    // Store existing images
    existingImages.value = [...(property.value.images || [])]
  } catch (error) {
    console.error('Failed to fetch property:', error)
    property.value = null
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.edit-property-container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }

.loading-state, .empty-state { text-align: center; padding: var(--space-16); }
.spinner { width: 48px; height: 48px; border: 4px solid var(--color-border-light); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto var(--space-4); }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state h3 { font-size: var(--font-size-2xl); font-weight: 700; margin-bottom: var(--space-2); }
.empty-state p { color: var(--color-text-secondary); margin-bottom: var(--space-6); }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 2rem; font-weight: 700; }

.stepper-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
.step-item { display: flex; align-items: center; flex-direction: column; color: #adb5bd; }
.step-item.active, .step-item.completed { color: #5a55ea; }
.step-circle { width: 30px; height: 30px; border-radius: 50%; background-color: #f1f3f5; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 2px solid #f1f3f5; }
.step-item.active .step-circle { background-color: #e8e7ff; border-color: #5a55ea; }
.step-item.completed .step-circle { background-color: #5a55ea; color: white; border-color: #5a55ea; }
.step-label { font-size: 0.875rem; margin-top: 0.5rem; }

.step-content { padding: 2rem; }
.step-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
.step-description { color: #6c757d; margin-bottom: 2rem; }

.section-subtitle { font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; }
.existing-images-section { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #e9ecef; }

.property-type-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
.type-card { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; border: 2px solid #e9ecef; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; }
.type-card:hover { border-color: #5a55ea; }
.type-card.selected { border-color: #5a55ea; background-color: #e8e7ff; }
.type-card i { font-size: 2rem; margin-bottom: 1rem; }

.amenities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; }
.amenity-item { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; border: 2px solid #e9ecef; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; text-align: center; }
.amenity-item:hover { border-color: #5a55ea; }
.amenity-item.selected { border-color: #5a55ea; background-color: #e8e7ff; }
.amenity-item i { font-size: 1.5rem; margin-bottom: 0.75rem; }

.listing-type-toggle { display: flex; border: 1px solid #ced4da; border-radius: 0.5rem; overflow: hidden; }
.listing-type-toggle button { flex: 1; padding: 1rem; border: none; background-color: white; cursor: pointer; font-weight: 600; transition: all 0.2s; }
.listing-type-toggle button.active { background-color: #5a55ea; color: white; }

.step-navigation { display: flex; justify-content: space-between; margin-top: 2rem; }
.ml-auto { margin-left: auto; }

.image-upload-container { text-align: center; }
.upload-btn { width: 100%; max-width: 300px; margin: 0 auto 2rem; padding: 1.5rem; font-size: 1rem; }

.image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 2rem; }
.image-preview-item { position: relative; aspect-ratio: 1; border-radius: 0.5rem; overflow: hidden; border: 2px solid #e9ecef; }
.image-preview-item img { width: 100%; height: 100%; object-fit: cover; }

.remove-image-btn { position: absolute; top: 0.5rem; right: 0.5rem; width: 32px; height: 32px; border-radius: 50%; background: rgba(220, 53, 69, 0.9); color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.remove-image-btn:hover { background: #c82333; transform: scale(1.1); }

.image-count { margin-top: 1rem; color: #6c757d; font-size: 0.875rem; }
.mt-4 { margin-top: 1rem; }
.mt-6 { margin-top: 1.5rem; }
.my-6 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
</style>
