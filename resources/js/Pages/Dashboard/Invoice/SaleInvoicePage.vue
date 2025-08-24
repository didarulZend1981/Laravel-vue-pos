<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import SaleInvoiceListComponent from '@/Components/Invoice/SaleInvoiceListComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Sale Invoice </title>
    </Head>

    <DashboardLayout>
        <SaleInvoiceListComponent/>
    </DashboardLayout>
</template>

<style scoped></style>
