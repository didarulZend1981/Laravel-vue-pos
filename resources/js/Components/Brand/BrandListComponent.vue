<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";


const loading = ref(false);
const page = usePage();
const Header = [
    { text: "No", value: "no" },
    { text: "Name", value: "name" },
    { text: "Description", value: "description" },
    { text: "Action", value: "number" },
];

const Item = computed(() => {
    return page.props.brand?.map((brand, index) => ({
        no: index + 1,
        name: brand.name,
        description: brand.description || "N/A",
        id: brand.id,
    })) || [];
});

//serch functionality
const searchValue = ref("");
const searchField = ref(["name"]);

// delete brand/company
const deleteBrand = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this brand?')) {
        router.delete(route('delete.brand', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                } else {
                    errorToast(page.props.flash.message || 'Failed to delete brand!');
                }
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete brand!');
            }
        });
    }
}
</script>

<template>
    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Brands/Companies</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                <Link class="btn btn-sm btn-info" :href="route('show.save.brand')">
                Add Brand/Company </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" border-cell
                        theme-color="#36b9cc" :rows-per-page="15" :search-field="searchField"
                        :search-value="searchValue">
                        <template #item-number="{ id }">
                            <Link type="button" class="btn btn-sm btn-outline-success"
                                :href="route('show.save.brand', { id: id })">
                            <i class="fa fa-edit"></i>
                            </Link>

                            <button class="btn btn-sm btn-outline-danger ml-2" @click.prevent="deleteBrand(id)">
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
