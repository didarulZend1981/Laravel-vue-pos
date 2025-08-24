<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import axios from "axios";
import Loader from "@/Components/Loader/Loader.vue";


const currentDate = new Date().toISOString().split("T")[0];
const loading = ref(false);
const page = usePage();
const Header = [
    { text: "No", value: "no" },
    { text: "Invoice Name", value: "inv_name" },
    { text: "Customer", value: "name" },
    { text: "Address", value: "address" },
    { text: "Mobile", value: "mobile" },
    { text: "Total", value: "total" },
    { text: "VAT", value: "vat" },
    { text: "Discount", value: "discount" },
    { text: "Subtotal", value: "subtotal" },
    { text: "Paid", value: "paid" },
    { text: "Rest", value: "rest" },
    { text: "A/R", value: "customer_payable" },
    { text: "Action", value: "number" },
];

const Item = computed(() => {
    return page.props.sale_invoices?.map((sale_invoices, index) => ({
        no: index + 1,
        inv_name: sale_invoices.invoice_name || "N/A",
        name: sale_invoices.customer.name,
        address: sale_invoices.customer.address || "N/A",
        mobile: sale_invoices.customer.phone,
        total: sale_invoices.total,
        vat: sale_invoices.vat,
        discount: sale_invoices.discount,
        subtotal: sale_invoices.subtotal,
        paid: sale_invoices.paid,
        rest: sale_invoices.rest,
        customer_payable: sale_invoices.customer_payable,
        id: sale_invoices.id,
        cus_id: sale_invoices.customer.id
    })) || [];
});

//search functionalify
const searchValue = ref("");
const searchField = ref(["inv_name", "name", "address", "mobile"]);

//===================invoice details=====================//
const invoiceDetails = ref(null);

async function detailsModalShow(id, cus_id) {
    try {
        loading.value = true;
        const response = await axios.post(route('sale.invoice.details', {id:id}), {
            customer_id: cus_id
        });
        loading.value = false;
        invoiceDetails.value = response.data;
        $('#invoiceDetails').modal('show'); // Show the modal
    } catch (error) {
        errorToast("Error fetching invoice details");
        loading.value = false;
    }
}


//==================print invoice==========================//
function printInvoice() {
    let printInv = document.getElementById('printInv').innerHTML;
    let originalContents = document.body.innerHTML;
    document.body.innerHTML = printInv;
    window.print();
    document.body.innerHTML = originalContents;
    setTimeout(function () {
        location.reload();
    }, 100);
}


//delete invoice
const deleteInvoice = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this invoice?')) {
        router.delete(route('sale.invoice.delete', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                } else {
                    errorToast(page.props.flash.message || 'Failed to delete!');
                }
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete!');
            }
        });
    }
}
</script>

<template>
    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Sale Invoices List</h1>

        <!-- invoice list data table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">

                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                <Link class="btn btn-sm btn-info" :href="route('show.sale')">
                Create Sale </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" border-cell
                        theme-color="#36b9cc" :rows-per-page="10" :search-field="searchField"
                        :search-value="searchValue">
                        <!-- Template for Action Buttons -->
                        <template #item-number="{ id, cus_id }">
                            <button type="button" class="btn btn-sm btn-outline-success"
                                @click="detailsModalShow(id, cus_id)">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger ml-2" @click="deleteInvoice(id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================invoice details modal=================== -->
    <div class="modal fade" id="invoiceDetails" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 10px; border: none;">
                <div class="modal-body" id="printInv" style="background-color: #f8f9fa;">

                    <!-- shop details and QR code -->
                    <div class="d-flex justify-content-between my-3 p-3"
                        style="background: linear-gradient(to right, #28a745, #5cb85c); color: white; border-radius: 5px;">
                        <div class="text-left">
                            <h3 class="modal-title mb-1" style="color: white; font-weight: 700;">ABC Shop</h3>
                            <p class="mb-0" style="color: #e8f5e9;">123, XYX Street, Mars Planet</p>
                            <p class="mb-0" style="color: #e8f5e9;">Phone: +1-234-567-890 | Email: info@abc.com</p>
                            <p class="mb-0" style="color: #e8f5e9;">Website: <a href="https://codearif.com"
                                    target="_blank" style="color: #e8f5e9; text-decoration: underline;">codearif.com</a>
                            </p>
                        </div>

                        <div class="text-right">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvnnF6LOIc-g4nnFr4sTXZRvKjUVnK0CNhOA&s"
                                alt="" style="max-width: 50%; background: white; padding: 5px; border-radius: 5px;">
                        </div>
                    </div>

                    <!-- builed to ship to -->
                    <div class="d-flex justify-content-between align-items-center mb-1 mt-4">
                        <div>
                            <h4 class=""
                                style="color: #2e7d32; border-bottom: 2px solid #2e7d32; display: inline-block;">
                                BILLED
                                TO</h4>
                        </div>
                        <div>
                            <h4 class="text-white"
                                style="background-color: #388e3c; padding: 5px 15px; border-radius: 20px; display: inline-block;">
                                POS</h4>
                        </div>
                    </div>

                    <hr style="border-top: 2px dashed #81c784;">

                    <div v-if="invoiceDetails">
                        <!-- Billing Section -->
                        <div class="p-3"
                            style="background-color: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-4">
                                <div class="flex-grow-1 text-break">
                                    <p style="line-height: 1.5;"><strong style="color: #2e7d32;">Name:</strong> <span
                                            class="text-success" style="font-weight: 500;">{{
                                                invoiceDetails?.customer_details?.name
                                            }}</span> </p>
                                    <p style="line-height: 1.5;"><strong style="color: #2e7d32;">Mobile:</strong> <span
                                            class="text-success" style="font-weight: 500;"> {{
                                                invoiceDetails?.customer_details?.phone
                                            }} </span> </p>
                                    <p style="line-height: 1.5;"><strong style="color: #2e7d32;">Email:</strong> <span
                                            class="text-success" style="font-weight: 500;"> {{
                                                invoiceDetails?.customer_details?.email || 'N/A'
                                            }} </span> </p>
                                    <p style="line-height: 1.5;"><strong style="color: #2e7d32;">Address:</strong> <span
                                            class="text-success" style="font-weight: 500;">{{
                                                invoiceDetails?.customer_details?.address }}</span> </p>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <p class="mb-2" style="line-height: 1.5;"><strong
                                            style="color: #2e7d32;">Date:</strong>
                                    </p>
                                    <p class="mb-0 text-success" style="font-weight: 500; line-height: 1.5;">{{
                                        currentDate
                                    }}</p>
                                </div>
                            </div>
                        </div>


                        <!-- Product Summary -->
                        <div class="mt-4 p-3"
                            style="background-color: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <h4 class="text-center mt-3"
                                style="color: #2e7d32; border-bottom: 2px solid #81c784; padding-bottom: 8px; display: inline-block;">
                                Product Summary</h4>
                            <table class="table mt-3">
                                <thead>
                                    <tr style="background-color: #e8f5e9;">
                                        <th style="color: #1b5e20;">NO.</th>
                                        <th style="color: #1b5e20;">Name</th>
                                        <th style="color: #1b5e20;">Qty</th>
                                        <th style="color: #1b5e20;">Rate</th>
                                        <th style="color: #1b5e20;">Sale Price</th>
                                        <th style="color: #1b5e20;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in invoiceDetails.invoice_products" :key="product.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ product?.products?.name ?? product?.product_name }}</td>
                                        <td>{{ product.qty }}</td>
                                        <td>{{ product.rate }}</td>
                                        <td>{{ product.sale_price }}</td>
                                        <td class="text-success" style="font-weight: 500;">{{ product.amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Amount Summary -->
                        <div class="mt-4 p-3"
                            style="background-color: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <h4 class="text-center mt-3"
                                style="color: #2e7d32; border-bottom: 2px solid #81c784; padding-bottom: 8px; display: inline-block;">
                                Invoice Breakdown</h4>
                            <hr style="border-top: 1px dashed #81c784;">

                            <p class="summary-item" style="line-height: 2;"><strong style="color: #2e7d32;">Total:
                                    $</strong> <span class="text-success" style="font-weight: 500;" id="getTotal"> {{
                                        invoiceDetails.invoice_details.total }} </span></p>
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #2e7d32;">VAT:</strong>
                                <span class="text-success" style="font-weight: 500;"> {{
                                    invoiceDetails.invoice_details.vat }} </span>
                            </p>
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #2e7d32;">Discount:</strong> <span class="text-success"
                                    style="font-weight: 500;"> {{
                                        invoiceDetails.invoice_details.discount }}</span> </p>
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #2e7d32;">Subtotal:</strong> <span class="text-success"
                                    style="font-weight: 500;"> {{
                                        invoiceDetails.invoice_details.subtotal }}</span> </p>
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #2e7d32;">Paid:</strong>
                                <span class="text-success" style="font-weight: 500;"> {{
                                    invoiceDetails.invoice_details.paid }}</span>
                            </p>
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #2e7d32;">Rest:</strong>
                                <span class="text-success" style="font-weight: 500;"> {{
                                    invoiceDetails.invoice_details.rest }}</span>
                            </p>
                            <hr style="border-top: 1px dashed #81c784;">
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #2e7d32; font-size: 1.1rem;">Payable:</strong> <span
                                    class="text-danger" style="font-weight: 700; font-size: 1.1rem;">{{
                                        invoiceDetails.invoice_details.customer_payable }}</span> </p>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 p-2 text-center" style="background-color: #e8f5e9; border-radius: 5px;">
                            <p class="mb-0" style="color: #2e7d32; font-size: 0.9rem;">Thank you for your business!</p>
                            <p class="mb-0" style="color: #2e7d32; font-size: 0.8rem;">Terms & Conditions Apply</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" @click="printInvoice()">Print <i class="fa fa-print"></i>
                    </button>
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

@media print {
    body * {
        visibility: hidden;
    }

    #printInv,
    #printInv * {
        visibility: visible;
    }

    #printInv {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background-color: white !important;
        color: black !important;
        padding: 20px;
    }

    .modal-footer {
        display: none !important;
    }
}
</style>
