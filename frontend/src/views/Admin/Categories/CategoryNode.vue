<template>
  <div class="pl-4 border-l border-gray-300">

    <!-- Category Row -->
    <div
      class="flex items-center justify-between py-2 px-2 rounded-md cursor-pointer 
             hover:bg-gray-100 transition"
    >
      <div class="flex items-center gap-2">
        
        <!-- Expand/Collapse -->
        <button
          v-if="category.children?.length"
          class="text-gray-500 hover:text-gray-700"
          @click="expanded = !expanded"
        >
          <span v-if="expanded">➖</span>
          <span v-else>➕</span>
        </button>

        <!-- Category Name -->
        <span class="font-medium">{{ category.name }}</span>
      </div>

      <!-- Actions -->
      <div>
        <button class="text-blue-500 mr-2 hover:underline" @click="$emit('edit', category)">
          Edit
        </button>
        <button class="text-red-500 hover:underline" @click="$emit('delete', category)">
          Delete
        </button>
      </div>
    </div>

    <!-- Children -->
    <div v-if="expanded" class="ml-6">
      <CategoryNode
        v-for="child in category.children"
        :key="child.id"
        :category="child"
        @edit="$emit('edit', $event)"
        @delete="$emit('delete', $event)"
      />
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import CategoryNode from './CategoryNode.vue'

const props = defineProps({
  category: { type: Object, required: true }
})

const expanded = ref(true)
</script>
