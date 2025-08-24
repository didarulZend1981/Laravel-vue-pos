<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const saleReportForm = useForm({
    saleDateFrom: '',
    saleDateTo: '',
});

const purchaseReportForm = useForm({
    purchaseDataFrom: '',
    purchaseDataTo: '',
});

function SalesReport() {
    if (saleReportForm.saleDateFrom === '' || saleReportForm.saleDateTo === '') {
        errorToast('Please select both dates');
    } else {
        // Properly pass the parameters to the Laravel route
        let url = '/report/sales/' + saleReportForm.saleDateFrom + '/' + saleReportForm.saleDateTo;
        window.open(url, '_blank');
    }
}

function PurchaseReport() {
    if (purchaseReportForm.purchaseDataFrom === '' || purchaseReportForm.purchaseDataTo === '') {
        errorToast('Please select both dates');
    } else {
        // Properly pass the parameters to the Laravel route
        let url = '/report/purchase/' + purchaseReportForm.purchaseDataFrom + '/' + purchaseReportForm.purchaseDataTo;
        window.open(url, '_blank');
    }
}
</script>

<template>

    <Head>
        <title> POS || Product </title>
    </Head>

    <DashboardLayout>
        <div class="container-fluid">
            <div class="row mb-3">
                <!-- sales report -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4 h-100">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <div>
                                <h4 class="m-0 font-weight-bold text-info">Sales Report</h4>
                            </div>
                            <div>
                                <h4 class="m-0 font-weight-bold text-info">#POS</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="SalesReport">
                                <div class="form-floating mb-3">
                                    <label for="fromDate">From Date</label>
                                    <input v-model="saleReportForm.saleDateFrom" type="date" class="form-control">
                                </div>
                                <div class="form-floating mb-3">
                                    <label for="toDate">To Date</label>
                                    <input v-model="saleReportForm.saleDateTo" type="date" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-info w-100 d-block">Download</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- product buy report -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4 h-100">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <div>
                                <h4 class="m-0 font-weight-bold text-info">Purchase Report</h4>
                            </div>
                            <div>
                                <h4 class="m-0 font-weight-bold text-info">#POS</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <form @submit.prevent="PurchaseReport">
                                <div class="form-floating mb-3">
                                    <label for="fromDate">From Date</label>
                                    <input v-model="purchaseReportForm.purchaseDataFrom" type="date"
                                        class="form-control">
                                </div>
                                <div class="form-floating mb-3">
                                    <label for="toDate">To Date</label>
                                    <input v-model="purchaseReportForm.purchaseDataTo" type="date" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-info w-100 d-block">Download</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>

</template>

<style scoped></style>
