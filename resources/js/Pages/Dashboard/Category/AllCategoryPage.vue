<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import AllCategoryComponent from '@/Components/Category/AllCategoryComponent.vue';
</script>

<template>
    <Head>
        <title> POS || All Categories </title>
    </Head>

    <DashboardLayout>
        <AllCategoryComponent />
    </DashboardLayout>
</template>

<style scoped>

</style>

