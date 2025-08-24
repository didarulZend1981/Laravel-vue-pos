<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import PurchaseInvoiceListComponent from '@/Components/Invoice/PurchaseInvoiceListComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Purchase Invoice </title>
    </Head>

    <DashboardLayout>
        <PurchaseInvoiceListComponent />
    </DashboardLayout>
</template>

<style scoped></style>
