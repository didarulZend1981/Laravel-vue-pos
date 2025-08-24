<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const purchaseInvoices = page.props.purchaseInvoices || 0;
const totalPurshaseAmount = page.props.totalPurshaseAmount || 0;
const totalPaidAmount = page.props.totalPaidAmount || 0;
const accPayableAmount = page.props.accPayableAmount || 0;
</script>

<template>
    <!-- Purshase -->
    <div class="mt-3" style="border: 1px solid #81ecff;">
        <div class="p-2 mb-2" style="background: #dcf9fe;">
            <h5 class="text-info font-weight-bold"> Purshase Information </h5>
        </div>
        <div class="row p-2">
            <!-- Total Purchase -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total <span class="text-danger">Purchase</span>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ purchaseInvoices }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Purchase Amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    <span class="text-danger">Purchase</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ totalPurshaseAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Paid Amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    <span class="text-danger">Paid</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ totalPaidAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- account payable -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Account <span class="text-danger">Payable</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ accPayableAmount.toLocaleString() }} /-</div>
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
