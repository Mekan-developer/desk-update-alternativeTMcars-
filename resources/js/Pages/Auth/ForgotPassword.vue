<script setup>
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

onMounted(() => router.visit(route('login'), { replace: true }))
</script>

<template>
    <div></div>
</template>
