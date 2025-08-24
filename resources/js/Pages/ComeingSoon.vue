<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head>
        <title>POS || Comeing soon</title>
    </Head>

    <div class="min-vh-100 d-flex justify-content-center align-items-center bg-light"
        style="background: linear-gradient(135deg, #6f86d6, #48c6ef);">

        <div class="card shadow-lg p-4" style="width: 350px; border-radius: 16px; background: #ffffffee;">
            <h5 class="text-center mb-4" style="color: #4e73df;"> The Google login feature is currently under construction </h5>

            <div class="text-center">
                <Link :href="route('signIn.page')" class="btn btn-success w-100">Login</Link>
            </div>

        </div>
    </div>
</template>
