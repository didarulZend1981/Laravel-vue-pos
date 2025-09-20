<script setup>

// 1=admin,2=sale,3=purchese
import { ref, onMounted, onUnmounted } from 'vue';
import { Link,useForm } from '@inertiajs/vue3';
import SidebarDropdown from './Sidebar/SidebarDropdown.vue';
import SidebarItem from './Sidebar/SidebarItem.vue';

const isSidebarToggled = ref(false);

const profileData = useForm({
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    address: '',
    // image: '',
});

async function fetchProfileData() {
    try {
        const response = await axios.get(route('get.profile'));
        // console.log(response)
        if (response.data.status === 'success') {
            // profileData.value = response.data.user;
            Object.assign(profileData, response.data.user);
        }
    } catch (error) {
        errorToast('Error fetching profile !!');
    }
}

onMounted(() => {
    fetchProfileData();
});




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
        <div class="sidebar-brand-text mx-3"> POS



        </div>
        </Link>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Dashboard -->


        <SidebarItem
        v-if="profileData.role == 1|| profileData.role == 2|| profileData.role == 3"
        label="Dashboard"
        icon="fas fa-fw fa-tachometer-alt icon"
        href="/dashboard"
        />



        <!-- brand or company -->

            <SidebarDropdown
                    v-if="profileData.role == 1"
                    title="User"
                    icon="fa fa-bold icon"
                    collapseId="collapseFour"
                    :activeUrls="['/user']"
                    :links="[
                    { label: 'user List', href: '/user' }

                    ]"
           />

        <!-- cutomer -->


        <!-- admin profileData.role==1-->

         <SidebarDropdown
            v-if="profileData.role == 1 || profileData.role == 2"
            title="Customer"
            icon="fa fa-users icon"
            collapseId="collapseOne"
            :activeUrls="['/customer', '/customer/save']"
            :links="[
            { label: 'Customer List', href: '/customer' },
            { label: 'Customer Save', href: '/customer/save' },
             ]"
        />




        <!-- supplier -->

          <SidebarDropdown
            v-if="profileData.role == 1 || profileData.role == 3"
            title="Supplier"
            icon="fa fa-user-plus icon"
            collapseId="collapseTwo"
            :activeUrls="['/supplier', '/supplier/save']"
            :links="[
            { label: 'supplier List', href: '/supplier' },
            { label: 'Supplier Save', href: '/supplier/save' }

            ]"
        />

        <!-- Category Dropdown -->









        <SidebarDropdown
            v-if="profileData.role == 1 || profileData.role == 3"
            title="Category"
            icon="fa fa-bars"
            collapseId="collapseThree"
            :activeUrls="['/category', '/category/save', '/category/all']"
            :links="[
            { label: 'Category List', href: '/category' },
            { label: 'Category Save', href: '/category/save' },
            { label: 'All Categories', href: '/category/all' }
            ]"
        />

        <!-- brand or company -->

            <SidebarDropdown
                    v-if="profileData.role == 1 || profileData.role == 3"
                    title="Brand"
                    icon="fa fa-bold icon"
                    collapseId="collapseFour"
                    :activeUrls="['/brand', '/brand/save']"
                    :links="[
                    { label: 'Brand List', href: '/brand' },
                    { label: 'Brand Save', href: '/brand/save' }
                    ]"
           />




        <!-- product -->



        <SidebarDropdown
            v-if="profileData.role == 1 || profileData.role == 3"
            title="Product"
            icon="fa fa-cubes icon"
            collapseId="collapseFive"
            :activeUrls="['/product', '/product/save', '/product/all']"
            :links="[
            { label: 'product List', href: '/product' },
            { label: 'Product Save', href: '/product/save' },
            { label: 'All Product', href: '/product/all' }
            ]"
        />


        <!-- sale and custom sale generate -->

        <!-- <SidebarDropdown
            v-if="profileData.role == 1 || profileData.role == 2"
            title="Sale Generate"
            icon="fa fa-dollar-sign icon"
            collapseId="collapseSix"
            :activeUrls="['/sale-invoice']"
            :links="[
            { label: 'Create Sale', href: '/sale-invoice' },
            // { label: 'Custom Sale', href: '/sale-invoice/custom' }

            ]"
        /> -->
        <SidebarItem
            v-if="profileData.role == 1 || profileData.role == 2"
            label="Create Sale"
            icon="fa fa-dollar-sign icon"
            href="/sale-invoice"
        />




        <!-- Purchase and custom purchase generate -->


         <!-- <SidebarDropdown
            v-if="profileData.role == 1"
            title="Purchase"
            icon="fa fa-shopping-cart icon"
            collapseId="collapseSeven"
            :activeUrls="['/purchase-invoice']"
            :links="[
            { label: 'Create purchase', href: '/purchase-invoice' },
            // { label: 'Custom purchase', href: '/sale-invoice/custom' }

            ]"
        /> -->

        <SidebarItem
            v-if="profileData.role == 1"
            label="Purchase"
            icon="fa fa-shopping-cart icon"
            href="/purchase-invoice"
        />

        <SidebarItem
            v-if="profileData.role == 3"
            label="Purchase"
            icon="fa fa-shopping-cart icon"
            href="/purchase-invoice"
        />








        <!-- invoice -->


          <SidebarDropdown
            v-if="profileData.role == 1"
            title="Invoice"
            icon="fa fa-table icon"
            collapseId="collapseEight"
            :activeUrls="['/sale-invoice/list', '/purchase-invoice/list']"
            :links="[
            { label: 'Sale Invoice', href: '/sale-invoice/list' },
            { label: 'Purchase Invoice', href: '/purchase-invoice/list' }

            ]"
        />




        <SidebarItem
            v-if="profileData.role == 2"
            label="Sale Invoice"
            icon="fa fa-table icon"
            href="/sale-invoice/list"
        />






        <!-- report -->

          <SidebarItem
            v-if="profileData.role == 1 || profileData.role == 2 || profileData.role == 3"
            label="Report"
            icon="fa fa-file icon"
            href="/report"
        />






        <SidebarItem
          v-if="profileData.role == 1 || profileData.role == 2 || profileData.role == 3"
        label="Stock"
        icon="fa fa-box icon"
        href="/stock"
        />
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
