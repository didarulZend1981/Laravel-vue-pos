<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head>
        <title> POS || 404 </title>
    </Head>

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <div class="text-center">
                        <div class="error mx-auto" data-text="404">404</div>
                        <p class="lead text-gray-800 mb-5">Page Not Found</p>
                        <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
                        <Link href="/dashboard">&larr; Back to Dashboard</Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Wrapper Styling */
#wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f8f9fc;
}

/* Content Wrapper */
#content-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}

/* Centering Content */
#content .container-fluid {
    text-align: center;
}

.error {
    font-size: 100px;
    font-weight: bold;
    color: #e74c3c;
    margin-bottom: 20px;
}

.lead {
    font-size: 20px;
    color: #6c757d;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-500 {
    color: #858796 !important;
}

a {
    color: #4e73df;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
