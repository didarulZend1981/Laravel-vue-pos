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
    { text: "Supplier", value: "name" },
    { text: "Company", value: "company" },
    { text: "Phone", value: "phone" },
    { text: "Total", value: "total" },
    { text: "Paid", value: "paid" },
    { text: "Rest", value: "rest" },
    { text: "Account Payable", value: "a_payable" },
    { text: "Action", value: "number" },
];

const Item = computed(() => {
    return page.props.purchase_invoices?.map((purchase_invoices, index) => ({
        no: index + 1,
        name: purchase_invoices?.supplier?.name,
        company: purchase_invoices?.supplier?.brand?.name ?? purchase_invoices?.supplier?.company_name,
        phone: purchase_invoices.supplier.phone,
        total: purchase_invoices.total,
        paid: purchase_invoices.paid,
        rest: purchase_invoices?.rest,
        a_payable: purchase_invoices.account_payable,
        id: purchase_invoices.id,
        sup_id: purchase_invoices.supplier.id
    })) || [];
});

//search functionalify
const searchValue = ref("");
const searchField = ref(["name", "company", "phone"]);

//===================invoice details=====================//
const invoiceDetails = ref(null);

async function detailsModalShow(id, sup_id) {
    try {
        const response = await axios.post(route('purchase.invoice.details', { id: id }), {
            supplier_id: sup_id
        });
        invoiceDetails.value = response.data;
        $('#purchaseInvDetails').modal('show');
    } catch (error) {
        errorToast("Error fetching invoice details");
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
        router.delete(route('purchase.invoice.delete', { id: id }), {
            preserveScroll: true,
            ondark: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    darkToast(page.props.flash.message);
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
        <h1 class="h3 mb-3 text-gray-800">Purchase Invoices List</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">

                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                <Link class="btn btn-sm btn-info" :href="route('show.purchase.invoice')">
                Create Parchase </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" border-cell
                        theme-color="#36b9cc" :rows-per-page="10" :search-field="searchField"
                        :search-value="searchValue">
                        <!-- Template for Action Buttons -->
                        <template #item-number="{ id, sup_id }">
                            <button type="button" class="btn btn-sm btn-outline-success"
                                @click="detailsModalShow(id, sup_id)">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger ml-2" @click.prevent="deleteInvoice(id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================invoice details modal=================== -->
    <div class="modal fade" id="purchaseInvDetails" tabindex="-1" role="dialog"
        aria-labelledby="purchaseInvDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 10px; border: none;">
                <div class="modal-body" id="printInv" style="background-color: #f8f9fa;">

                    <!-- shop details and QR code -->
                    <div class="d-flex justify-content-between my-3 p-3"
                        style="background: linear-gradient(to right, #5800cf, #8632f7); color: white; border-radius: 5px;">
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

                    <!-- builed from ship to -->
                    <div class="d-flex justify-content-between align-items-center mb-1 mt-4">
                        <div>
                            <h4
                                style="color: #5800cf; border-bottom: 2px solid #5800cf; display: inline-block;">
                                Purchase From</h4>
                        </div>
                        <div>
                            <h4 class="text-white"
                                style="background-color: #5800cf; padding: 5px 15px; border-radius: 20px; display: inline-block;">
                                POS</h4>
                        </div>
                    </div>

                    <hr style="border-top: 2px dashed #5800cf;">

                    <div v-if="invoiceDetails">
                        <!-- Billing Section -->
                        <div class="p-3"
                            style="background-color: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-4">
                                <div class="flex-grow-1 text-break">
                                    <p style="line-height: 1;"><strong style="color: #5800cf;">Name:</strong> <span
                                            class="text-dark" style="font-weight: 500;">{{
                                                invoiceDetails?.supplier_details?.name
                                            }}</span> </p>
                                    <p style="line-height: 1;"><strong style="color: #5800cf;">Mobile:</strong> <span
                                            class="text-dark" style="font-weight: 500;"> {{
                                                invoiceDetails?.supplier_details?.phone
                                            }} </span> </p>
                                    <p style="line-height: 1;"><strong style="color: #5800cf;">Email:</strong> <span
                                            class="text-dark" style="font-weight: 500;"> {{
                                                invoiceDetails?.supplier_details?.email || 'N/A'
                                            }} </span> </p>
                                    <p style="line-height: 1;"><strong style="color: #5800cf;">Address:</strong> <span
                                            class="text-dark" style="font-weight: 500;">{{
                                                invoiceDetails?.supplier_details?.address }}</span> </p>
                                    <p style="line-height: 1;"><strong style="color: #5800cf;">Company:</strong> <span
                                            class="text-dark" style="font-weight: 500;">{{
                                                invoiceDetails?.supplier_details?.brand.name ?? invoiceDetails?.supplier_details?.company_name }}</span> </p>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <p class="mb-2" style="line-height: 1.5;"><strong
                                            style="color: #5800cf;">Date:</strong>
                                    </p>
                                    <p class="mb-0 text-dark" style="font-weight: 500; line-height: 1.5;">{{ new Date(invoiceDetails?.invoice_details?.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Summary -->
                        <div class="mt-4 p-3"
                            style="background-color: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <h4 class="text-center mt-3"
                                style="color: #5800cf; border-bottom: 2px solid #5800cf; padding-bottom: 8px; display: inline-block;">
                                Product Summary</h4>
                            <table class="table mt-3">
                                <thead>
                                    <tr style="background-color: #e2cdff;">
                                        <th style="color: #5800cf;">Name</th>
                                        <th style="color: #5800cf;">NO.</th>
                                        <th style="color: #5800cf;">Qty</th>
                                        <th style="color: #5800cf;">Price</th>
                                        <th style="color: #5800cf;">Purchase Price</th>
                                        <th style="color: #5800cf;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in invoiceDetails.invoice_products" :key="product.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ product?.products?.name }}</td>
                                        <td>{{ product.qty }}</td>
                                        <td>{{ product?.products?.price }}</td>
                                        <td>{{ product.purchase_price }}</td>
                                        <td class="text-dark" style="font-weight: 500;">{{ product.qty * product.purchase_price }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Amount Summary -->
                        <div class="mt-4 p-3"
                            style="background-color: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <h4 class="text-center mt-3"
                                style="color: #5800cf; border-bottom: 2px solid #5800cf; padding-bottom: 8px; display: inline-block;">
                                Invoice Breakdown</h4>
                            <hr style="border-top: 1px dashed #5800cf;">

                            <p class="summary-item" style="line-height: 2;"><strong style="color: #5800cf;">Total:
                                    $</strong> <span class="text-dark" style="font-weight: 500;" id="getTotal"> {{
                                        invoiceDetails.invoice_details.total }} </span></p>

                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #5800cf;">Paid:</strong>
                                <span class="text-dark" style="font-weight: 500;"> {{
                                    invoiceDetails.invoice_details.paid }}</span>
                            </p>
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #5800cf;">Rest:</strong>
                                <span class="text-dark" style="font-weight: 500;"> {{
                                    invoiceDetails.invoice_details.rest }}</span>
                            </p>
                            <hr style="border-top: 1px dashed #81c784;">
                            <p class="summary-item" style="line-height: 2;"><strong
                                    style="color: #5800cf; font-size: 1.1rem;">Payable:</strong> <span
                                    class="text-danger" style="font-weight: 700; font-size: 1.1rem;">{{
                                        invoiceDetails.invoice_details.account_payable }}</span> </p>
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
</style>
