<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProductByCategoryComponent from '@/Components/Product/ProductByCategoryComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Category With Products</title>
    </Head>

    <DashboardLayout>
        <ProductByCategoryComponent/>
    </DashboardLayout>
</template>

<style scoped></style>