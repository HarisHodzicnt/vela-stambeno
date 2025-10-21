<template>
  <div class="counter-input">
    <label class="form-label">{{ label }}</label>
    <div class="counter-controls">
      <button type="button" @click="decrement" :disabled="modelValue <= min" class="control-btn">
        <i class="pi pi-minus"></i>
      </button>
      <span class="counter-value">{{ modelValue }}</span>
      <button type="button" @click="increment" class="control-btn">
        <i class="pi pi-plus"></i>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  modelValue: { type: Number, required: true },
  label: { type: String, required: true },
  min: { type: Number, default: 0 },
});

const emit = defineEmits(['update:modelValue']);

const increment = () => {
  emit('update:modelValue', props.modelValue + 1);
};

const decrement = () => {
  if (props.modelValue > props.min) {
    emit('update:modelValue', props.modelValue - 1);
  }
};
</script>

<style scoped>
.counter-input {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.form-label {
  font-weight: 600;
}

.counter-controls {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.control-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 1.5px solid #ced4da;
  background-color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  font-size: 1rem;
  color: #495057;
}

.control-btn i {
  font-size: 0.875rem;
  font-weight: bold;
}

.control-btn:hover {
  border-color: #5a55ea;
  background-color: #f8f9fa;
}

.control-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.counter-value {
  font-size: 1.25rem;
  font-weight: 600;
  min-width: 30px;
  text-align: center;
}
</style>
