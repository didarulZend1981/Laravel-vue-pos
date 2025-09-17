

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
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref,computed } from "vue";


const page = usePage()


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
