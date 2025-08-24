<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const products = computed(() => page.props.products.data || []);
const links = computed(() => page.props.products.links || []);
const productCount = page.props.productCount || 0;
</script>

<template>
    <div id="content-wrapper" class="d-flex flex-column">
        <div class="container-fluid">
            <div class="row mb-3">
                <!-- bradecrumb -->
                <div class="col-12">
                    <div class="shadow my-3 p-3 d-flex justify-content-between align-item-center">
                        <div>
                            <h5 class="text-info font-weight-bold">All stockout products</h5>
                        </div>
                        <div>
                            <h5 class="text-info font-weight-bold">Total lowest stock's ({{ productCount }}) products</h5>
                        </div>
                    </div>
                </div>

                <!-- product cart for loop -->
                <div class="col-md-4 mb-4" v-for="product in products" :key="product?.id">
                    <div class="card h-100">
                        <div class="row g-0 h-100">
                            <div class="col-4">
                                <img :src="product?.image ? `/storage/${product?.image}` : '/asset/img/placeholder.jpg'"
                                    class="img-fluid rounded-start w-100 h-100" alt="Product image"
                                    style="object-fit: cover;">
                            </div>
                            <div class="col-8">
                                <div class="card-body py-2 px-3">
                                    <p class="mb-1" style="line-height: 1.4;"><strong>Product Name:</strong> <span
                                            class="text-info">{{
                                                product?.name }}</span>
                                    </p>
                                    <p class="mb-1" style="line-height: 1.4;"><strong>Price:</strong> <span
                                            class="text-info">{{ product?.price }}</span> </p>
                                    <p class="mb-1" style="line-height: 1.4;"><strong>Unit:</strong> <span
                                            class="text-info">{{ product?.unit }}</span> </p>
                                    <p class="mb-1" style="line-height: 1.4;"><strong>Stock Qty:</strong> <span
                                            class="text-info">{{ product?.stock }}</span> </p>
                                    <p class="mb-1" style="line-height: 1.4;"><strong>Brand:</strong> <span
                                            class="text-info">{{ product?.brand?.name }}</span> </p>
                                    <p class="mb-0" style="line-height: 1.4;"><strong>Category:</strong> <span
                                            class="text-info">{{ product?.category?.name }}</span> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pb-5">
        <nav class="d-pagination" aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li v-for="(link, index) in links" :key="index" class="page-item"
                    :class="{ active: link.active, disabled: !link.url }">
                    <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" preserve-scroll>
                    </Link>
                    <span v-else class="page-link" v-html="link.label"></span>
                </li>
            </ul>
        </nav>
    </div>
</template>

