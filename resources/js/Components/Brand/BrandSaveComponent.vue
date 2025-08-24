<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Loader from '../Loader/Loader.vue';
import { ref } from 'vue';

const page = usePage();
const loading = ref(false);

// Check if brand data exists
const brand = page.props.brand || {};

// Initialize form with existing data or defaults
const form = useForm({
    name: brand.name || '',
    description: brand.description || '',
});

function saveBrand() {
    loading.value = true;
    const routeName = brand.id ? 'update.brand' : 'store.brand';
    form.post(route(routeName, { id: brand.id }), {
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
            } else if (errors.description) {
                errorToast(errors.description)
            } else {
                errorToast('Failed to save brand');
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
                        <div>
                            <h6 class="m-0 font-weight-bold text-info">Save Brand</h6>
                        </div>
                        <div>
                            <Link :href="route('brand.page')" class="btn btn-sm btn-info">Back to list
                            </Link>
                        </div>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="saveBrand()">
                            <div class="form-floating mb-3">
                                <label for="name" class="form_label"> Full Name <span class="text-danger">*</span>
                                </label>
                                <input v-model="form.name" type="text" class="form-control" id="name"
                                    placeholder="Brand Name">
                            </div>

                            <div class="form-floating mb-3">
                                <label for="description">Description</label>
                                <textarea v-model="form.description" id="description" class="form-control"
                                    placeholder="Description"> </textarea>
                            </div>
                            <button type="submit align-right" class="btn btn-info"> Save Change </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Loader v-if="loading" />
</template>

<style scoped></style>
