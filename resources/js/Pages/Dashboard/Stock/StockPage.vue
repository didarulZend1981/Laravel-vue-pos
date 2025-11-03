

<template>

    <Head>
        <title> POS || Stock-Report </title>
    </Head>

    <DashboardLayout>



            <div class="container-fluid">
                    <div class="row mb-3">
                        <!-- sales report -->
                        <div class="col-lg-12">
                            <div class="card shadow mb-4 h-100">
                                <div class="card-header py-3 d-flex justify-content-between">
                                    <div>
                                        <h4 class="m-0 font-weight-bold text-info">Stock Report</h4>
                                    </div>
                                    <div>
                                        <h4 class="m-0 font-weight-bold text-info">#POS</h4>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <form @submit.prevent="SalesReport">
                                        <div class="row">
                                            <div class="row mb-3 align-items-center col-md-4">
                                                    <div class="col-auto">
                                                        <label for="fromDate" class="col-form-label">From Date</label>
                                                    </div>
                                                    <div class="col">
                                                        <input v-model="stockReportForm.stockDateFrom" type="date" class="form-control" id="fromDate">
                                                    </div>
                                                </div>

                                                <div class="row mb-3 align-items-center col-md-4">
                                                    <div class="col-auto">
                                                        <label for="toDate" class="col-form-label">To Date</label>
                                                    </div>
                                                    <div class="col">
                                                        <input v-model="stockReportForm.stockDateTo" type="date" class="form-control" id="toDate">
                                                    </div>
                                                </div>

                                            <button type="submit" class=" col-md-4 btn btn-info w-100 d-block">Submit</button>
                                        </div>

                                    </form>
                                 </div>
                            </div>
                        </div>


                    </div>






                      <div class="mb-4" style="border: 1px solid #81ecff;">
                        <div class="card-header p-2 mb-4 d-flex justify-content-between">
                            <h5 class="text-info font-weight-bold"> Today's Business Status  </h5>
                            <p class="text-info">

                                <strong>From Date:</strong> {{ stockDateFrom }}
                                <strong>To Date:</strong> {{ stockDateTo }}
                            </p>
                        </div>
                        <div class="row  card-body">



                                     <StatCard title="Opening" :quantity="openingQty" :value="openingValue" symbol="৳" />
                                     <StatCard title="Purchase" :quantity="periodPurchase_qty" :value="periodPurchaseAmount" symbol="৳" />
                                     <StatCard title="Total" :quantity="totalPurchaseQty" :value="totalPurchaseAmount" symbol="৳" />
                                     <StatCard title="Sale" :quantity="periodSaleQty" :value="periodSaleAmount" symbol="৳" />

                                     <StatCard title="Closing" :quantity="closingQty" :value="closingValue" symbol="৳" />
                                     <StatCard title="Profit/Loss" :quantity="periodSaleQty" :value="profitOrLoss" symbol="৳" />




                         </div>
                    </div>


             <div class="card shadow mb-4 h-100">




                        <div class="card-header py-3 d-flex justify-content-between">
                                        <div>
                                            <h4 class="m-0 font-weight-bold text-info"><strong>From Date:</strong> {{ stockDateFrom }}</h4>
                                        </div>
                                        <div>
                                            <h4 class="m-0 font-weight-bold text-info"><strong>To Date:</strong> {{ stockDateTo }}</h4>
                                        </div>
                        </div>
                        <div class="card-body">
                               <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">
                            <div class="table-responsive">



                        <EasyDataTable :headers="Header" :items="Item" border-cell theme-color="#36b9cc" :rows-per-page="10"
                        :search-field="searchField" :search-value="searchValue">

                        <template #item-image="{ image }">
                            <img :src="image ? `/storage/${image}` : '/asset/img/placeholder.jpg'"
                                alt="product Image" style="width: 50px; height: 50px; object-fit: cover;" class="p-1">
                        </template>

                    </EasyDataTable>
                            </div>
                        </div>
                </div>

        </div>
    </DashboardLayout>
</template>





<script setup>

import StatCard from '@/Components/Card/StatCard.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref,computed } from "vue";


const page = usePage()
const reportSummaery = page.props.reportSummaery || 0;
const openingQty=reportSummaery.opening_qty || 0;
const openingValue=reportSummaery.opening_value || 0;
const periodPurchase_qty=reportSummaery.period_purchase_qty || 0;
const periodPurchaseAmount=reportSummaery.period_purchase_amount || 0;
const periodSaleQty=reportSummaery.period_sale_qty || 0;
const periodSaleAmount=reportSummaery.period_sale_amount || 0;
const totalPurchaseQty=reportSummaery.total_purchase_qty || 0;
const totalPurchaseAmount=reportSummaery.total_purchase_amount || 0;
const closingQty=reportSummaery.closing_qty || 0;
const closingValue=reportSummaery.closing_value || 0;
const profitOrLoss=reportSummaery.profit_or_loss || 0;




const stockReportForm = useForm({
    stockDateFrom: '',
    stockDateTo: '',
});

// Table headers
const Header = [
    { text: "No", value: "no" },
    { text: "image", value: "image" },
    { text: "product_name", value: "product_name" },
    { text: "opening", value: "opening" },
    { text: "opening_blance", value: "opening_blance" },
    { text: "purchase_blance", value: "purchase" },
    { text: "purchase_blance", value: "purchase_blance" },
    { text: "sale", value: "sale" },
    { text: "sale_blance", value: "sale_blance" },
    { text: "closing", value: "closing" },
    { text: "closing_blance", value: "closing_blance" },
];

// Search functionality
const searchValue = ref("");
const searchField = ref(["product_name"]);

const Item = computed(() => {
    return page.props.report?.map((item, index) => ({
        no: index + 1,
        image: item.image,
        product_name: item.product_name,
        opening: item.opening_qty,
        opening_blance: item.opening_balance,
        purchase: item.purchase_qty,
        purchase_blance: item.purchase_balance,
        sale: item.sale_qty,
        sale_blance: item.sale_balance,
        closing: item.closing_qty,
        closing_blance: item.closing_balance,
    })) || [];
});

function SalesReport() {
    if (stockReportForm.stockDateFrom === '' || stockReportForm.stockDateTo === '') {
        errorToast('Please select both dates');
    } else {
        router.post('/stock/stocks', {
            stockDateFrom: stockReportForm.stockDateFrom,
            stockDateTo: stockReportForm.stockDateTo,
        }, {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                successToast('Report generated');
            },
            onError: () => {
                errorToast('Something went wrong');
            }
        });
    }
}

const stockDateFrom = page.props.stockDateFrom || '';
const stockDateTo = page.props.stockDateTo || '';

</script>

<style scoped></style>
