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


        <ProductFormModal
            :show="showModal"
            :product="selectedProduct"
            :onClose="closeModal"
            :onSubmit="addProductToInvoice"
            title="Add Product to Purchess"
            :fields="[
                { label: 'Product Name', model: 'name', readonly: true },
                { label: 'Price', model: 'price', type: 'number' },
                { label: 'Qty', model: 'qty', type: 'number' },
                { label: 'Manufacture Date', model: 'ManufectureDate', type: 'date' },
                { label: 'Validate Time', model: 'ValidateTime', type: 'number' },
                { label: 'Expiry Date', model: 'ExpaireDate', type: 'date' }
            ]"
        />





  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import ProductFormModal from './ProductFormModal.vue';



// তারিখ সংরক্ষণ
const selectedDate = ref('')
const selectedExp = ref('')



const props = defineProps({
  products: Array
});

const emit = defineEmits(['add-to-invoice']);

// Table headers
const productHeader = [
  { text: "Image", value: "image" },
  { text: "Name", value: "name" },
//   { text: "Price", value: "price" },
  { text: "Qty", value: "qty" },
  { text: "Action", value: "number" },
];

// Reactive search and table items
const productSearchValue = ref('');
const productSearchField = ref(['name', 'price']);
const products = props.products || [];

const productItems = computed(() => {
  return products
    .slice()
    .sort((a, b) => {

      const aStock = a.stock ?? 0;
      const bStock = b.stock ?? 0;

      if (aStock === 0 && bStock !== 0) return  1; // a আগে
      if (aStock !== 0 && bStock === 0) return -1;  // b আগে
      return 0; // সমান হলে অবস্থান ঠিক রাখো
    })
    .map(p => ({
      image: p.image,
      name: p.name,
      qty: p.stock,
      id: p.id
    }));
});

// Modal logic
const selectedProduct = ref({ name: '', price: 0, qty: 1,   ManufectureDate: '',
  ValidateTime: '',
  ExpaireDate: '' });

// Component লোড হলে আজকের তারিখ বসানো
onMounted(() => {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  const todayStr = `${year}-${month}-${day}`

  selectedProduct.value.ManufectureDate = todayStr
  selectedProduct.value.ExpaireDate = todayStr
})

// ValidateTime এর সাথে Manufacture Date যোগ করে ExpaireDate auto-calculate
watch(
  () => [selectedProduct.value.ManufectureDate, selectedProduct.value.ValidateTime],
  ([manuDate, validateMonth]) => {
    if (manuDate && validateMonth) {
      const d = new Date(manuDate)
      d.setMonth(d.getMonth() + parseInt(validateMonth))
      const year = d.getFullYear()
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      selectedProduct.value.ExpaireDate = `${year}-${month}-${day}`
    }
    // ValidateTime না দিলে, ExpaireDate user manually দিতে পারবে
  }
)




const showModal = ref(false); // Control modal visibility

function openCustomizationModal(productId) {
    const product = props.products.find(p => p.id === productId);
    if (product) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;

        selectedProduct.value = { ...product,
            qty: 1,
            ManufectureDate: todayStr,
            ExpaireDate: todayStr,
            ValidateTime: ''

        };
        showModal.value = true;
    }



}

function closeModal() {
  showModal.value = false;
}

function addProductToInvoice() {
  console.log("✅ addProductToInvoice called", selectedProduct.value);
  const p = selectedProduct.value;
  emit("add-to-invoice", {
    name: p.name,
    qty: p.qty,
    purchase_price: p.price,
    total: p.qty * p.price,
    product_id: p.id,
    ManufectureDate: p.ManufectureDate,
    ExpaireDate: p.ExpaireDate
  });
  closeModal();
}


</script>

<style scoped>

</style>
