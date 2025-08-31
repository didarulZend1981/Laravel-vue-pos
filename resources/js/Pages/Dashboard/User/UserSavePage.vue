<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head} from '@inertiajs/vue3';

import UserSaveComponent from '@/Components/User/UserSaveComponent.vue';

</script>

<template>
    <Head>
        <title> POS || Save Customers </title>
    </Head>

    <DashboardLayout>

        <UserSaveComponent/>
    </DashboardLayout>

</template>

<style scoped></style>
