<template>
  <div class="p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
      Confirm Delete
    </h2>

    <p class="text-gray-600 mb-6">
      Are you sure you want to delete 
      <span class="font-semibold text-red-600">{{ itemName }}</span>?
      This action cannot be undone.
    </p>

    <div class="flex justify-end gap-3">
      <button
        @click="$emit('cancel')"
        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
      >
        Cancel
      </button>

      <button 
        @click="$emit('confirm')"
        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
      >
        Yes, Delete
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  itemName: { type: String, default: 'this item' }
})
</script>
