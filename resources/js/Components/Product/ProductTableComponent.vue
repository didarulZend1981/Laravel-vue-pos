<template>
  <div>
    <!-- Search and Table -->
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-info">Pick Product</h6>
        <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text" v-model="productSearchValue" />
      </div>
      <div class="card-body">
        <EasyDataTable
          buttons-pagination
          alternating
          :headers="productHeader"
          :items="productItems"
          border-cell
          theme-color="#36b9cc"
          :rows-per-page="10"
          :search-field="productSearchField"
          :search-value="productSearchValue"
        >
          <template #item-image="{ image }">
            <img
              :src="image ? `/storage/${image}` : 'https://skala.or.id/wp-content/uploads/2024/01/dummy-post-square-1-1.jpg'"
              alt="Product Image"
              style="width: 50px; height: 50px; object-fit: cover;"
              class="p-1"
            />
          </template>

          <template #item-number="{ id }">
            <button class="btn btn-sm btn-outline-success" @click="openCustomizationModal(id)">
              <i class="fa fa-plus"></i>
            </button>
          </template>
        </EasyDataTable>
      </div>
    </div>

    <!-- Modal -->
<div v-if="showModal" class="custom-modal-backdrop">
  <div class="custom-modal">
    <div class="modal-header">
      <h5 class="modal-title">Product for Invoice</h5>
      <button type="button" class="btn btn-outline-info" @click="closeModal">×</button>
    </div>
    <div class="modal-body">
      <form @submit.prevent="addProductToInvoice">
        <div class="mb-3">
          <input type="text" class="form-control" v-model="selectedProduct.name" readonly />
        </div>
        <div class="mb-3">
          <input type="number" class="form-control" v-model="selectedProduct.price" placeholder="Product Price" />
        </div>
        <div class="mb-3">
          <input type="number" class="form-control" v-model="selectedProduct.qty" placeholder="Quantity" />
        </div>
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-outline-info mr-3" @click="closeModal">Cancel</button>
          <button type="submit" class="btn btn-info">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  products: Array
});

const emit = defineEmits(['add-to-invoice']);

// Table headers
const productHeader = [
  { text: "Image", value: "image" },
  { text: "Name", value: "name" },
  { text: "Price", value: "price" },
  { text: "Qty", value: "qty" },
  { text: "Action", value: "number" },
];

// Reactive search and table items
const productSearchValue = ref('');
const productSearchField = ref(['name', 'price']);
const products = props.products || [];

const productItems = computed(() => {
  return products.map(p => ({
    image: p.image,
    name: p.name,
    price: p.price,
    qty: p.stock,
    id: p.id
  }));
});

// Modal logic
const selectedProduct = ref({ name: '', price: 0, qty: 1 });



const showModal = ref(false); // Control modal visibility

function openCustomizationModal(productId) {
  const product = props.products.find(p => p.id === productId);
  if (product) {
    selectedProduct.value = { ...product, qty: 1 };
    showModal.value = true;
  }
}

function closeModal() {
  showModal.value = false;
}

function addProductToInvoice() {
  const newProduct = {
    name: selectedProduct.value.name,
    qty: selectedProduct.value.qty,
    purchase_price: selectedProduct.value.price,
    total: selectedProduct.value.qty * selectedProduct.value.price,
    product_id: selectedProduct.value.id,
  };

  emit('add-to-invoice', newProduct);
  closeModal(); // Hide modal
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
</style>
