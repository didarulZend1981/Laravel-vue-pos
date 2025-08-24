<script setup>
import BrandListComponent from '@/Components/Brand/BrandListComponent.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title> POS || Brand </title>
    </Head>

    <DashboardLayout>
        <BrandListComponent />
    </DashboardLayout>
</template>

<style scoped>

</style>
