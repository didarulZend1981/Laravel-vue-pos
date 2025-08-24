<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomerlistComponent from '@/Components/Customer/CustomerlistComponent.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head>
        <title> POS || Customers </title>
    </Head>

    <DashboardLayout>
        <CustomerlistComponent />
    </DashboardLayout>
</template>

<style scoped></style>
