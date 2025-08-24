<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head} from '@inertiajs/vue3';
import BrandSaveComponent from '@/Components/Brand/BrandSaveComponent.vue';

</script>

<template>
    <Head>
        <title> POS || Save Customers </title>
    </Head>

    <DashboardLayout>
        <BrandSaveComponent />
    </DashboardLayout>

</template>

<style scoped></style>
