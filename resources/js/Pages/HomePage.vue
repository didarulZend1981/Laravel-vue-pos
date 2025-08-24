<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head>
        <title>POS || Home</title>
    </Head>

    <div class="min-vh-100 d-flex justify-content-center align-items-center bg-light"
        style="background: linear-gradient(135deg, #6f86d6, #48c6ef);">

        <div class="card shadow-lg p-4" style="width: 350px; border-radius: 16px; background: #ffffffee;">
            <h2 class="text-center mb-4" style="color: #4e73df;">Welcome to POS</h2>

            <div class="row mb-3">
                <div class="col-6 d-flex justify-content-end pr-1">
                    <Link :href="route('signIn.page')" class="btn btn-primary w-100"
                        style="background-color: #4e73df; border: none;">Login</Link>
                </div>
                <div class="col-6 d-flex justify-content-start pl-1">
                    <Link :href="route('signup.page')" class="btn btn-outline-primary w-100">Register</Link>
                </div>
            </div>

            <div class="text-center">
                <Link :href="route('show.dashboard')" class="btn btn-success w-100">Dashboard</Link>
            </div>

        </div>
    </div>
</template>
