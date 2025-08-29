<!-- SidebarDropdown.vue -->
<template>
  <li class="nav-item" :class="{ active: activeUrls.includes($page.url) }">
    <a
      class="nav-link collapsed"
      href="#"
      data-toggle="collapse"
      :data-target="`#${collapseId}`"
      aria-expanded="true"
      :aria-controls="collapseId"
    >
      <i :class="['icon', icon]"></i>
      <span>{{ title }}</span>
    </a>

    <div
      class="collapse"
      :id="collapseId"
      aria-labelledby="headingTwo"
      data-parent="#accordionSidebar"
    >
      <div class="bg-white py-2 collapse-inner rounded">
        <Link
          v-for="link in links"
          :key="link.href"
          class="collapse-item"
          :class="{ active: $page.url === link.href }"
          :href="link.href"
        >
          {{ link.label }}
        </Link>
      </div>
    </div>
  </li>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  title: String,
  icon: String,
  collapseId: String,
  activeUrls: Array,
  links: Array, // [{ label: '...', href: '...' }]
});
</script>
<style>
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
