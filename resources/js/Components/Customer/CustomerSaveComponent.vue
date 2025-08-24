<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Loader from '../Loader/Loader.vue';
import { ref } from 'vue';

const page = usePage();
const loading = ref(false);

// Check if customer data exists
const customer = page.props.customer || {};

// Initialize form with existing data or defaults
const form = useForm({
    name: customer.name || '',
    email: customer.email || '',
    phone: customer.phone || '',
    zip: customer.zip || '',
    address: customer.address || '',
    comment: customer.comment || '',
});

function saveCustomer() {
    loading.value = true;
    const routeName = customer.id ? 'update.customer' : 'store.customer';
    form.post(route(routeName, { id: customer.id }), {
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
                errorToast(errors.name);
            } else if (errors.email) {
                errorToast(errors.email)
            } else if (errors.phone) {
                errorToast(errors.phone)
            } else if (errors.zip) {
                errorToast(errors.zip)
            } else if (errors.address) {
                errorToast(errors.address)
            } else if (errors.comment) {
                errorToast(errors.comment)
            } else {
                errorToast('Failed to save customer');
            }
        }
    });
}
</script>


<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <div>
                            <h6 class="m-0 font-weight-bold text-info">Save Customer</h6>
                        </div>
                        <div>
                            <Link :href="route('customer.page')" class="btn btn-sm btn-info">Back to list
                            </Link>
                        </div>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="saveCustomer()">
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="name" class="form_label"> Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input v-model="form.name" type="text" class="form-control" id="name"
                                        placeholder="Full Name">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span>
                                    </label>
                                    <input v-model="form.email" type="email" class="form-control" id="email"
                                        placeholder="Email">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="phone">Phone Number(11 degits recomanded) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input v-model="form.phone" type="tel" class="form-control" id="phone"
                                        placeholder="Phone">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="zip">ZIP </label>
                                    <input v-model="form.zip" type="text" class="form-control" placeholder="Zip"
                                        id="zip">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="address">Address </label>
                                    <textarea v-model="form.address" type="text" id="address" class="form-control"
                                        placeholder="Address"> </textarea>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="comment">Comment</label>
                                    <textarea v-model="form.comment" id="comment" class="form-control"
                                        placeholder="Comment"> </textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info"> Save Change </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <Loader v-if="loading" />
</template>

<style scoped></style>
