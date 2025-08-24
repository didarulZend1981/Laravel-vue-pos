<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";

const page = usePage();
const loading = ref(false);
const supplierDetails = ref(null);

const Header = [
    { text: "No", value: "no" },
    { text: "Name", value: "name" },
    { text: "Email", value: "email" },
    { text: "Phone", value: "phone" },
    { text: "Address", value: "address" },
    { text: "Company", value: "company" },
    { text: "Action", value: "number" },
];

const Item = computed(() => {
    return page.props.supplier?.map((supplier, index) => ({
        no: index + 1,
        name: supplier.name,
        email: supplier.email,
        phone: supplier.phone,
        address: supplier.address || "N/A",
        company: supplier?.brand?.name ?? supplier?.company_name,
        id: supplier.id,
    })) || [];
});

//search functionalify
const searchValue = ref("");
const searchField = ref(["name", "email", "phone", "address", "company"]);

// supplier details 
async function detailsModalShow(id) {
    try {
        loading.value = true;
        const response = await axios.get(route('supplier.details', { id: id }));
        loading.value = false;
        supplierDetails.value = response.data;
        $('#supplierProfileModal').modal('show'); // Show the modal
    } catch (error) {
        errorToast("Error fetching invoice details");
        loading.value = false;
    }
}


//delete supplier
const deleteSupplier = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this suppplier?')) {
        router.delete(route('delete.supplier', { id: id }), {
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
        <h1 class="h3 mb-3 text-gray-800">Supplier</h1>

        <!-- supplier data table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">

                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                <Link class="btn btn-sm btn-info" :href="route('show.save.supplier')">
                Add Supplier </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" border-cell
                        theme-color="#36b9cc" :rows-per-page="15" :search-field="searchField"
                        :search-value="searchValue">
                        <template #item-number="{ id }">
                            <!-- view supplier details -->
                            <button class="btn btn-sm btn-info mr-2" @click.prevent="detailsModalShow(id)">
                                <i class="fa fa-address-card"></i>
                            </button>

                            <!-- edit supplier -->
                            <Link type="button" class="btn btn-sm btn-outline-success"
                                :href="route('show.save.supplier', { id: id })">
                            <i class="fa fa-edit"></i>
                            </Link>

                            <!-- delete supplier -->
                            <button class="btn btn-sm btn-outline-danger ml-2" @click.prevent="deleteSupplier(id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- customer details modal -->
    <div class="modal fade" id="supplierProfileModal" tabindex="-1" role="dialog"
        aria-labelledby="supplierProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" v-if="supplierDetails">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="supplierProfileModalLabel">Supplier Information</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="/asset/img/placeholder-person.jpg" class="img-fluid rounded-circle mb-3"
                                style="max-width: 50%;" alt="Profile Picture">
                            <h5>{{ supplierDetails?.supplier?.name }}</h5>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6>Contact Information</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-envelope mr-2"></i> {{ supplierDetails?.supplier?.email ??
                                            'N/A' }} </li>
                                        <li><i class="fas fa-phone mr-2"></i> {{ supplierDetails?.supplier?.phone ??
                                            'N/A'
                                            }} </li>
                                        <li><i class="fas fa-map-marker-alt mr-2"></i> {{
                                            supplierDetails?.supplier?.address
                                            ?? 'N/A' }} </li>
                                        <li><i class="fa fa-address-card mr-2"></i> {{ supplierDetails?.supplier?.brand?.name ??
                                            supplierDetails?.supplier?.company_name }} </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- transection information -->
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
                                                <th scope="col">Paid</th>
                                                <th scope="col">Rest</th>
                                                <th scope="col">Account Payable</th>
                                                <th scope="col">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(invoice, index) in supplierDetails?.supplier?.invoices"
                                                :key="index">
                                                <th scope="row">{{ index + 1 }}</th>
                                                <td>{{ invoice?.invoice_name ?? "N/A" }}</td>
                                                <td>{{ invoice?.total }}</td>
                                                <td>{{ invoice?.paid ?? 0 }}</td>
                                                <td>{{ invoice?.rest }}</td>
                                                <td>{{ invoice?.account_payable }}</td>
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
                                supplierDetails?.transectionWithSupplier === 0 ? '0' :
                                    supplierDetails?.transectionWithSupplier }}
                                    Tk A/C Payable</span> </h6>
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
