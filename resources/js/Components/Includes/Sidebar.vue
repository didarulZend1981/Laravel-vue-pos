<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const isSidebarToggled = ref(false);

const toggleSidebar = () => {
    isSidebarToggled.value = !isSidebarToggled.value;
    document.body.classList.toggle('sidebar-toggled');
    document.querySelector('.sidebar').classList.toggle('toggled');

    if (isSidebarToggled.value) {
        const collapses = document.querySelectorAll('.sidebar .collapse');
        collapses.forEach(collapse => {
            new bootstrap.Collapse(collapse, { toggle: false }).hide();
        });
    }
};

const handleResize = () => {
    const width = window.innerWidth;
    const sidebar = document.querySelector('.sidebar');

    if (width < 768) {
        const collapses = document.querySelectorAll('.sidebar .collapse');
        collapses.forEach(collapse => {
            new bootstrap.Collapse(collapse, { toggle: false }).hide();
        });
    }

    if (width < 480 && !sidebar.classList.contains('toggled')) {
        document.body.classList.add('sidebar-toggled');
        sidebar.classList.add('toggled');
        const collapses = document.querySelectorAll('.sidebar .collapse');
        collapses.forEach(collapse => {
            new bootstrap.Collapse(collapse, { toggle: false }).hide();
        });
    }
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
    handleResize(); // Initialize on mount
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <ul class="navbar-nav bg-info sidebar sidebar-dark accordion" :class="{ 'toggled': isSidebarToggled }"
        id="accordionSidebar">
        <Link class="sidebar-brand d-flex align-items-center justify-content-center" :href="route('show.dashboard')">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa fa-calculator"></i>
        </div>

        <!-- logo -->
        <div class="sidebar-brand-text mx-3"> POS </div>
        </Link>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Dashboard -->
        <li class="nav-item" :class="{ 'active': $page.url === '/dashboard' }">
            <Link class="nav-link" href="/dashboard">
            <i class="fas fa-fw fa-tachometer-alt icon"></i>
            <span>Dashboard</span></Link>
        </li>

        <!-- cutomer -->
        <li class="nav-item" :class="{ 'active': ['/customer', '/customer/save'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
                <i class="fa fa-users icon"></i>
                <span>Customer</span>
            </a>
            <div id="collapseOne" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/customer' }" href="/customer">
                    Customer List</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/customer/save' }"
                        href="/customer/save"> Customer Save </Link>
                </div>
            </div>
        </li>

        <!-- supplier -->
        <li class="nav-item" :class="{ 'active': ['/supplier', '/supplier/save'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                aria-expanded="true" aria-controls="collapseTwo">
                <i class="fa fa-user-plus icon"></i>
                <span>Supplier</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/supplier' }" href="/supplier">
                    Supplier List</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/supplier/save' }"
                        href="/supplier/save"> Supplier Save </Link>
                </div>
            </div>
        </li>

        <!-- category -->
        <li class="nav-item"
            :class="{ 'active': ['/category', '/category/save', '/category/all'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                aria-expanded="true" aria-controls="collapseThree">
                <i class="fa fa-bars icon"></i>
                <span>Category</span>
            </a>
            <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/category' }" href="/category">
                    Category List</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/category/save' }"
                        href="/category/save">
                    Category Save</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/category/all' }"
                        href="/category/all">
                    All Categories</Link>
                </div>
            </div>
        </li>

        <!-- brand or company -->
        <li class="nav-item" :class="{ 'active': ['/brand', '/brand/save'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour"
                aria-expanded="true" aria-controls="collapseFour">
                <i class="fa fa-bold icon"></i>
                <span>Brand</span>
            </a>
            <div id="collapseFour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/brand' }" href="/brand">
                    Brand List</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/brand/save' }" href="/brand/save">
                    Brand Save</Link>
                </div>
            </div>
        </li>

        <!-- product -->
        <li class="nav-item" :class="{ 'active': ['/product', '/product/save', 'product/all'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFive"
                aria-expanded="true" aria-controls="collapseFive">
                <i class="fa fa-cubes icon"></i>
                <span>Product</span>
            </a>
            <div id="collapseFive" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/product' }" href="/product">
                    Product List</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/product/save' }"
                        href="/product/save">Save
                    Product</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/product/all' }" href="/product/all">
                    All
                    Product</Link>
                </div>
            </div>
        </li>

        <!-- sale and custom sale generate -->
        <li class="nav-item" :class="{ 'active': ['/sale-invoice', '/sale-invoice/custom'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSix"
                aria-expanded="true" aria-controls="collapseSix">
                <i class="fa fa-dollar-sign icon"></i>
                <span>Sale Generate</span>
            </a>
            <div id="collapseSix" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/sale-invoice' }"
                        href="/sale-invoice">
                    Create Sale</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/sale-invoice/custom' }"
                        href="/sale-invoice/custom">
                    Custom Sale</Link>
                </div>
            </div>
        </li>

        <!-- Purchase and custom purchase generate -->
        <li class="nav-item"
            :class="{ 'active': ['/purchase-invoice', '/purchase-invoice/custom'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSeven"
                aria-expanded="true" aria-controls="collapseSeven">
                <i class="fa fa-shopping-cart icon"></i>
                <span>Purchase</span>
            </a>
            <div id="collapseSeven" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/purchase-invoice' }"
                        href="/purchase-invoice">
                    Create purchase</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/purchase-invoice/custom' }"
                        href="/purchase-invoice/custom">
                    Custom purchase</Link>
                </div>
            </div>
        </li>

        <!-- invoice -->
        <li class="nav-item"
            :class="{ 'active': ['/sale-invoice/list', '/purchase-invoice/list'].includes($page.url) }">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEight"
                aria-expanded="true" aria-controls="collapseEight">
                <i class="fa fa-table icon"></i>
                <span>Invoice</span>
            </a>
            <div id="collapseEight" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/sale-invoice/list' }"
                        href="/sale-invoice/list">
                    Sale Invoice</Link>
                    <Link class="collapse-item" :class="{ 'active': $page.url === '/purchase-invoice/list' }"
                        href="/purchase-invoice/list">Purchase Invoice</Link>
                </div>
            </div>
        </li>

        <!-- report -->
        <li class="nav-item" :class="{ 'active': $page.url === '/report' }">
            <Link class="nav-link" href="/report">
            <i class="fa fa-file icon"></i>
            <span>Report</span></Link>
        </li>
    </ul>
</template>

<style scoped>
.icon {
    color: #36b9cc !important;
    background: rgba(255, 255, 255, 0.8);
    padding: 8px;
    border-radius: 50%;
    margin-right: 8px;
    font-size: 18px;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}
</style>
