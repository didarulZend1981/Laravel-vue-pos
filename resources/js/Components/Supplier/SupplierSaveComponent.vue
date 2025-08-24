<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Loader from '../Loader/Loader.vue';
import { ref } from 'vue';

const page = usePage();
const loading = ref(false);

// Get brands from the props
const brands = page.props.brands || [];

// Check if supplier data exists
const supplier = page.props.supplier || {};

// Initialize form with existing data or defaults
const form = useForm({
    name: supplier.name || '',
    email: supplier.email || '',
    phone: supplier.phone || '',
    brand_id: supplier.brand_id || '', // Bind brand ID here
    address: supplier.address || '',
});

function saveSupplier() {
    loading.value = true;
    const routeName = supplier.id ? 'update.supplier' : 'store.supplier';
    form.post(route(routeName, { id: supplier.id }), {
        onSuccess: () => {
            loading.value = false;
            if (page.props.flash.status === true) {
                successToast(page.props.flash.message);
                form.reset();
            } else {
                errorToast(page.props.flash.message);
            }
        },
        onError: (errors) => {
            loading.value = false;
            if (errors.name) {
                errorToast(errors.name)
            } else if (errors.email) {
                errorToast(errors.email)
            } else if (errors.phone) {
                errorToast(errors.phone)
            } else if (errors.brand_id) {
                errorToast(errors.brand_id)
            } else if (errors.address) {
                errorToast(errors.address)
            } else {
                errorToast('Failed to save supplier');
            }
        }
    });
}
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Save Supplier</h6>
                        <Link :href="route('supplier.page')" class="btn btn-sm btn-info">Back to list</Link>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="saveSupplier()">
                            <div class="mb-3">
                                <select v-model="form.brand_id" class="form-select form-control" id="brand"
                                    name="brand_id">
                                    <option value="">--Select Brand--</option>
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input v-model="form.name" type="text" class="form-control" id="name"
                                    placeholder="Full Name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input v-model="form.email" type="email" class="form-control" id="email"
                                    placeholder="Email">
                            </div>
                            <div class="mb-3">
                                <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input v-model="form.phone" type="tel" class="form-control" id="phone"
                                    placeholder="Phone">
                            </div>
                            <div class="mb-3">
                                <label for="address">Address</label>
                                <textarea v-model="form.address" id="address" class="form-control"
                                    placeholder="Address"></textarea>
                            </div>
                            <button type="submit" class="btn btn-info">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>


<style scoped></style>
