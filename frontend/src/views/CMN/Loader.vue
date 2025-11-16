<template>
  <div class="flex justify-center items-center py-10">
    <div class="loader"></div>
  </div>
</template>

<style scoped>
.loader {
  width: 40px;
  height: 40px;
  border: 4px solid #cbd5e1;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
