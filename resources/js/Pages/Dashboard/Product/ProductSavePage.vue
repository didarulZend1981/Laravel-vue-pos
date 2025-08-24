<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProductSaveComponent from '@/Components/Product/ProductSaveComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Product Save </title>
    </Head>

    <DashboardLayout>
        <ProductSaveComponent />
    </DashboardLayout>
</template>

<style scoped></style>
