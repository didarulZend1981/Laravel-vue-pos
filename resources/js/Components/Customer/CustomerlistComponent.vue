<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";
import axios from "axios";

const page = usePage();
const loading = ref(false);
const customerDetails = ref(null);

const Header = [
    { text: "No", value: "no" },
    { text: "Name", value: "name" },
    { text: "Email", value: "email" },
    { text: "Phone", value: "phone" },
    { text: "Zip", value: "zip" },
    { text: "Address", value: "address" },
    { text: "Comment", value: "comment" },
    { text: "Action", value: "number" },
];

// Map backend data to the `Item` format
const Item = computed(() => {
    return page.props.customers?.map((customer, index) => ({
        no: index + 1,
        name: customer.name,
        email: customer.email,
        phone: customer.phone,
        zip: customer.zip || "N/A",
        address: customer.address || "N/A",
        comment: customer.comment || "N/A",
        id: customer.id,
    })) || [];
});

//search functionalify
const searchValue = ref("");
const searchField = ref(["name", "email", "phone", "zip", "address", "comment"]);

// customer details 
async function detailsModalShow(id) {
    try {
        loading.value = true;
        const response = await axios.get(route('customer.details', { id: id }));
        loading.value = false;
        customerDetails.value = response.data;
        $('#customerProfileModal').modal('show'); // Show the modal
    } catch (error) {
        errorToast("Error fetching invoice details");
        loading.value = false;
    }
}


//delete customer
const deleteCustomer = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this customer?')) {
        router.delete(route('delete.customer', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                } else {
                    errorToast(page.props.flash.message || 'Failed to delete customer!');
                }
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete customer!');
            }
        });
    }
}
</script>

<template>
    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Customers</h1>

        <!-- customer list data table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue" />

                <Link :href="route('show.save.customer')" class="btn btn-sm btn-info">
                Add Customer
                </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" border-cell
                        theme-color="#36b9cc" :rows-per-page="15" :search-field="searchField"
                        :search-value="searchValue">
                        <template #item-number="{ id }">

                            <!-- view customer details -->
                            <button class="btn btn-sm btn-info mr-2" @click.prevent="detailsModalShow(id)">
                                <i class="fa fa-address-card"></i>
                            </button>

                            <!-- edit customer -->
                            <Link type="button" class="btn btn-sm btn-outline-success"
                                :href="route('show.save.customer', { id: id })">
                            <i class="fa fa-edit"></i>
                            </Link>

                            <!-- delete customer -->
                            <button class="btn btn-sm btn-outline-danger ml-2" @click.prevent="deleteCustomer(id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- customer details modal -->
    <div class="modal fade" id="customerProfileModal" tabindex="-1" role="dialog"
        aria-labelledby="customerProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" v-if="customerDetails">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="customerProfileModalLabel">Customer Information</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="/asset/img/placeholder-person.jpg" class="img-fluid rounded-circle mb-3"
                                style="max-width: 50%;" alt="Profile Picture">
                            <h5>{{ customerDetails?.customer?.name }}</h5>
                            <p class="text-info">{{ customerDetails?.customer?.comment }}</p>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6>Contact Information</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-envelope mr-2"></i> {{ customerDetails?.customer?.email ??
                                            'N/A' }} </li>
                                        <li><i class="fas fa-phone mr-2"></i> {{ customerDetails?.customer?.phone ??
                                            'N/A'
                                        }} </li>
                                        <li><i class="fas fa-map-marker-alt mr-2"></i> {{
                                            customerDetails?.customer?.address
                                            ?? 'N/A' }} </li>
                                        <li><i class="fa fa-address-card mr-2"></i> {{ customerDetails?.customer?.zip ??
                                            'N/A' }} </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6>Transection Information</h6>
                                </div>
                                <div>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">SN.</th>
                                                <th scope="col">Inv. Name</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">VAT</th>
                                                <th scope="col">Dis</th>
                                                <th scope="col">Paid</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Payable</th>
                                                <th scope="col">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(invoice, index) in customerDetails?.customer?.invoices"
                                                :key="index">
                                                <th scope="row">{{ index + 1 }}</th>
                                                <td>{{ invoice?.invoice_name ?? "N/A" }}</td>
                                                <td>{{ invoice?.total }}</td>
                                                <td>{{ invoice?.vat ?? 0 }}</td>
                                                <td>{{ invoice?.discount ?? 0 }}</td>
                                                <td>{{ invoice?.subtotal }}</td>
                                                <td>{{ invoice?.paid ?? 0 }}</td>
                                                <td>{{ invoice?.customer_payable }}</td>
                                                <td>{{ new Date(invoice?.created_at).toLocaleString('en-US', {
                                                    year: 'numeric',
                                                    month: 'long',
                                                    day: 'numeric',
                                                    hour: 'numeric',
                                                    minute: '2-digit',
                                                    hour12: true
                                                }) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr style="border-top: 2px dashed #000;">
                            <h6 class="text-right"><strong>Payment Status: </strong> <span class="text-danger"> {{
                                customerDetails?.customerTotalPayableAmount === 0 ? '0' :
                                    customerDetails?.customerTotalPayableAmount }}
                                    Tk Payable</span> </h6>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>
