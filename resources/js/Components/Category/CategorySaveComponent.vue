<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Loader from '../Loader/Loader.vue';
import { ref } from 'vue';

const page = usePage();
const loading = ref(false);

// Check if category data exists
const category = page.props.category || {};

// Initialize form with existing data or defaults
const form = useForm({
    name: category.name || '',
    description: category.description || '',
    image : category.image || '',
});

function saveCategory() {
    loading.value = true;
    const routeName = category.id ? 'update.category' : 'store.category';
    form.post(route(routeName, { id: category.id }), {
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
            } else if (errors.description) {
                errorToast(errors.description)
            } else if (errors.image) {
                errorToast(errors.image)
            }
             else {
                errorToast('Failed to save category');
            }
        },
    });
}

// Reactive property for dynamic image preview
const imagePreview = ref(
    category.image ? `/storage/category/${category?.image}` : '/asset/img/placeholder.jpg'
);

// Update image preview dynamically
function handleImageInput(event) {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>


<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Save Category</h6>
                        <Link :href="route('category.page')" class="btn btn-sm btn-info">Back to list</Link>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="saveCategory()">
                            <div class="mb-3">
                                <label for="name" class="form-label"> Category Name <span
                                        class="text-danger">*</span></label>
                                <input v-model="form.name" type="text" class="form-control" id="name">
                            </div>

                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea v-model="form.description" id="description" class="form-control"></textarea>
                            </div>

                            <!-- Image Field -->
                            <div class="mb-3">
                                <label for="categoryImage" class="form-label">Category Image</label>
                                <img :src="imagePreview" alt="Image Preview" class="img-fluid mb-3"
                                    style="display:block; max-height: 80px;" />
                                <input type="file" class="form-control" id="categoryImage"
                                    @input="handleImageInput" />
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
