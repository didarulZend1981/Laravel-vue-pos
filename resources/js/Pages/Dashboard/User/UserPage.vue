<script setup>

import UserListComponent from '@/Components/User/UserListComponent.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title> POS || User </title>
    </Head>

    <DashboardLayout>
        <UserListComponent />
    </DashboardLayout>
</template>

<style scoped>

</style>
