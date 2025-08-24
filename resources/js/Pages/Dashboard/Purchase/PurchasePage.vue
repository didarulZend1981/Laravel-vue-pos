<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { ref, computed } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import axios from "axios";
import Loader from "@/Components/Loader/Loader.vue";

// Access props passed from the backend
const loading = ref(false);
const { props } = usePage();
const suppliers = props.suppliers || []; // Ensure it's an array
const products = props.products || [];   // Ensure it's an array
const currentDate = new Date().toISOString().split("T")[0];

// DataTable Headers
const supplierHeader = [
    { text: "Type", value: "type" },
    { text: "Name", value: "name" },
    { text: "Action", value: "number" },
];

// Map backend data to the format required by EasyDataTable
const supplierItems = computed(() => {
    return suppliers.map((supplier) => ({
        type: `<i class="fa fa-user"></i>`,
        name: supplier.name,
        id: supplier.id,
        address: supplier.address,
        phone: supplier.phone
    }));
});

const productHeader = [
    { text: "Image", value: "Image" },
    { text: "Name", value: "name" },
    { text: "Price", value: "price" },
    { text: "Qty", value: "qty" },
    { text: "Action", value: "number" },
];

const productItems = computed(() => {
    return products.map((product) => ({
        image: product.image,
        name: product.name,
        price: product.price,
        qty: product.stock,
        id: product.id,
    }));
});

// Search functionality
const supplierSearchValue = ref("");
const supplierSearchField = ref(["name", "address", "phone"]);
const productSearchValue = ref("");
const productSearchField = ref(["name", "price"]);

//=======================product pick and assing to billign section=================//
const selectedProduct = ref({ name: '', price: 0, qty: 1 });
const invoiceData = ref([]);
const totalAmount = ref(0);
const paidAmount = ref(0);

// Function to select a product
function pickProduct(productId) {
    const product = products.find(prod => prod.id === productId);
    if (product) {
        selectedProduct.value = { ...product, qty: 1 }; // Set product with default quantity
        $('#productCustomization').modal('show'); // Show modal
    }
}

// Add product to invoice list
function addProductToInvlist() {
    const newProduct = {
        name: selectedProduct.value.name,
        qty: selectedProduct.value.qty,
        purchase_price: selectedProduct.value.price,
        total: selectedProduct.value.qty * selectedProduct.value.price, // Calculate total
        product_id: selectedProduct.value.id,
    };

    invoiceData.value.push(newProduct); // Add to invoice list
    updateSummary(); // Recalculate summary
    $('#productCustomization').modal('hide'); // Hide modal
    $('#productCustomizationBtnCls').click();
}

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
                invoice_name
            };

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
                                <div class="text-end">
                                    <p><strong>Date:</strong></p>
                                    <p class="text-info">{{ currentDate }}</p>
                                </div>
                            </div>

                            <!-- Product Table -->
                            <table class="table" id="invoiceTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Qty</th>
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
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <div>
                                <h6 class="m-0 font-weight-bold text-info">Pick Product</h6>
                            </div>
                            <div>
                                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                                    v-model="productSearchValue">
                            </div>
                        </div>
                        <div class="card-body">
                            <EasyDataTable buttons-pagination alternating :headers="productHeader" :items="productItems"
                                border-cell theme-color="#36b9cc" :rows-per-page="10" :search-field="productSearchField"
                                :search-value="productSearchValue">
                                <!-- Template for Image -->
                                <template #item-image="{ image }">
                                    <img :src="image ? `/storage/${image}` : 'https://skala.or.id/wp-content/uploads/2024/01/dummy-post-square-1-1.jpg'"
                                        alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;"
                                        class="p-1">
                                </template>

                                <!-- Template for Action Buttons -->
                                <template #item-number="{ id, name }">
                                    <button class="btn btn-sm btn-outline-success" @click="pickProduct(id)">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>
                </div>

                <!-- pick customer -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <div>
                                <h6 class="mt-1 font-weight-bold text-info">Pick Supplier</h6>
                            </div>
                            <div>
                                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                                    v-model="supplierSearchValue">
                            </div>
                        </div>
                        <div class="card-body">
                            <EasyDataTable buttons-pagination alternating :headers="supplierHeader"
                                :items="supplierItems" border-cell theme-color="#36b9cc" :rows-per-page="15"
                                :search-field="supplierSearchField" :search-value="supplierSearchValue">
                                <template #item-type="{ type }">
                                    <span
                                        class="text-info rounded-circle border border-info d-inline-flex justify-content-center align-items-center"
                                        style="width: 30px; height: 30px; font-size: 1rem;" v-html="type">
                                    </span>
                                </template>

                                <template #item-number="{ id }">
                                    <button class="btn btn-sm btn-outline-success" @click="pickSupplier(id)">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>


    <!-- product customization modal -->
    <div class="modal fade" id="productCustomization" tabindex="-1" aria-labelledby="productCustomizationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Product for invoice</h5>
                    <button type="button" id="productCustomizationModalLabel" class="btn btn-outline-info"
                        data-dismiss="modal" aria-label="Close"> ×</button>
                </div>
                <div class="modal-body">
                    <form id="invoiceForm" @submit.prevent="addProductToInvlist()">
                        <div class=" mb-3">
                            <input type="text" class="form-control" v-model="selectedProduct.name"
                                placeholder="Product Name" readonly />
                        </div>

                        <div class=" mb-3">
                            <input type="number" class="form-control" v-model="selectedProduct.price"
                                placeholder="Product Price" />
                        </div>

                        <div class="mb-3">
                            <input type="number" class="form-control" v-model="selectedProduct.qty"
                                placeholder="Quantity" />
                        </div>

                        <div class="d-flex justify-content-end">
                            <div>
                                <button id="productCustomizationBtnCls" type="button" class="btn btn-outline-info mr-3"
                                    data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-info">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
