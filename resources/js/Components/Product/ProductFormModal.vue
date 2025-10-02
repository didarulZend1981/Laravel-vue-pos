<!-- <template>
  <div v-if="show" class="custom-modal-backdrop">
    <div class="custom-modal">
      <form @submit.prevent="handleSubmit">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="btn btn-outline-info" @click="onClose">×</button>
        </div>

        <div class="modal-body">
          <div
            v-for="field in fields"
            :key="field.model"
            class="mb-2 d-flex align-items-center"
          >
            <label class="form-label me-3" :style="{ minWidth: '150px' }">
              {{ field.label }}
            </label>

            <input
              v-model="product[field.model]"
              :type="field.type || 'text'"
              class="form-control mb-2"
              :readonly="field.readonly || false"
              :placeholder="field.placeholder || ''"
            />
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-info" @click="onClose">Cancel</button>
          <button type="submit" class="btn btn-info">Add</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  title: {
    type: String,
    default: 'Product for Invoice',
  },
  product: Object,
  fields: {
    type: Array,
    default: () => [
      { label: 'Product Name', model: 'name', readonly: true },
      { label: 'Price', model: 'price', type: 'number' },
      { label: 'Qty', model: 'qty', type: 'number' },
      { label: 'Manufacture Date', model: 'ManufectureDate', type: 'date' },
      { label: 'Validate Time', model: 'ValidateTime', type: 'number' },
      { label: 'Expiry Date', model: 'ExpaireDate', type: 'date' },
    ],
  },
  onClose: Function,
  onSubmit: Function,
});

function handleSubmit() {
  console.log("🧾 Submitting product:", props.product);
  props.onSubmit?.();
}
</script>

<style scoped>
.custom-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.custom-modal {
  background: white;
  border-radius: 6px;
  padding: 20px;
  max-width: 500px;
  width: 100%;
}
</style> -->







<!-- ProductFormModal.vue -->

<template>
  <div v-if="show" class="custom-modal-backdrop">
    <div class="custom-modal">
      <form @submit.prevent="handleSubmit">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="btn btn-outline-info" @click="onClose">×</button>
        </div>

        <div class="modal-body">
          <div
            v-for="field in fields"
            :key="field.model"
            :class="field.wrapperClass || 'mb-2 d-flex align-items-center'"
            :style="field.wrapperStyle || {}"
          >
            <label
              :class="field.labelClass || 'form-label me-3'"
              :style="field.labelStyle || { minWidth: '150px' }"
            >
              {{ field.label }}
            </label>

            <input
              v-model="product[field.model]"
              :type="field.type || 'text'"
              :class="field.inputClass || 'form-control mb-2'"
              :style="field.inputStyle || {}"
              :readonly="field.readonly || false"
              :placeholder="field.placeholder || ''"
            />
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-info" @click="onClose">Cancel</button>
          <button type="submit" class="btn btn-info">Add</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  title: {
    type: String,
    default: 'Product for Invoice',
  },
  product: Object,
  fields: {
    type: Array,
    default: () => [],
  },
  onClose: Function,
  onSubmit: Function,
});

function handleSubmit() {
  console.log("🧾 Submitting product:", props.product);
  props.onSubmit?.();
}
</script>

<style scoped>
.custom-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.custom-modal {
  background: white;
  border-radius: 6px;
  padding: 20px;
  max-width: 600px;
  width: 100%;
}
</style>
