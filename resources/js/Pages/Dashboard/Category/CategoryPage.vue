<script setup>
import CategoryListComponent from '@/Components/Category/CategoryListComponent.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title> POS || Category </title>
    </Head>

    <DashboardLayout>
        <CategoryListComponent />
    </DashboardLayout>
</template>

<style scoped>

</style>

