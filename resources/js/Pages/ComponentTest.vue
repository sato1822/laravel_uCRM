<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Label from '@/Components/InputLabel.vue';
import Input from '@/Components/TextInput.vue'

const emitTest = e => console.log(e)
</script>
<template>
  <GuestLayout>
    <Label value="aaaaaa">bbbbbbb</Label>
    <Input 
    modelValue="名前を記入してください"
    @update:modelValue="emitTest"
    ></Input>
  </GuestLayout>
</template>