<script setup>
import { Chart, registerables } from 'chart.js';
import { BarChart } from 'vue-chart-3';
import { computed, onMounted, reactive, ref } from 'vue';

const props = defineProps({
  date: Object
})

const labels = computed(() => props.date.labels)
const totals = computed(() => props.date.totals)

const dataList = ref([]);

Chart.register(...registerables);

const barData = reactive({
  labels: labels,
  datasets: [
    {
      labels: '売上',
      data: totals,
      backgroundColor: "rgb(75, 192, 192)",
      tension: 0.1
    }
  ]
})
</script>
<template>
  <div>
    <BarChart :chartData="barData" />
  </div>
</template>