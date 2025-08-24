<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const categories = computed(() => page.props.categories.data || []);
const links = computed(() => page.props.categories.links || []);
const categoryCount = page.props.categoryCount || 0;
</script>

<template>
    <div id="content-wrapper" class="d-flex flex-column">
        <div class="container-fluid">
            <div class="row mb-3">
                <!-- bradecrumb -->
                <div class="col-12">
                    <div class="shadow my-3 p-3 d-flex justify-content-between align-item-center">
                        <div>
                            <h5 class="text-info font-weight-bold"> All Categories </h5>
                        </div>
                        <div>
                            <h5 class="text-info font-weight-bold">Total ({{ categoryCount }}) categories</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Categories -->
                <div class="col-xl-3 col-md-6 mb-4" v-for="category in categories" :key="category.id">
                    <Link style="text-decoration: none;" :href="route('category.by.id', {id: category.id})">
                    <div class="card h-100">
                        <div>
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2 p-3">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        {{ category.name }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold">{{ category.products_count ?? 0 }}</div>
                                </div>
                                <div class="col">
                                    <img :src="category.image ? `/storage/category/${category.image}` : '/asset/img/placeholder.jpg'" class="img-fluid rounded-start"
                                        alt="Product image" style="object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                    </Link>
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

<style scoped></style>
