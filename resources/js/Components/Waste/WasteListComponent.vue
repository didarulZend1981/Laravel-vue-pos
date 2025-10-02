<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { usePage,router } from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";
import ProductFormModal from "../Product/ProductFormModal.vue";




const page = usePage();
const loading = ref(false);
const product=page.props.wasteProduct


// Table headers
const Header = [
    { text: "No", value: "no" },
    { text: "Image", value: "image" },
    { text: "Product Name", value: "product_name" },
    { text: "waste qty", value: "waste_qty" },
    { text: "purchase price", value: "purchase_price" },
    { text: "invoice name", value: "invoice_name" },
    { text: "case", value: "case" },
    { text: "refound", value: "action" },
];



const Item = computed(() => {
    return product?.map((item, index) => ({
        no: index + 1,
        image: item.image,
        product_name: item.product_name,
        waste_qty: item.waste_qty,
        purchase_price: item.purchase_price,
        invoice_name: item.invoice_name,
        case: item.case,
        refound: item.refound,
        id: item.id,

    })) || [];
});

const searchValue = ref("");
const searchField = ref(["product_name"]);

const handleWasteProductClick = () => {
    loading.value = true;

    router.get(route('west'), {}, {
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        },
        onSuccess: (page) => {
            // If message is returned (no products), show it
            if (page?.props?.message) {
               successToast(page.props.message);
            }
        },
        onError: (errors) => {
            alert("Something went wrong!");
        }
    });
};

//remove product from invoice table
const showModal = ref(false)


// Modal logic
const selectedProduct = ref({ ManufectureDate: '',
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



function refound(id) {
  const selected = product.find(p => p.id === id);
  if (selected) {
    const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;


    selectedProduct.value = {
      name: selected.product_name,
      price: selected.purchase_price,
      qty: selected.waste_qty,
      case: selected.case,
      invoice_name: selected.invoice_name,
      invoice_Refound:"",
      product_id: selected.product_id,
      invoice_supplier_id: selected.invoice_supplier_id,
      RefundDate:todayStr,
      ManufectureDate: todayStr, // default blank
      ValidateTime: "",
      ExpaireDate: todayStr,
      waste_products_id: selected.id
    };
    showModal.value = true;
  }
}



function closeModal() {
  showModal.value = false
  selectedProduct.value = {
    name: '',
    price: '',
    qty: '',
    case: '',
    invoice_name: '',
    invoice_Refound:"",
    RefundDate:"",
    ManufectureDate: '',
    ValidateTime: '',
    ExpaireDate: '',
    waste_products_id:""
  }
}




function addProductToInvoice() {
      loading.value = true;

  const p = selectedProduct.value;

  const form = {
    name: p.name,
    qty: p.qty,
    purchase_price: p.price,
    invoice_name:p.invoice_name,
    invoice_Refound:p.invoice_Refound,
    product_id: p.product_id,
    invoice_supplier_id: p.invoice_supplier_id,
    RefundDate: p.RefundDate,
    ManufectureDate: p.ManufectureDate,
    ExpaireDate: p.ExpaireDate,
    waste_products_id: p.waste_products_id,
    case: p.case
  };










    router.post(route('waste.refund'), form, {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                    setTimeout(() => {
                    router.push('/waste');
                    }, 200);

                } else {
                    errorToast(page.props.flash.message || 'Failed to delete waste product!');
                }
                 closeModal();
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete waste product!');
            }
        });



}




</script>

<template>

   <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Waste product</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                 <button class="btn btn-sm btn-info" @click="handleWasteProductClick">
                    Waste product found
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable :headers="Header" :items="Item" border-cell theme-color="#36b9cc" :rows-per-page="15"
                        :search-field="searchField" :search-value="searchValue">
                         <template #item-image="{ image }">
                            <img :src="image ? `/storage/${image}` : '/asset/img/placeholder.jpg'"
                                alt="Category Image" style="width: 50px; height: 50px; object-fit: cover;" class="p-1">
                        </template>

                        <template #item-action="{ id }">
                            <button class="btn btn-sm btn-outline-danger" @click="refound(id)">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>





        </div>

         <ProductFormModal
            :show="showModal"
            :product="selectedProduct"
            :onClose="closeModal"
            :onSubmit="addProductToInvoice"
            title="Product refound"invoice_name
            :fields="[
                { label: 'Product Name', model: 'name', readonly: true },
                { label: 'Price', model: 'price', type: 'number' },
                { label: 'Qty', model: 'qty', type: 'number' },
                { label: 'case', model: 'case', type: 'text' },
                { label: 'invoice name', model: 'invoice_name', readonly: true },
                { label: 'invoice Refound', model: 'invoice_Refound', type: 'text' },
                { label: 'RefundDate', model: 'RefundDate',type: 'date' },
                { label: 'Manufacture Date', model: 'ManufectureDate', type: 'date' },
                { label: 'Validate Time', model: 'ValidateTime', type: 'number' },
                { label: 'Expiry Date', model: 'ExpaireDate', type: 'date' }
            ]"
        />


<


    </div>

    <Loader v-if="loading" />
</template>

<style scoped>
table td {
    padding-left: 0.5rem;
}



</style>
