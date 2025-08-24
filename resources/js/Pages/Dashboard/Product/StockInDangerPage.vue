<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import DangerStockComponent from '@/Components/Product/DangerStockComponent.vue';
</script>

<template>

    <Head>
        <title> POS || Products Stock In Danger</title>
    </Head>

    <DashboardLayout>
        <DangerStockComponent/>
    </DashboardLayout>
</template>

<style scoped></style>