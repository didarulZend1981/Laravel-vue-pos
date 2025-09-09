<script setup>
import { computed, ref, watch } from "vue";
import EasyDataTable from "vue3-easy-data-table"; // Make sure this is installed
const props = defineProps({
    suppliers: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(["pick"]);

const supplierSearchValue = ref("");
const supplierSearchField = ref(["name", "address", "phone"]);

// DataTable Headers
const supplierHeader = [
    { text: "Type", value: "type" },
    { text: "Name", value: "name" },
    { text: "Action", value: "number" },
];

// Format supplier data
const supplierItems = computed(() => {
    return props.suppliers.map((supplier) => ({
        type: `<i class="fa fa-user"></i>`,
        name: supplier.name,
        id: supplier.id,
        address: supplier.address,
        phone: supplier.phone
    }));
});

function pickSupplier(id) {
    emit("pick", id);
}
</script>

<template>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <div>
                <h6 class="mt-1 font-weight-bold text-info">Pick Supplier</h6>
            </div>
            <div>
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="supplierSearchValue">
            </div>
        </div>
        <div class="card-body">
            <EasyDataTable buttons-pagination alternating :headers="supplierHeader"
                :items="supplierItems" border-cell theme-color="#36b9cc" :rows-per-page="15"
                :search-field="supplierSearchField" :search-value="supplierSearchValue">
                <template #item-type="{ type }">
                    <span
                        class="text-info rounded-circle border border-info d-inline-flex justify-content-center align-items-center"
                        style="width: 30px; height: 30px; font-size: 1rem;" v-html="type">
                    </span>
                </template>

                <template #item-number="{ id }">
                    <button class="btn btn-sm btn-outline-success" @click="pickSupplier(id)">
                        <i class="fa fa-plus"></i>
                    </button>
                </template>
            </EasyDataTable>
        </div>
    </div>
</template>
