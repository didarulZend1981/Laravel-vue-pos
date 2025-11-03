<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { ref, computed,onMounted } from "vue";
import { Head, Link, usePage,useForm } from "@inertiajs/vue3";
import axios from "axios";
import Loader from "@/Components/Loader/Loader.vue";
import SupplierTableComponent from "@/Components/Supplier/SupplierTableComponent.vue";



// Access props passed from the backend
const loading = ref(false);
const { props } = usePage();
const customers = props.customers || [];
// const products = props.products || [];
const products = props.purches || [];
// console.log("purches=======",products)
const currentDate = new Date().toISOString().split("T")[0];

// তারিখ সংরক্ষণ
const selectedDate = ref('')

// ✅ ফরম্যাট করা তারিখ (DD-MM-YYYY)
const formattedDate = computed(() => {
  if (!selectedDate.value) return ''
  const d = new Date(selectedDate.value)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}`
})

// ✅ Component লোড হলে current date বসাও
onMounted(() => {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  selectedDate.value = `${year}-${month}-${day}` // 📌 YYYY-MM-DD format
})


const productHeader = [
    { text: "Image", value: "image", width: 50 },
    { text: "Name", value: "name", width: 130 },
    { text: "Stock", value: "stock", width: 50 },
    { text: "Price", value: "price", width: 50 },
    { text: "Action", value: "action", width: 50 },
];



const productItems = computed(() => {
  // Step 1: স্টক থাকা প্রোডাক্ট এবং expiry_date অনুযায়ী sort করা
  const filteredProducts = products
    .filter(item => (item.stock_qty ?? 0) > 0)
    .sort((a, b) => {
      const aDate = new Date(a.expiry_date);
      const bDate = new Date(b.expiry_date);
      return aDate - bDate; // যেটার তারিখ আগে, সেটা আগে আসবে
    });

  // Step 2: ইউনিক প্রোডাক্ট তৈরি করা (product_id দিয়ে)
  const seen = new Set();
  const uniqueProducts = [];

  for (const item of filteredProducts) {
    if (!seen.has(item.product_id)) {
      seen.add(item.product_id);
      uniqueProducts.push(item);
    }
  }

  // Step 3: uniqueProducts কে বারবার রিপিট করা যতক্ষণ না মূল filteredProducts এর সমান হয়
  const combinedList = [];
  const totalLength = filteredProducts.length;
  const uniqueLength = uniqueProducts.length;

  for (let i = 0; i < totalLength; i++) {
    combinedList.push(uniqueProducts[i % uniqueLength]);
  }

  // Step 4: ফাইনাল ফরম্যাটে রূপান্তর
  return combinedList.map((item) => ({
    image: item.products?.image || '',
    name: item.products?.name || 'No name',
    stock: item.stock_qty,
    price: item.purchase_price,
    id: item.id,
    product_id: item.product_id,
    expiry_date: item.expiry_date,
  }));
});


// Search functionality
const customerSearchValue = ref("");
const customerSearchField = ref(["name"]);
const productSearchValue = ref("");
const productSearchField = ref(["name"]);

//=======================product pick and assing to billign section=================//
const selectedProduct = ref({ name: '', purchase_price: 0, qty: 1, sale_price: 0, amount: 0 });
const invoiceData = ref([]);
const totalAmount = ref(0);
const restAmount = ref(0);
const vatPercentage = ref(0);
const discountPercentage = ref(0);
const invoiceName = ref("");

// Function to select a product
function pickProduct(productId) {
    const product = products.find(prod => prod.id === productId);
    if (product) {
        selectedProduct.value = { ...product, qty: 1 };

        console.log("testiong=================================",selectedProduct.value)
        $('#productCustomization').modal('show');
    }
}

    // qty validation
    function checkQty() {
            if (!selectedProduct.value) return;

            if (selectedProduct.value.qty > selectedProduct.value.stock_qty) {
                alert("Quantity cannot be more than available stock!");
                selectedProduct.value.qty = selectedProduct.value.stock_qty;
            } else if (selectedProduct.value.qty < 1) {
                alert("Quantity cannot be less than 1!");
                selectedProduct.value.qty = 1;
            }
    }

     // checkSalePrice validation





function  addProductToInvlist(event) {
    event.preventDefault(); // prevent form submission

    const newProduct = {
        name: selectedProduct.value.products?.name || 'No name',
        qty: selectedProduct.value.qty,
        rate: selectedProduct.value.purchase_price,
        sale_price: selectedProduct.value.sale_price,
        total: selectedProduct.value.qty * selectedProduct.value.sale_price,
        product_id: selectedProduct.value.product_id,
        id: selectedProduct.value.id,
    };

    invoiceData.value.push(newProduct);
    updateSummary();
    $('#productCustomization').modal('hide');
}

//remove product from invoice table
function removeProduct(index) {
    invoiceData.value.splice(index, 1);
    updateSummary();
}

// Update discount
function discountChange(event) {
    discountPercentage.value = parseFloat(event.target.value) || 0;
    updateSummary();
}

// Update VAT
function vatChange(event) {
    vatPercentage.value = parseFloat(event.target.value) || 0;
    updateSummary();
}

//calculate rest amount
function calculateRest(event) {
    restAmount.value = parseFloat(event.target.value) || 0;
    updateSummary();
}

// Update summary dynamically
function updateSummary() {
    let total = 0;

    // Calculate total for all products
    invoiceData.value.forEach(item => {
        total += item.total;
    });

    totalAmount.value = total;

    // Calculate VAT, discount, and payable
    const vatAmount = (total * vatPercentage.value) / 100;
    const discountAmount = (total * discountPercentage.value) / 100;
    const subTotalAmount = total + vatAmount - discountAmount;
    const paidAmount = restAmount.value;
    const rest_amount = subTotalAmount - restAmount.value;

    // Update DOM bindings (if needed)
    document.getElementById('total').textContent = total.toFixed(2);
    document.getElementById('vat').textContent = vatAmount.toFixed(2);
    document.getElementById('discount').textContent = discountAmount.toFixed(2);
    document.getElementById('subtotal').textContent = subTotalAmount.toFixed(2);
    document.getElementById('paid').textContent = paidAmount.toFixed(2);
    document.getElementById('rest').textContent = rest_amount.toFixed(2);
    document.getElementById('customer_payable').textContent = rest_amount.toFixed(2);
}


//=====================customer pick and assing to billing section==================//
const customerPick = ref({
    name: "",
    phone: "",
    address: "",
    id: "",
});

const pickWalk = (id) => {
    const selectedCustomer = customers.find(customer => customer.id === id);
    if (selectedCustomer) {
        customerPick.value = {
            name: selectedCustomer.name,
            phone: selectedCustomer.phone,
            address: selectedCustomer.address,
            id: selectedCustomer.id,
        };
    }
};

const pickCustomer = (id) => {
    const selectedCustomer = customers.find(customer => customer.id === id);
    if (selectedCustomer) {
        customerPick.value = {
            name: selectedCustomer.name,
            phone: selectedCustomer.phone,
            address: selectedCustomer.address,
            id: selectedCustomer.id,
        };
    }
};

//==============================finally generate invoic==============================//
async function generateInvoice() {
    try {
        loading.value = true;

        const total = parseFloat(document.getElementById('total').innerText);
        const vat = parseFloat(document.getElementById('vat').innerText);
        const discount = parseFloat(document.getElementById('discount').innerText);
        const subtotal = parseFloat(document.getElementById('subtotal').innerText);
        const paid = parseFloat(document.getElementById('paid').innerText);
        const rest = parseFloat(document.getElementById('rest').innerText);
        const customer_payable = parseFloat(document.getElementById('customer_payable').innerText);
        const customer_id = parseInt(document.getElementById('getCusId').innerText);
        const invoice_name = invoiceName.value;

        if (!customer_id) {
            errorToast("Please select customer first");
        } else if (invoiceData.value.length === 0) {
            errorToast("Please add products first");
        } else {
            const data = { total, vat, discount, subtotal, paid, rest, customer_payable, invoice_name, customer_id, products: invoiceData.value,invoice_date: selectedDate.value };
            console.log("test-----",data);
            const response = await axios.post('/sale-invoice/create', data);

            if (response.data.status === true) {
                successToast(response.data.message);
                setTimeout(() => {
                    window.location.href = "/sale-invoice/list";
                }, 1000);
            } else {
                errorToast('error');
            }
        }
    } catch (error) {
        errorToast('An error occurred while creating the invoice.');
    } finally {
        loading.value = false;
    }
}







// function demageProduct(event) {
//     event.preventDefault();
//     // const product_id=selectedProduct.value.product_id
//     const data = {
//             product_id: selectedProduct.value.product_id,
//             waste_qty: selectedProduct.value.stock_qty,
//             purchase_price: selectedProduct.value.purchase_price,
//             case: "borken",
//             purchase_invoice_id: selectedProduct.value.purchase_invoice_id,
//             refound: 0,
//     };
//     // console.log("tesrtt==data=============================================",data)
//     // console.log("tesrtt=============================================",product_id)
//      const response = await axios.post('/waste/damage', data);

//             if (response.data.status === true) {
//                 successToast(response.data.message);
//                 setTimeout(() => {
//                     window.location.href = "/waste/";
//                 }, 1000);
//             } else {
//                 errorToast('error');
//             }




//     successToast("Damage product added to the list");
// }




async function demageProduct(event) {
    event.preventDefault();


    try {
        loading.value = true;


            const form = useForm({
                id: selectedProduct.value.id,
                product_id: selectedProduct.value.product_id,
                waste_qty: selectedProduct.value.qty,
                purchase_price: selectedProduct.value.purchase_price,
                case: "broken",
                purchase_invoice_id: selectedProduct.value.purchase_invoice_id,
                refound: false
            });

            form.post('/waste/damage', {
            onSuccess: () => {
                console.log("Damage recorded");
                $('#productCustomization').modal('hide');
            },
            onError: (errors) => {
                console.error("Validation failed", errors);
            }
            });




     } catch (error) {
        errorToast('An error occurred while creating the invoice.');
    } finally {
        loading.value = false;
    }

    $('#productCustomization').modal('hide');


    // try {
    //     const response = await axios.post(route('waste'), data);

    //     // if (response.data.status === true) {
    //     //     successToast(response.data.message);
    //     //     setTimeout(() => {
    //     //         window.location.href = "/waste/";
    //     //     }, 1000);
    //     // } else {
    //     //     errorToast('Error: ' + (response.data.message || 'Unknown error'));
    //     // }
    // } catch (error) {
    //     // console.error("Error while submitting damaged product:", error);
    //     // errorToast('Request failed: ' + (error.response?.data?.message || error.message));
    // }
}

</script>

<template>

    <Head>
        <title> POS || Create Sale </title>
    </Head>

    <DashboardLayout>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="shadow my-3 p-3 d-flex justify-content-between align-item-center">
                                <div>
                                    <h5 class="text-info font-weight-bold">Create sale for big/previous customer</h5>
                                </div>

                                 <button class="btn btn-sm btn-outline-success" @click="pickWalk(id=1)">
                                               walk Customer <i class="fa fa-plus"></i>
                                            </button>

                                <div>


                                    <Link href="/sale-invoice/custom" class="btn btn-sm btn-info">Create custom sale
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <!-- Billing Section -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <!-- Header -->
                                <div class="card-header py-3 d-flex justify-content-between">
                                    <h4 class="m-0 font-weight-bold text-info">BUILD TO</h4>
                                    <h4 class="m-0 font-weight-bold text-info">POS</h4>
                                </div>

                                <!-- Body -->
                                <div class="card-body">
                                    <!-- Customer Info -->
                                    <div class="d-flex justify-content-between align-items-start mb-3 gap-4">
                                        <div>
                                            <p style="line-height: 0.8;"><strong>Name:</strong> <span
                                                    class="text-info">{{ customerPick.name
                                                    }}</span></p>
                                            <p style="line-height: 0.8;"><strong>Phone:</strong> <span
                                                    class="text-info">{{ customerPick.phone
                                                    }}</span></p>
                                            <p style="line-height: 0.8;"><strong>Address:</strong> <span
                                                    class="text-info">{{ customerPick.address
                                                    }}</span></p>
                                            <p class="d-none"><strong>ID:</strong> <span id="getCusId">{{
                                                customerPick.id
                                                    }}</span></p>
                                        </div>
                                        <div class="text-end" style="max-width: 200px;">
                                    <!-- <p><strong>Date:</strong></p> -->
                                    <input type="date" v-model="selectedDate" class="form-control" />

                                </div>
                                    </div>

                                    <!-- Product Table -->
                                    <table class="table" id="invoiceTable">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>TK</th>
                                                <th>Close</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="invoiceData.length === 0">
                                                <td colspan="4" class="text-center text-muted pt-2">No products found
                                                </td>
                                            </tr>
                                            <tr v-for="(item, index) in invoiceData" :key="index">
                                                <td>{{ item.name }}</td>
                                                <td>{{ item.qty }}</td>
                                                <td>{{ item.total.toFixed(2) }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        @click="removeProduct(index)">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="mb-2" style="background: #636363; height: 2px;"></div>
                                    <!-- VAT & Discount Inputs -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <label>Discount (%)</label>
                                            <input type="number" class="form-control" id="discountPercentage" min="0"
                                                step="0.25" @input="discountChange" />
                                        </div>
                                        <div class="col-6">
                                            <label>VAT (%)</label>
                                            <input type="number" class="form-control" id="vatPercentage" min="0"
                                                step="1" @input="vatChange" />
                                        </div>
                                    </div>

                                    <!-- Summary Breakdown -->
                                    <h4 class="text-center mt-3 text-info">Invoice Summary</h4>
                                    <div class="mb-2" style="background: #636363; height: 2px;"></div>

                                    <div class="summary-item"><strong>Total (৳):</strong> <span class="text-info"
                                            id="total">0.00</span></div>
                                    <div class="summary-item"><strong>Discount (৳):</strong> <span class="text-info"
                                            id="discount">0.00</span></div>
                                    <div class="summary-item"><strong>VAT (৳):</strong> <span class="text-info"
                                            id="vat">0.00</span></div>
                                    <div class="summary-item"><strong>Subtotal (৳):</strong> <span class="text-info"
                                            id="subtotal">0.00</span></div>

                                    <div class="summary-item"><strong>Paid Amount(৳):</strong> <span class="text-info"
                                            id="paid">0.00</span></div>
                                    <div class="d-flex justify-content-between">
                                        <div><strong>Pay (৳):</strong></div>
                                        <div> <input type="number" class="form-control" id="calculateRest" min="0"
                                                step="100" @input="calculateRest" /></div>
                                    </div>
                                    <div class="summary-item"><strong>Rest (৳):</strong> <span class="text-info"
                                            id="rest">0.00</span></div>

                                    <div class="mb-2" style="background: #636363; height: 2px;"></div>
                                    <div class="summary-item"><strong class="text-danger">Customer Payable (৳):</strong>
                                        <span class="text-danger" id="customer_payable">0.00</span>
                                    </div>

                                    <div class="mt-3">
                                        <label for="invoiceName" class="form-label">Invoice Name</label>
                                        <input type="text" id="invoiceName" class="form-control" v-model="invoiceName">
                                    </div>

                                    <!-- Generate Invoice -->
                                    <button class="btn btn-info w-100 mt-4" @click.prevent="generateInvoice">
                                        Generate Invoice
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- pick product -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between">
                                    <div>
                                        <h6 class="m-0 font-weight-bold text-info">Pick Product</h6>
                                    </div>
                                    <div>
                                        <input placeholder="Search..." class="form-control w-auto form-control-sm"
                                            type="text" v-model="productSearchValue">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <EasyDataTable buttons-pagination alternating :headers="productHeader"
                                        :items="productItems" border-cell theme-color="#36b9cc" :rows-per-page="10"
                                        :search-field="productSearchField" :search-value="productSearchValue">

                                        <template #item-image="{ image }">
                                            <img :src="image ? `/storage/${image}` : '/asset/img/placeholder.jpg'"
                                                alt="Product Image"
                                                style="width: 50px; height: 50px; object-fit: cover;" class="p-1">
                                        </template>


                                        <template #item-action="{ id }">
                                            <button class="btn btn-sm btn-outline-success" @click="pickProduct(id)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </template>
                                    </EasyDataTable>
                                </div>
                            </div>




                        </div>

                        <!-- pick customer -->
                        <div class="col-lg-4">



                            <SupplierTableComponent :suppliers="customers" supplier-label="Pic Customer" @pick="pickCustomer" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>



    <!-- product customization modal -->
    <div class="modal fade" id="productCustomization" tabindex="-1" aria-labelledby="productCustomizationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Product for invoice</h5>
                </div>
                <div class="modal-body">
                    <!-- <form id="invoiceForm" @submit.prevent="addProductToInvlist()"> -->
                    <form id="invoiceForm">
                        <div class="mb-3 stock-expiry">
                            <label class="form-label abel-fixed">Name</label>
                            <input type="text" class="form-control input-flex" :value="selectedProduct.products?.name || ''"
                            placeholder="Product Name" readonly />
                        </div>

                        <div class="mb-3 stock-expiry">
                            <label class="form-label abel-fixed">Purchase price</label>
                            <input type="number" class="form-control input-flex" v-model="selectedProduct.purchase_price"
                                placeholder="Product origingal price" readonly />
                        </div>

                        <div class="mb-3 stock-expiry">
                            <label class="form-label label-fixed">Sale price</label>
                            <input type="number" class="form-control input-flex" v-model.number="selectedProduct.sale_price"

                              placeholder="Sale price" />
                        </div>

                        <div class="mb-3">
                            <div class="stock-expiry">
                                    <label class="form-label label-fixed">Quantity</label>
                                    <input type="number" class="form-control input-flex" v-model="selectedProduct.qty"
                                    @input="checkQty"
                                    min="1"
                                    placeholder="Quantity" />
                            </div>




                               <div class="stock-expiry">
                                    <p>Available Stock: {{ selectedProduct.stock_qty }}</p>
                                    <p>Expiry Date: {{ selectedProduct.expiry_date }}</p>
                               </div>

                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <button id="productCustomizationBtnCls" type="button" class="btn btn-outline-info mr-3"
                                    data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-info" @click="addProductToInvlist($event)">Add


                                </button>
                                <!-- <button type="submit" class="btn btn-info">Add


                                </button> -->
                            </div>
                            <div>
                                <button type="submit" class="btn btn-info" @click="demageProduct($event)">demage



                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>

<style scoped>
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 5px 0;
    font-size: 16px;
}

.summary-item strong {
    padding-right: 20px;
}

#invoiceTable th,
#invoiceTable td {
    padding-top: 2px;
    padding-bottom: 2px;
    vertical-align: middle;
}

#invoiceTable td:first-child,
#invoiceTable th:first-child {
    width: 70%;
    white-space: normal;
}

.stock-expiry {
  display: flex;
  justify-content: space-between;
}




.label-fixed {
  width: 100px;
}

.input-flex {
  width: 300px;
}

</style>




