<template>
  <div>
    <CategoryNode
      v-for="cat in categories"
      :key="cat.id"
      :category="cat"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
  </div>
</template>

<script setup lang="ts">
import CategoryNode from './CategoryNode.vue'

defineProps({
  categories: { type: Array, required: true }
})

defineEmits(['edit', 'delete'])
</script>
