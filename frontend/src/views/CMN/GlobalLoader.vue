<template>
  <div
    v-if="loading"
    class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-[9999]"
  >
    <div class="loader"></div>
  </div>
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import { useLoaderStore } from "@/stores/loader";

const { loading } = storeToRefs(useLoaderStore());
</script>

<style scoped>
.loader {
  width: 60px;
  height: 60px;
  border: 6px solid #eee;
  border-top-color: #3490dc;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
