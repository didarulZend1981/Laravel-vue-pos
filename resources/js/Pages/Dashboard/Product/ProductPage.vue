<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProductListComponent from '@/Components/Product/ProductListComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Product List</title>
    </Head>

    <DashboardLayout>
        <ProductListComponent/>
    </DashboardLayout>
</template>

<style scoped></style>
