<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { getToday } from '@/common.js';
import { router } from '@inertiajs/core';

const props = defineProps({
  customers:Array,
  items:Array
})

const form = reactive({
  date: null,
  customer_id: null,
  status: true,
  items: []
})

const itemList = ref([])

const totalPrice = computed(() => {
  let total = 0
  itemList.value.forEach( item => {
    total += item.price * item.quantity
  })
  return total
})

onMounted(() => {
  form.date = getToday()
  props.items.forEach( item => {
    itemList.value.push({
      id: item.id,
      name: item.name,
      price: item.price,
      quantity: 0
    })
  })
})

const purchaseStore = () => {
  itemList.value.forEach( item => {
    if(item.quantity > 0){
      form.items.push({
        id: item.id,
        quantity: item.quantity
      })
    }
  })
  router.post(route('purchases.store'), form)
}

const quantity = ["0","1","2","3","4","5","6","7","8","9"]

</script>

<template>
  <form @submit.prevent="purchaseStore">
  日付<br>
  <input type="date" name="date" v-model="form.date"><br>

  会員名<br>
  <select name="customer" v-model="form.customer_id">
    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
      {{ customer.id }} : {{ customer.name }}
    </option>
  </select><br>

  商品・サービス<br>
  <thead>
    <tr>
      <th>Id</th>
      <th>商品名</th>
      <th>金額</th>
      <th>数量</th>
      <th>小計</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="item in itemList" :key="item.id">
      <td>{{ item.id }}</td>
      <td>{{ item.name }}</td>
      <td>{{ item.price }}</td>
      <td>
        <select name="quantity" v-model="item.quantity">
          <option v-for="q in quantity" :value="q" :key="q">{{ q }}</option>
        </select>
      </td>
      <td>{{ item.price *  item.quantity}}</td>
    </tr>
  </tbody><br>
  合計:{{ totalPrice }}円
  <button>登録する</button>
</form>
</template>