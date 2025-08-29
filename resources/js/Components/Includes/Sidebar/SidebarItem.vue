<template>
  <li class="nav-item" :class="{ active: $page.url === href }">
    <Link class="nav-link" :href="href">
      <i :class="['icon', icon]"></i>
      <span>{{ label }}</span>
    </Link>
  </li>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  label: String,     // যেমন: "Dashboard"
  icon: String,      // যেমন: "fas fa-fw fa-tachometer-alt"
  href: String       // যেমন: "/dashboard"
});
</script>

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
