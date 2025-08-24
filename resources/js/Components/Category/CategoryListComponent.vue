<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";

const page = usePage();
const loading = ref(false);

// Table headers
const Header = [
    { text: "No", value: "no" },
    { text: "Image", value: "image" },
    { text: "Category name", value: "name" },
    { text: "Description", value: "description" },
    { text: "Action", value: "action" },
];

const Item = computed(() => {
    return page.props.category?.map((item, index) => ({
        no: index + 1,
        image: item.image,
        name: item.name,
        description: item.description || "N/A",
        id: item.id,
    })) || [];
});

// Search functionality
const searchValue = ref("");
const searchField = ref(["name"]);

//delete category
const deleteCategory = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this category?')) {
        router.delete(route('delete.category', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                } else {
                    errorToast(page.props.flash.message || 'Failed to delete category!');
                }
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete category!');
            }
        });
    }
}
</script>


<template>
    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Product Categories</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">
                <Link class="btn btn-sm btn-info" :href="route('show.save.category')">
                Add Category
                </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable :headers="Header" :items="Item" border-cell theme-color="#36b9cc" :rows-per-page="15"
                        :search-field="searchField" :search-value="searchValue">
                         <template #item-image="{ image }">
                            <img :src="image ? `/storage/category/${image}` : '/asset/img/placeholder.jpg'"
                                alt="Category Image" style="width: 50px; height: 50px; object-fit: cover;" class="p-1">
                        </template>

                        <template #item-action="{ id }">
                            <Link type="button" class="btn btn-sm btn-outline-success"
                                :href="route('show.save.category', { id: id })">
                            <i class="fa fa-edit"></i>
                            </Link>

                            <button class="btn btn-sm btn-outline-danger ml-2" @click.prevent="deleteCategory(id)">
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

<style scoped>
table td {
    padding-left: 0.5rem;
}
</style>
