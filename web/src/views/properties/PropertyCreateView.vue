<template>
  <div class="create-property-container">
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
        <p class="step-description">Odaberite vrstu nekretnine koju želite objaviti.</p>
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
        <p class="step-description">Dodajte slike vaše nekretnine. Preporučujemo barem 3 slike.</p>
        <div class="image-upload-container">
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
          
          <div v-if="selectedImages.length > 0" class="image-preview-grid">
            <div v-for="(image, index) in selectedImages" :key="index" class="image-preview-item">
              <img :src="image.preview" :alt="'Slika ' + (index + 1)" />
              <button type="button" @click="removeImage(index)" class="remove-image-btn">
                <i class="pi pi-times"></i>
              </button>
            </div>
          </div>
          
          <p v-if="selectedImages.length > 0" class="image-count">{{ selectedImages.length }} slika/e odabrano</p>
        </div>
      </div>

      <!-- Step 6: Title & Description -->
      <div v-if="currentStep === 5">
        <p class="step-description">Dajte vašoj nekretnini privlačan naslov i detaljan opis.</p>
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
        <p class="step-description">Odredite cijenu za vašu nekretninu.</p>
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
        <p class="step-description">Pregledajte sve unesene informacije prije objavljivanja.</p>
        <PropertyPreview :property="form" :image-previews="imagePreviewUrls" />
      </div>

    </div>

    <div class="step-navigation">
      <button v-if="currentStep > 0" @click="prevStep" class="btn btn-secondary">Nazad</button>
      <button v-if="currentStep < steps.length - 1" @click="nextStep" class="btn btn-primary ml-auto">Dalje</button>
      <button v-if="currentStep === steps.length - 1" @click="publishProperty" class="btn btn-primary ml-auto">Objavi</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '@/api/client';
import CounterInput from '@/components/forms/CounterInput.vue';
import PropertyPreview from '@/components/properties/PropertyPreview.vue';

const router = useRouter();
const fileInput = ref<HTMLInputElement | null>(null);
const selectedImages = ref<Array<{ file: File; preview: string }>>([]);

const imagePreviewUrls = computed(() => {
  return selectedImages.value.map(img => img.preview);
});

const currentStep = ref(0);
const steps = [
  { title: 'Vrsta Nekretnine' },
  { title: 'Lokacija' },
  { title: 'Detalji' },
  { title: 'Sadržaji' },
  { title: 'Slike' },
  { title: 'Naslov i Opis' },
  { title: 'Cijena' },
  { title: 'Pregled' },
];

const form = ref({
  propertyType: '',
  addressLine1: '',
  city: '',
  municipality: '',
  country: 'BA',
  bedrooms: 1,
  bathrooms: 1,
  sizeSqm: 50,
  amenities: [],
  images: [],
  title: '',
  description: '',
  listingType: 'rent',
  pricePerNight: null,
  salePrice: null,
});

const propertyTypes = [
  { label: 'Stan', value: 'apartment', icon: 'pi-building' },
  { label: 'Kuća', value: 'house', icon: 'pi-home' },
  { label: 'Soba', value: 'room', icon: 'pi-key' },
  { label: 'Poslovni Prostor', value: 'commercial', icon: 'pi-briefcase' },
];

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const availableAmenities = [
  { label: 'Wi-Fi', value: 'wifi', icon: 'pi-wifi' },
  { label: 'Klima', value: 'ac', icon: 'pi-sun' },
  { label: 'Parking', value: 'parking', icon: 'pi-car' },
  { label: 'Balkon', value: 'balcony', icon: 'pi-th-large' },
  { label: 'Lift', value: 'elevator', icon: 'pi-sort-alt' },
  { label: 'Kuhinja', value: 'kitchen', icon: 'pi-box' },
  { label: 'TV', value: 'tv', icon: 'pi-desktop' },
  { label: 'Ljubimci Dozvoljeni', value: 'pets_allowed', icon: 'pi-prime' },
];

const toggleAmenity = (amenity: string) => {
  const index = form.value.amenities.indexOf(amenity);
  if (index === -1) {
    form.value.amenities.push(amenity);
  } else {
    form.value.amenities.splice(index, 1);
  }
};

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const files = target.files;
  
  if (files) {
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      const preview = URL.createObjectURL(file);
      selectedImages.value.push({ file, preview });
    }
  }
};

const removeImage = (index: number) => {
  URL.revokeObjectURL(selectedImages.value[index].preview);
  selectedImages.value.splice(index, 1);
};

const publishProperty = async () => {
  try {
    // Prepare the property data as JSON
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
      instantBook: false,
    };

    // Use the api client which properly handles JSON and auth
    const response = await api.post('/properties', propertyData);
    
    console.log('Property created successfully:', response);
    
    // Upload images if any
    if (selectedImages.value.length > 0) {
      const imageFormData = new FormData();
      selectedImages.value.forEach((img) => {
        imageFormData.append('images[]', img.file);
      });
      
      try {
        const token = localStorage.getItem('token');
        await fetch(`http://localhost:8010/api/properties/${response.id}/images`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
          body: imageFormData,
        });
      } catch (error) {
        console.error('Failed to upload images:', error);
      }
    }
    
    // Redirect to dashboard on success
    router.push({ name: 'Dashboard' });
  } catch (error) {
    console.error('Failed to create property:', error);
    alert('Greška prilikom kreiranja nekretnine. Molimo pokušajte ponovo.');
  }
};
</script>

<style scoped>
.create-property-container {
  max-width: 800px;
  margin: 2rem auto;
}

.stepper-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2rem;
}

.step-item {
  display: flex;
  align-items: center;
  flex-direction: column;
  color: #adb5bd;
}

.step-item.active, .step-item.completed {
  color: #5a55ea;
}

.step-circle {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: #f1f3f5;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  border: 2px solid #f1f3f5;
}

.step-item.active .step-circle {
  background-color: #e8e7ff;
  border-color: #5a55ea;
}

.step-item.completed .step-circle {
  background-color: #5a55ea;
  color: white;
  border-color: #5a55ea;
}

.step-label {
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.step-content {
  padding: 2rem;
}

.step-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.step-description {
  color: #6c757d;
  margin-bottom: 2rem;
}

.property-type-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.type-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  border: 2px solid #e9ecef;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.type-card:hover {
  border-color: #5a55ea;
}

.type-card.selected {
  border-color: #5a55ea;
  background-color: #e8e7ff;
}

.type-card i {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.amenities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}

.amenity-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  border: 2px solid #e9ecef;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.amenity-item:hover {
  border-color: #5a55ea;
}

.amenity-item.selected {
  border-color: #5a55ea;
  background-color: #e8e7ff;
}

.amenity-item i {
  font-size: 1.5rem;
  margin-bottom: 0.75rem;
}

.listing-type-toggle {
  display: flex;
  border: 1px solid #ced4da;
  border-radius: 0.5rem;
  overflow: hidden;
}

.listing-type-toggle button {
  flex: 1;
  padding: 1rem;
  border: none;
  background-color: white;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.listing-type-toggle button.active {
  background-color: #5a55ea;
  color: white;
}

.step-navigation {
  display: flex;
  justify-content: space-between;
  margin-top: 2rem;
}

.image-upload-container {
  text-align: center;
}

.upload-btn {
  width: 100%;
  max-width: 300px;
  margin: 0 auto 2rem;
  padding: 1.5rem;
  font-size: 1rem;
}

.image-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
  margin-top: 2rem;
}

.image-preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 0.5rem;
  overflow: hidden;
  border: 2px solid #e9ecef;
}

.image-preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.remove-image-btn:hover {
  background: #e74c3c;
  transform: scale(1.1);
}

.image-count {
  margin-top: 1rem;
  color: #6c757d;
  font-size: 0.875rem;
}
</style>
