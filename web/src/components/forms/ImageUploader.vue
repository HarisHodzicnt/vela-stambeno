<template>
  <div class="image-uploader">
    <div class="dropzone" @dragover.prevent @drop.prevent="handleDrop" @click="openFileDialog">
      <i class="pi pi-cloud-upload"></i>
      <p>Prevucite slike ovdje ili kliknite za odabir</p>
      <input type="file" ref="fileInput" @change="handleFileChange" multiple accept="image/*" class="hidden-input" />
    </div>
    <div class="preview-grid">
      <div v-for="(image, index) in previews" :key="index" class="preview-item">
        <img :src="image.url" :alt="image.file.name" class="preview-image" />
        <button @click="removeImage(index)" class="remove-btn">
          <i class="pi pi-times"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const fileInput = ref<HTMLInputElement | null>(null);
const previews = ref<{ url: string; file: File }[]>([]);
const emit = defineEmits(['update:files']);

const openFileDialog = () => {
  fileInput.value?.click();
};

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    addFiles(Array.from(target.files));
  }
};

const handleDrop = (event: DragEvent) => {
  if (event.dataTransfer?.files) {
    addFiles(Array.from(event.dataTransfer.files));
  }
};

const addFiles = (files: File[]) => {
  for (const file of files) {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        previews.value.push({ url: e.target?.result as string, file });
        emitFiles();
      };
      reader.readAsDataURL(file);
    }
  }
};

const removeImage = (index: number) => {
  previews.value.splice(index, 1);
  emitFiles();
};

const emitFiles = () => {
  emit('update:files', previews.value.map(p => p.file));
};
</script>

<style scoped>
.image-uploader { width: 100%; }
.dropzone {
  border: 2px dashed #ced4da;
  border-radius: 0.5rem;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}
.dropzone:hover { border-color: #5a55ea; }
.dropzone i { font-size: 2.5rem; color: #868e96; }
.dropzone p { margin-top: 1rem; color: #6c757d; }
.hidden-input { display: none; }

.preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 1rem;
  margin-top: 2rem;
}

.preview-item {
  position: relative;
}

.preview-image {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 0.5rem;
}

.remove-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.6);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
