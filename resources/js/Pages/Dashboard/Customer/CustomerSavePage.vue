<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomerSaveComponent from '@/Components/Customer/CustomerSaveComponent.vue';
import { Head} from '@inertiajs/vue3';

</script>

<template>

    <Head>
        <title> POS || Save Customers </title>
    </Head>

    <DashboardLayout>
        <CustomerSaveComponent />
    </DashboardLayout>
</template>

<style scoped></style>
