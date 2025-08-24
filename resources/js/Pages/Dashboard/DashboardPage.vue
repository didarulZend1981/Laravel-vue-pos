<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import ProductCatComCusSupComponent from '@/Components/Dashboard/ProductCatComCusSupComponent.vue';
import TodayComponent from '@/Components/Dashboard/TodayComponent.vue';
import SalesComponent from '@/Components/Dashboard/SalesComponent.vue';
import PurchaseComponent from '@/Components/Dashboard/PurchaseComponent.vue';

const page = usePage();
const StockInDanger = page.props.StockInDanger || 0;
</script>

<template>

    <Head>
        <title> POS || Dashboard </title>
    </Head>

    <DashboardLayout>
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <a href="/report" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm"><i
                        class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
            </div>

            <!-- dangers stock position -->
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Warning</strong> {{ StockInDanger }} + products are in danger of running out of stock.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <Link class="btn btn-sm btn-success" :href="route('low.stock.products')"> Check it out </Link>
            </div>

            <TodayComponent />
            <SalesComponent/>
            <PurchaseComponent/>
            <ProductCatComCusSupComponent />
        </div>
    </DashboardLayout>
</template>

<style scoped></style>
