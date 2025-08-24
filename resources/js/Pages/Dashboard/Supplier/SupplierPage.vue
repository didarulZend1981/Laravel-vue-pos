<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import SupplierListComponent from '@/Components/Supplier/SupplierListComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Supplier </title>
    </Head>

    <DashboardLayout>
        <SupplierListComponent/>
    </DashboardLayout>
</template>

<style scoped></style>
