<script setup>
import { reactive, ref, watch } from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import axios from "axios";
import Loader from "@/Components/Loader/Loader.vue";


// Date initialization
const currentDate = new Date().toISOString().split("T")[0];
const loading = ref(false);
const page = usePage();
const categories = page.props.categories || [];
const brands = page.props.brands || [];

// Form data
const data = reactive({
    name: '',
    email: '',
    phone: '',
    address: '',
    company_name: '',

    invoice_name: '',
    total: '',
    paid: 0,
    rest: 0,
    account_payable: '',

    products: [
        {
            name: '',
            qty: 1,
            rate: 0,
            price: 0,
            amount: 0,
            category_id: '',
            brand_id: '',
            description: '',
            purchase_price: ''
        },
    ],
});


// Totals
const total = ref(0);
const restAmount = ref(0);
const accountPayable = ref(0);

// Functions
const calculateAmounts = () => {
    total.value = 0;

    data.products.forEach((product) => {
        product.total = product.qty * product.price;
        total.value += product.total;
    });

    restAmount.value = total.value - data.paid;
    accountPayable.value = restAmount.value;
};


// Watch and auto-calculate
watch(
    () => [data.products, data.paid],
    calculateAmounts,
    { deep: true, immediate: true }
);


// Add/Remove product rows
const addMoreItems = () => {
    data.products.push({
        category_id: '',
        brand_id: '',
        name: '',
        qty: 1,
        price: 0,
        unit: 0,
        stock: 0,
        description: '',
        purchase_price: data.price
    });
};

const removeItem = (index) => {
    data.products.splice(index, 1);
};


//=======================finally generate custom invoic======================//
async function generateInvoice() {
    loading.value = true;

    try {
        // Validate customer info
        if (!data.name || !data.address) {
            errorToast("Please fill in required supplier information.");
            return;
        }

        // Validate product list
        if (!data.products || data.products.length === 0) {
            errorToast("Please add at least one product.");
            return;
        }

        // Prepare invoice payload
        const invoicePayload = {
            name: data.name,
            email: data.email,
            phone: data.phone,
            company_name: data.company_name,
            address: data.address,

            invoice_name: data.invoice_name,
            total: total.value,
            paid: data.paid,
            rest: restAmount.value,
            account_payable: accountPayable.value,

            products: data.products.map(p => ({
                category_id: p.category_id,
                brand_id: p.brand_id,
                name: p.name,
                unit: p.unit,
                qty: p.qty,
                price: p.price,
                description: p.description,
                purchase_price: p.price,
            }))
        };

        const response = await axios.post('/purchase-invoice/custom-create', invoicePayload);

        if (response.data.status === true) {
            successToast(response.data.message);
            setTimeout(() => {
                window.location.href = "/purchase-invoice/list";
            }, 1000);
        } else {
            errorToast(response.data.message || "Failed to create invoice.");
        }

    } catch (error) {
        console.error(error);

        if (error.response?.status === 403) {
            errorToast("You don't have permission to perform this action.");
        } else {
            errorToast("An unexpected error occurred while generating the invoice.");
        }

    } finally {
        loading.value = false;
    }
}

</script>


<template>

    <Head>
        <title> POS || Custom Sale </title>
    </Head>

    <DashboardLayout>
        <div id="content-wrapper" class="d-flex flex-column">
            <div class="container-fluid">
                <div class="row mb-3">

                    <!-- bradecrumb -->
                    <div class="col-12">
                        <div class="shadow my-3 p-3 d-flex justify-content-between align-item-center">
                            <div>
                                <h5 class="text-info font-weight-bold">Create custom form random supplier/company</h5>
                            </div>
                            <div>
                                <Link :href="route('show.purchase.invoice')" class="btn btn-sm btn-info">Create purchase
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- billing section -->
                    <div class="col-12">
                        <div class="shadow p-3">
                            <div class="d-flex justify-content-between">
                                <!-- supplier or company section -->
                                <div class="col-md-8">
                                    <h4 class="text-info">Purchase From</h4>
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Supplier Name *</label>
                                            <input v-model="data.name" type="text" class="form-control"
                                                placeholder="Name" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Email</label>
                                            <input v-model="data.email" type="email" class="form-control"
                                                placeholder="Email" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Phone</label>
                                            <input v-model="data.phone" type="tel" class="form-control"
                                                placeholder="Phone" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Company name</label>
                                            <input v-model="data.company_name" type="text" class="form-control"
                                                placeholder="Company name" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Address *</label>
                                            <textarea v-model="data.address" class="form-control"
                                                placeholder="Address"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- date and invoice name -->
                                <div class="col-md-4">
                                    <h4 class="text-info">POS</h4>
                                    <hr />
                                    <label>Invoice Name</label>
                                    <input v-model="data.invoice_name" type="text" class="form-control mb-3"
                                        placeholder="Invoice Name" />
                                    <p><strong>Date:</strong> {{ currentDate }}</p>
                                </div>
                            </div>

                            <!-- Product Table -->
                            <div class="table-responsive my-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Brand/Company</th>
                                            <th>Product Name</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Description</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in data.products" :key="index">
                                            <!-- category_id -->
                                            <td>
                                                <select class="form-select form-control" v-model="product.category_id">
                                                    <option disabled value="">Select Category</option>
                                                    <option v-for="category in categories" :key="category.id"
                                                        :value="category.id">
                                                        {{ category.name }}
                                                    </option>
                                                </select>
                                            </td>

                                            <!-- brand_id -->
                                            <td>
                                                <select class="form-select form-control" v-model="product.brand_id">
                                                    <option disabled value="">Select Brand</option>
                                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                                        {{ brand.name }}
                                                    </option>
                                                </select>
                                            </td>

                                            <!-- product name -->
                                            <td><input v-model="product.name" class="form-control"
                                                    placeholder="Product Name" /></td>
                                            <!-- product unit -->
                                            <td><input v-model="product.unit" class="form-control"
                                                    placeholder="Unit (e.g., KG, Pcs)" /></td>
                                            <!-- qty -->
                                            <td><input v-model.number="product.qty" class="form-control" type="number"
                                                    min="1" /></td>
                                            <!-- price -->
                                            <td><input v-model.number="product.price" class="form-control" type="number"
                                                    placeholder="Price" /></td>
                                            <!-- description -->
                                            <td><textarea v-model="product.description" placeholder="Description"
                                                    class="form-control" style="height: 50px;"></textarea></td>
                                            <!-- total -->
                                            <td>{{ product.total.toFixed(2) }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm"
                                                    @click="removeItem(index)">Remove</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-success btn-sm" @click="addMoreItems">Add Product</button>
                            </div>

                            <hr>

                            <!-- Summary Section -->
                            <div class="d-flex justify-content-between">
                                <div class="col-md-5">
                                    <h4 class="text-center mt-3 text-info">Invoice Summary</h4>
                                    <div class="mb-2" style="background: #636363; height: 2px;"></div>

                                    <div class="summary-item"><strong>Total (৳):</strong> <span class="text-info"
                                            id="subtotal">{{ total.toFixed(2) }}</span>
                                    </div>

                                    <div class="summary-item"><strong>Paid (৳):</strong> <span class="text-info"
                                            id="rest">{{ restAmount.toFixed(2) }}</span>
                                    </div>

                                    <div class="summary-item mt-1"><strong>Pay (৳):</strong>
                                        <input v-model.number="data.paid" type="number" class="form-control"
                                            step="0.01" />
                                    </div>

                                    <hr style="border-top: 2px dashed #000;">
                                    <div class="summary-item"><strong>Acc Payable (৳):</strong> <span
                                            class="text-danger" id="account_payable">{{ accountPayable.toFixed(2)
                                            }}</span>
                                    </div>

                                    <!-- Generate Invoice -->
                                    <button class="btn btn-info w-100 mt-4" @click.prevent="generateInvoice">
                                        Generate Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Loader v-if="loading" />
    </DashboardLayout>

</template>

<style scoped>
.table {
    border-collapse: collapse;
    width: 100%;
}

.table th {
    background-color: #f8f8f8;
    font-weight: bold;
}

.table th,
.table td {
    text-align: left;
    padding: 8px;
    border: 1px solid #ddd;
}

.notes label {
    font-weight: bold;
    color: #555;
}

.notes textarea {
    margin-top: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100%;
    padding: 10px;
    background-color: #f9f9f9;
}

.totals {
    text-align: right;
}

.totals input {
    width: 180px;
    border-radius: 4px;
    padding: 5px;
    border: 1px solid #ccc;
    background-color: #f9f9f9;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 5px 0;
    font-size: 16px;
}

.summary-item strong {
    padding-right: 20px;
}
</style>