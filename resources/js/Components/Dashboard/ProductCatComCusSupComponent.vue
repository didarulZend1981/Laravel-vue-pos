<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
const products = page.props.products || 0;
const categories = page.props.categories || 0;
const companies = page.props.companies || 0;
const customers = page.props.customers || 0;
const suppliers = page.props.suppliers || 0;
</script>

<template>
    <!-- Products, Categories, Brands/Companies, Customers & Suppliers -->
    <div class="my-3" style="border: 1px solid #81ecff;">
        <div class="p-2 mb-2" style="background: #dcf9fe;">
            <h5 class="text-info font-weight-bold"> Products, Categories, Brands/Companies, Customers & Suppliers </h5>
        </div>
        <div class="row p-2">
            <!-- Total Products -->
            <div class="col-xl-3 col-md-6 mb-4">
                <Link :href="route('all.products')" style="text-decoration: none;">
                <div class="card py-2 bg-info">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1 text-light">
                                    Total Products
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">{{ products }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-cart-arrow-down fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </Link>
            </div>

            <!-- Total Category -->
            <div class="col-xl-3 col-md-6 mb-4">
                <Link :href="route('all.categories')" style="text-decoration: none;">
                <div class="card py-2 bg-info">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1 text-light">
                                    Total Categories
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">{{ categories }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-th-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </Link>
            </div>

            <!-- Total Companies -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card py-2 bg-info">
                    <Link :href="route('brand.page')" style="text-decoration: none;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Total Companies
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">{{ companies }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-home fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-xl-3 col-md-6 mb-4">
                <Link :href="route('customer.page')" style="text-decoration: none;">
                <div class="card py-2 bg-info">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1 text-light">
                                    Total Customers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">{{ customers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </Link>
            </div>

            <!-- Total Suppliers -->
            <div class="col-xl-3 col-md-6 mb-4">
                <Link :href="route('supplier.page')" style="text-decoration: none;">
                <div class="card py-2 bg-info">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Total Suppliers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">{{ suppliers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
