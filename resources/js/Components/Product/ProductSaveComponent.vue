<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Loader from '../Loader/Loader.vue';

// Fetch props from the server
const loading = ref(false);
const page = usePage();
const brands = page.props.brands || [];
const categories = page.props.categories || [];
const product = page.props.product || {};


// Form data
const form = useForm({
    name: product.name || '',
    price: product.price || '',
    unit: product.unit || '',
    discount_price: product.discount_price || '',
    stock: product.stock || '',
    status: product.status || '',
    brand_id: product.brand_id || '',
    category_id: product.category_id || '',
    description: product.description || '',
    image: product.image || '',
});

// Function to handle form submission
function saveProduct() {
    loading.value = true;
    const routeName = product.id ? 'update.product' : 'store.product';
    form.post(route(routeName, { id: product.id }), {
        onSuccess: () => {
            loading.value = false;
            successToast(page.props.flash.message || 'Product saved successfully!');
            form.reset();
        },
        onError: (errors) => {
            loading.value = false;
            if (errors.name) {
                errorToast(errors.name);
            } else if (errors.price) {
                errorToast(errors.price);
            } else if (errors.unit) {
                errorToast(errors.unit);
            } else if (errors.discount_price) {
                errorToast(errors.discount_price);
            } else if (errors.stock) {
                errorToast(errors.stock);
            } else if (errors.status) {
                errorToast(errors.status);
            } else if (errors.brand_id) {
                errorToast(errors.brand_id);
            } else if (errors.category_id) {
                errorToast(errors.category_id);
            } else if (errors.description) {
                errorToast(errors.description)
            } else if (errors.image) {
                errorToast(errors.image)
            } else {
                errorToast('Failed to save product.');
            }
        },
    });
}

// Reactive property for dynamic image preview
const imagePreview = ref(
    product.image ? `/storage/${product.image}` : '/asset/img/placeholder.jpg'
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
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Save Product</h6>
                        <Link :href="route('product.page')" class="btn btn-sm btn-info">Back to list</Link>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="saveProduct()">
                            <!-- Category -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category</label>
                                    <select v-model="form.category_id" class="form-select form-control" id="category">
                                        <option value="">-- Select Category --</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Brand -->
                                <div class="col-md-6">
                                    <label for="brand" class="form-label">Brand</label>
                                    <select v-model="form.brand_id" class="form-select form-control" id="brand">
                                        <option value="">-- Select Brand --</option>
                                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                            {{ brand.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <!-- product name -->
                                <div class="col-md-6">
                                    <label for="productName">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" v-model="form.name" class="form-control" id="productName"
                                        placeholder="Product Name" />
                                </div>
                                <!-- product price -->
                                <div class="col-md-2"> <label for="productPrice">Price <span
                                            class="text-danger">*</span></label>
                                    <input type="number" v-model="form.price" class="form-control" id="productPrice"
                                        placeholder="Price" />
                                </div>
                            
                                <!-- unit -->
                                <div class="col-md-2"> <label for="productUnit">Unit (e.g., KG, Pcs) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" v-model="form.unit" class="form-control" id="productUnit"
                                        placeholder="Unit" />
                                </div>

                                <!-- stock quantity -->
                                <div class="col-md-2"> <label for="productStock">Stock qty<span
                                            class="text-danger"> *</span></label>
                                    <input type="number" v-model="form.stock" class="form-control" id="productStock"
                                        placeholder="Stock" />
                                </div>
                            </div>

                            <div class=" mb-3">
                                <label for="description">Description</label>
                                <textarea v-model="form.description" class="form-control" id="description"
                                    style="height: 80px;"></textarea>
                            </div>


                            <!-- Image Field -->
                            <div class="mb-3">
                                <label for="productImage" class="form-label mb-2">Product Image</label>
                                <img :src="imagePreview" alt="Image Preview" class="img-fluid mb-3"
                                    style="display:block; max-height: 100px;" />
                                <input type="file" class="form-control-file" id="productImage"
                                    @input="handleImageInput" />
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-3">
                                <button type="submit" class="btn btn-info btn-block">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>

<style scoped>
.dropdown-menu {
    display: none;
}

.dropdown:hover>.dropdown-menu {
    display: block;
    position: absolute;
    left: 100%;
    top: 0;
    margin-top: -1px;
}
</style>
