<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { ref, computed ,onMounted } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import axios from "axios";
import Loader from "@/Components/Loader/Loader.vue";
import SupplierTableComponent from "@/Components/Supplier/SupplierTableComponent.vue";
import ProductTableComponent from "@/Components/Product/ProductTableComponent.vue";

// Access props passed from the backend
const loading = ref(false);
const { props } = usePage();
const suppliers = props.suppliers || []; // Ensure it's an array
const products = props.products || [];   // Ensure it's an array
// const currentDate = new Date().toISOString().split("T")[0];





// তারিখ সংরক্ষণ
const selectedDate = ref('')

// ✅ ফরম্যাট করা তারিখ (DD-MM-YYYY)
const formattedDate = computed(() => {
  if (!selectedDate.value) return ''
  const d = new Date(selectedDate.value)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}`
})

// ✅ Component লোড হলে current date বসাও
onMounted(() => {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  selectedDate.value = `${year}-${month}-${day}` // 📌 YYYY-MM-DD format
})



//=======================product pick and assing to billign section=================//
// const selectedProduct = ref({ name: '', price: 0, qty: 1 });
const invoiceData = ref([]);
const totalAmount = ref(0);
const paidAmount = ref(0);




//remove product from invoice table
function removeProduct(index) {
    invoiceData.value.splice(index, 1);
    updateSummary();
}

// paid amount change
function paidChange(event) {
    paidAmount.value = parseFloat(event.target.value) || 0;
    updateSummary();
}

// Update summary dynamically
function updateSummary() {
    let total = 0;

    // Calculate total for all products
    invoiceData.value.forEach(item => {
        total += item.total;
    });

    totalAmount.value = total;

    // Calculate payable amount
    const payableAmount = totalAmount.value - paidAmount.value;
    const restAmount = payableAmount;


    // Update DOM bindings (if needed)
    document.getElementById('subTotal').textContent = total.toFixed(2);
    document.getElementById('paid').textContent = paidAmount.value.toFixed(2);
    document.getElementById('account_payable').textContent = payableAmount.toFixed(2);
    document.getElementById('rest').textContent = restAmount.toFixed(2);
}

//==============================finally generate invoic==============================//
async function generateInvoice() {

    try {
        loading.value = true;

        // Retrieve values from the DOM and Vue state
        const total = parseFloat(document.getElementById('subTotal').innerText);
        const paid = parseFloat(document.getElementById('paid').innerText);
        const account_payable = parseFloat(document.getElementById('account_payable').innerText);
        const supplier_id = parseInt(document.getElementById('getSupId').innerText);
        const invoice_name = document.getElementById('invoice_name').value;
        const rest = parseFloat(document.getElementById('rest').innerText);

        if (!supplier_id) {
            errorToast('Please select a supplier first');
        } else if (invoiceData.value.length === 0) {
            errorToast('Please add products')
        } else if (paid > total) {
            errorToast('Paid amount cannot be greater than total amount')
        } else {
            // Prepare the data payload
            const data = {
                    total,
                    paid,
                    rest,
                    account_payable,
                    supplier_id,
                    products: invoiceData.value,
                    invoice_name,
                    purchase_date: selectedDate.value
            };

            console.log("Invoice Data Value: ", invoiceData.value);

            // Send the invoice data to the server
            const response = await axios.post(route('create.purchase.invoice'), data);

            if (response.data.status === true) {
                successToast(response.data.message);
                setTimeout(() => {
                    window.location.href = route('show.purchase.invoice.page');
                }, 500);
            } else {
                throw new Error(response.data.message);
            }
        }

    } catch (error) {
        errorToast(error.message || 'An error occurred while creating the invoice.');
    } finally {
        loading.value = false;
    }
}




//=====================customer pick and assing to billing section==================//
const supplierPick = ref({
    name: "",
    address: "",
    phone: "",
    id: "",
});

const pickSupplier = (id) => {
    const selectedSupplier = suppliers.find(supplier => supplier.id === id);
    if (selectedSupplier) {
        supplierPick.value = {
            name: selectedSupplier.name,
            address: selectedSupplier.address,
            phone: selectedSupplier.phone,
            id: selectedSupplier.id
        };
    }
};
</script>

<template>

    <Head>
        <title> POS || Purchase </title>
    </Head>

    <DashboardLayout>
        <div class="container-fluid">
            <div class="row mb-3">

                <div class="col-12">
                    <div class="shadow my-3 p-3 d-flex justify-content-between align-item-center">
                        <div>
                            <h5 class="text-info font-weight-bold">Create purchase from previous supplier or company
                            </h5>
                        </div>
                        <div>
                            <Link href="/purchase-invoice/custom" class="btn btn-sm btn-info">Create custom purchase
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- bulling section -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <h4 class="m-0 font-weight-bold text-info">Purchase From</h4>
                            <h4 class="m-0 font-weight-bold text-info">#POS</h4>
                        </div>

                        <div class="card-body">
                            <!-- Customer Info -->
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-4">
                                <div>
                                    <p><strong>Name:</strong> <span class="text-info">{{ supplierPick.name
                                    }}</span></p>
                                    <p><strong>Phone:</strong> <span class="text-info">{{ supplierPick.phone
                                    }}</span></p>
                                    <p><strong>Address:</strong> <span class="text-info">{{
                                        supplierPick.address
                                            }}</span></p>
                                    <p class="d-none"><strong>ID:</strong> <span class="text-info" id="getSupId">{{
                                        supplierPick.id
                                            }}</span></p>
                                </div>
                                <div class="text-end" style="max-width: 200px;">
                                    <!-- <p><strong>Date:</strong></p> -->
                                    <input type="date" v-model="selectedDate" class="form-control" />

                                </div>
                            </div>

                            <!-- Product Table -->
                            <table class="table" id="invoiceTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Qty</th>
                                        <th>Sale Price</th>
                                        <th>TK</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in invoiceData" :key="index">
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.qty }}</td>
                                        <td>{{ item.total.toFixed(2) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger" @click="removeProduct(index)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Summary -->
                            <h4 class="text-center mt-3 text-info">Summary</h4>
                            <div class="mb-2" style="background: #636363; height: 2px;"></div>
                            <p class="summary-item"><strong>Subtotal: (৳)</strong> <span class="text-info"
                                    id="subTotal">0.00</span></p>
                            <p class="summary-item"><strong>Paid: (৳)</strong> <span class="text-info"
                                    id="paid">0.00</span></p>
                            <p class="summary-item"><strong>Rest: (৳)</strong> <span class="text-info"
                                    id="rest">0.00</span></p>
                            <div class="mb-2" style="background: #636363; height: 2px;"></div>
                            <p class="summary-item"><strong>Account Payable: (৳)</strong> <span class="text-danger"
                                    id="account_payable">0.00</span>
                            </p>

                            <div class="mb-2" style="background: #636363; height: 2px;"></div>
                            <!-- PAID -->
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label>Paid (৳)</label>
                                    <input type="number" class="form-control" id="paidChange" value="0" min="0" step="1"
                                        @input="paidChange" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="mb-3">
                                    <label>Invoice Name</label>
                                    <input type="text" class="form-control" id="invoice_name"/>
                                </div>
                            </div>

                            <button class="btn btn-info w-100 mt-4" @click.prevent="generateInvoice">
                                Generate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- pick product -->
                <div class="col-lg-4">



                    <ProductTableComponent
                            :products="products"
                            @add-to-invoice="invoiceData.push($event); updateSummary()"
                            />
                </div>

                <!-- pick customer -->
                <div class="col-lg-4">

                    <SupplierTableComponent :suppliers="suppliers" @pick="pickSupplier" />
                </div>
            </div>
        </div>
    </DashboardLayout>




    <Loader v-if="loading" />
</template>

<style scoped>
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 5px 0;
    font-size: 16px;
}

.summary-item strong {
    margin-right: 10px;
}
</style>
