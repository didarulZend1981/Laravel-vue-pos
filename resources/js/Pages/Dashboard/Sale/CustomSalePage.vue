<script setup>
import { reactive, ref, watch } from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import axios from "axios";
import Loader from "@/Components/Loader/Loader.vue";


// Date initialization
const currentDate = new Date().toISOString().split("T")[0];
const loading = ref(false);

// Form data
const data = reactive({
    name: '',
    email: '',
    phone: '',
    zip: '',
    address: '',
    comment: '',
    invoice_name: '',
    paid: 0,

    products: [
        {
            product_name: '',
            qty: 1,
            rate: 0,
            sale_price: 0,
            amount: 0,
        },
    ],

    vat: 0,
    discount: 0,
});

// Totals
const total = ref(0);
const vatAmount = ref(0);
const discountAmount = ref(0);
const subtotal = ref(0);
const restAmount = ref(0);
const customerPayable = ref(0);

// Functions
const calculateAmounts = () => {
    total.value = 0;

    data.products.forEach(p => {
        p.amount = p.qty * p.sale_price;
        total.value += p.amount;
    });

    vatAmount.value = (total.value * data.vat) / 100;
    discountAmount.value = (total.value * data.discount) / 100;
    subtotal.value = total.value + vatAmount.value - discountAmount.value;
    restAmount.value = subtotal.value - data.paid;
    customerPayable.value = restAmount.value;
};

// Watch and auto-calculate
watch(
    () => [data.products, data.vat, data.discount, data.paid],
    calculateAmounts,
    { deep: true, immediate: true }
);

// Add/Remove product rows
const addMoreItems = () => {
    data.products.push({ product_name: '', qty: 1, rate: 0, sale_price: 0, amount: 0 });
};

const removeItem = (index) => {
    data.products.splice(index, 1);
};


//==============================finally generate custom invoic==============================//
async function generateInvoice() {
    try {
        loading.value = true;

        // Validate customer
        if (!data.name || !data.address) {
            errorToast("Please fill in required customer information.");
            return;
        }

        // Validate products
        if (!data.products || data.products.length === 0) {
            errorToast("Please add at least one product.");
            return;
        }

        // Prepare data object
        const invoicePayload = {
            name: data.name,  // Changed from customer_name to match your controller
            email: data.email,
            phone: data.phone,
            zip: data.zip,
            address: data.address,
            comment: data.comment,
            invoice_name: data.invoice_name,
            total: total.value,
            vat: data.vat,  // Changed to send percentage instead of amount
            discount: data.discount,  // Changed to send percentage instead of amount
            subtotal: subtotal.value,
            paid: data.paid,
            rest: restAmount.value,
            customer_payable: customerPayable.value,
            products: data.products.map(p => ({
                product_name: p.product_name,
                qty: p.qty,
                rate: p.rate,
                sale_price: p.sale_price,
                amount: p.amount // Changed from amount to total to match your controller
            }))
        };

        const response = await axios.post('/sale-invoice/custom-create', invoicePayload);

        if (response.data.status === true) {
            successToast(response.data.message);
            setTimeout(() => {
                window.location.href = "/sale-invoice/list";
            }, 1000);
        } else {
            errorToast(response.data.message || "Failed to create invoice.");
        }
    } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 403) {
            errorToast('error');
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
                                <h5 class="text-info font-weight-bold">Create custom sale for random customer</h5>
                            </div>
                            <div>
                                <Link href="/sale-invoice" class="btn btn-sm btn-info">Create sale</Link>
                            </div>
                        </div>
                    </div>

                    <!-- billing section -->
                    <div class="col-12">
                        <div class="shadow p-3">
                            <div class="d-flex justify-content-between">
                                <!-- customer section -->
                                <div class="col-md-8">
                                    <h4 class="text-info">BUILD TO</h4>
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Customer Name *</label>
                                            <input v-model="data.name" type="text" class="form-control"
                                                placeholder="Name" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Email</label>
                                            <input v-model="data.email" type="email" class="form-control"
                                                placeholder="Email" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Phone</label>
                                            <input v-model="data.phone" type="tel" class="form-control"
                                                placeholder="Phone" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>ZIP</label>
                                            <input v-model="data.zip" type="text" class="form-control"
                                                placeholder="ZIP" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Address *</label>
                                            <textarea v-model="data.address" class="form-control"
                                                placeholder="Address"></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Comment</label>
                                            <textarea v-model="data.comment" class="form-control"
                                                placeholder="Comment"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- date and invoice name -->
                                <div class="col-md-4">
                                    <h4 class="text-info">#POS</h4>
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
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Sale Price</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in data.products" :key="index">
                                            <td><input v-model="product.product_name" class="form-control"
                                                    placeholder="Product" /></td>
                                            <td><input v-model.number="product.qty" class="form-control" type="number"
                                                    min="1" /></td>
                                            <td><input v-model.number="product.rate" class="form-control"
                                                    type="number" /></td>
                                            <td><input v-model.number="product.sale_price" class="form-control"
                                                    type="number" /></td>
                                            <td>{{ product.amount.toFixed(2) }}</td>
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
                                            id="total">{{ total.toFixed(2) }}</span></div>

                                    <div class="summary-item"><strong>Discount (৳):</strong> <span class="text-info"
                                            id="discount">{{ discountAmount.toFixed(2) }}</span></div>

                                    <div class="summary-item"><strong>VAT (৳):</strong> <span class="text-info"
                                            id="vat">{{ vatAmount.toFixed(2) }}</span></div>

                                    <div class="summary-item"><strong>Subtotal (৳):</strong> <span class="text-info"
                                            id="subtotal">{{ subtotal.toFixed(2) }}</span></div>

                                    <div class="summary-item mt-1"><strong>Paid (৳):</strong>
                                        <input v-model.number="data.paid" type="number" class="form-control"
                                            step="0.01" />
                                    </div>
                                    <div class="summary-item"><strong>Rest (৳):</strong> <span class="text-info"
                                            id="rest">{{ restAmount.toFixed(2) }}</span></div>

                                    <hr style="border-top: 2px dashed #000;">
                                    <div class="summary-item"><strong>Customer Payable (৳):</strong> <span
                                            class="text-danger" id="customer_payable">{{ customerPayable.toFixed(2)
                                            }}</span></div>

                                    <!-- Generate Invoice -->
                                    <button class="btn btn-info w-100 mt-4" @click.prevent="generateInvoice">
                                        Generate Invoice
                                    </button>
                                </div>

                                <div class="col-md-4">
                                    <h4 class="text-center mt-3 text-info">VAT & Discount Calculation</h4>
                                    <div class="mb-2" style="background: #636363; height: 2px;"></div>
                                    <div class="mt-2">
                                        <label class="form-label">VAT (%):</label>
                                        <input type="number" v-model.number="data.vat" class="form-control" step="5" />
                                    </div>
                                    <div class="mt-2">
                                        <label class="form-label">Discount (%):</label>
                                        <input type="number" v-model.number="data.discount" class="form-control"
                                            step="5" />
                                    </div>
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