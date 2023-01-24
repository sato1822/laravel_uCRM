
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
// import { onMounted } from '@vue/runtime-core';
import Pagination from '@/Components/Pagination.vue'
import { onMounted } from '@vue/runtime-core';

defineProps({
  customers: Object
})

// onMounted(() => {
//   console.log(props.customers)
// })

</script>

<template>
  <div>
    <ul v-for="customer in customers.data" :key="customer.id">
    {{ customer.id }}
    {{ customer.name }}
    </ul>
    <Pagination class="mt-6" :links="customers.links"></Pagination>
  </div>
</template>
