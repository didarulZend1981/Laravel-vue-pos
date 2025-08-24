<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import SupplierSaveComponent from '@/Components/Supplier/SupplierSaveComponent.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title> POS || Supplier Save </title>
    </Head>

    <DashboardLayout>
        <SupplierSaveComponent/>
    </DashboardLayout>
</template>

<style scoped></style>

