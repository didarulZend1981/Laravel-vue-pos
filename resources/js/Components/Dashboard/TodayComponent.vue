<script setup>
import { usePage } from '@inertiajs/vue3';

const currentDate = new Date().toISOString().split("T")[0];
const page = usePage();
const todaysSoldCount = page.props.todaysSoldCount || 0;
const todaysSoldAmount = page.props.todaysSoldAmount || 0;
const todaysRestAmount = page.props.todaysRestAmount || 0;
const todaysReceivedAmount = page.props.todaysReceivedAmount || 0;
const todaysPurchaseProductsCount = page.props.todaysPurchaseProductsCount || 0;
const todaysPurchasedAmount = page.props.todaysPurchasedAmount || 0;
const todaysPaidAmount = page.props.todaysPaidAmount || 0;
const todaysAccPayableAmount = page.props.todaysAccPayableAmount || 0;
</script>

<template>
    <!-- Daily business status -->
    <div style="border: 1px solid #81ecff;">
        <div class="p-2 mb-2 d-flex justify-content-between" style="background: #dcf9fe;">
            <h5 class="text-info font-weight-bold"> Today's Business Status  </h5>
            <p class="text-info">{{ currentDate }}</p>
        </div>
        <div class="row p-2">
            <!-- Today's sold product -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Sold Products
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysSoldCount.toLocaleString() }} </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-cart-arrow-down fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's total sold amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Total Sold Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysSoldAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's total received amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Total Received Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysReceivedAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's total rest amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Total Rest Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysRestAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's purchased product -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's <span class="text-danger">Purchased</span> Products
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysPurchaseProductsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Today's total sold amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Total <span class="text-danger">Purchased</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysPurchasedAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's total received amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Total <span class="text-danger">Paid</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysPaidAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

              <!-- Today's total rest amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's <span class="text-danger">Account payable</span>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ todaysAccPayableAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
