<template>
  <transition name="slide-fade">
    <div
      v-if="visible"
      class="fixed top-5 right-5 px-4 py-3 rounded shadow-lg text-white z-50 min-w-[200px]"
      :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
    >
      {{ message }}
    </div>
  </transition>
</template>

<script setup lang="ts">
import { useToast } from '@/views/CMN/useToast'

const { visible, message, type } = useToast()
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.4s ease;
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
