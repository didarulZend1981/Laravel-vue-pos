<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";

const loading = ref(false);
const page = usePage();
const Header = [
    { text: "No", value: "no" },
    { text: "Image", value: "image" },
    { text: "Name", value: "name" },
    { text: "Brand", value: "brand" },
    { text: "Category", value: "category" },
    // { text: "Price", value: "price" },
    { text: "Unit", value: "unit" },
    // { text: "Stock", value: "stock" },
    { text: "Action", value: "number" },
];

const Item = computed(() => {
    return page.props.products?.map((product, index) => ({
        no: index + 1,
        image: product.image,
        name: product?.name,
        // price: product.price,
        unit: product.unit,
        // stock: product.stock,
        category: product.category?.name,
        brand: product.brand?.name || "N/A",
        id: product.id,
    })) || [];
});


//search functionalify
const searchValue = ref("");
const searchField = ref(["name", "brand", "category"]);

// delete product
const deleteProduct = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this product?')) {
        router.delete(route('delete.product', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                } else {
                    errorToast(page.props.flash.message || 'Failed to delete product!');
                }
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete product!');
            }
        });
    }
}
</script>

<template>
    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Products</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                <Link class="btn btn-sm btn-info" :href="route('show.save.product')">
                Add Product
                </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header"
                        :items="Item" border-cell theme-color="#36b9cc"
                        :rows-per-page="10" :search-field="searchField" :search-value="searchValue">

                        <!-- Template for Image -->
                        <template #item-image="{ image }">
                            <img :src="image ? `/storage/${image}` : '/asset/img/placeholder.jpg'"
                                alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;" class="p-1">
                        </template>

                        <!-- Template for Action Buttons -->
                        <template #item-number="{ id }">
                            <Link type="button" class="btn btn-sm btn-outline-success"
                                :href="route('show.save.product', { id: id })">
                            <i class="fa fa-edit"></i>
                            </Link>
                            <button class="btn btn-sm btn-outline-danger ml-2"
                                @click.prevent="deleteProduct(id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>

