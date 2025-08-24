<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const saleInvoices = page.props.saleInvoices || 0;
const totalAmount = page.props.totalAmount || 0;
const totalReceivedAmount = page.props.totalReceivedAmount || 0;
const acReceivableAmount = page.props.acReceivableAmount || 0;
</script>

<template>
    <!-- Sales -->
    <div class="mt-3" style="border: 1px solid #81ecff;">
        <div class="p-2 mb-2" style="background: #dcf9fe;">
            <h5 class="text-info font-weight-bold"> Sales Information </h5>
        </div>
        <div class="row p-2">
            <!-- Total Sales -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total <span class="text-danger">Sales</span>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ saleInvoices }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Sale Amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total <span class="text-danger">Sale</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ totalAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Received amount -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total <span class="text-danger">Received</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ totalReceivedAmount.toLocaleString() }} /-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Receivable -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Account <span class="text-danger">Receivable</span> Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ acReceivableAmount.toLocaleString() }} /-</div>
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
