<script setup>
import WasteListComponent from '@/Components/Waste/WasteListComponent.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head>
        <title> POS || All Waste </title>
    </Head>

    <DashboardLayout>

        <WasteListComponent>
            
        </WasteListComponent>




    </DashboardLayout>
</template>

<style scoped>

</style>

