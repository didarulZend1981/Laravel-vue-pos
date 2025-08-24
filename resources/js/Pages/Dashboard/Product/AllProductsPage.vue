<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import AllProductsComponent from '@/Components/Product/AllProductsComponent.vue';
</script>

<template>

    <Head>
        <title> POS || All Products</title>
    </Head>

    <DashboardLayout>
        <AllProductsComponent/>
    </DashboardLayout>
</template>

<style scoped></style>