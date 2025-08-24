<script setup>
import Topbar from '@/Components/Includes/Topbar.vue';
import Sidebar from '@/Components/Includes/Sidebar.vue';
</script>

<template>
    <div id="wrapper">
        <Sidebar />
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <Topbar />
                <slot>

                </slot>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
