<template>
  <div class="flex flex-wrap justify-center items-center gap-1 py-4">
    <div class="text-gray-600 text-sm p-3">
        Showing {{ from }} to {{ to }} of {{ total }}
    </div>

    <!-- Prev Button -->
    <button
      :disabled="currentPage === 1"
      @click="$emit('change', currentPage - 1)"
      class="px-3 py-2 border rounded disabled:opacity-50"
    >
      Prev
    </button>

    <!-- First Page -->
    <button
      @click="$emit('change', 1)"
      class="px-3 py-1 border rounded"
      :class="currentPage === 1 ? 'bg-blue-600 text-white' : ''"
    >
      1
    </button>

    <!-- Left Ellipsis -->
    <span v-if="currentPage > 4" class="px-2">...</span>

    <!-- Middle Page Numbers -->
    <button
      v-for="n in middlePages"
      :key="n"
      @click="$emit('change', n)"
      class="px-3 py-1 border rounded"
      :class="currentPage === n ? 'bg-blue-600 text-white' : ''"
    >
      {{ n }}
    </button>

    <!-- Right Ellipsis -->
    <span v-if="currentPage < lastPage - 3" class="px-2">...</span>

    <!-- Last Page -->
    <button
      v-if="lastPage > 1"
      @click="$emit('change', lastPage)"
      class="px-3 py-1 border rounded"
      :class="currentPage === lastPage ? 'bg-blue-600 text-white' : ''"
    >
      {{ lastPage }}
    </button>

    <!-- Next Button -->
    <button
      :disabled="currentPage === lastPage"
      @click="$emit('change', currentPage + 1)"
      class="px-3 py-2 border rounded disabled:opacity-50"
    >
      Next
    </button>

    <!-- Per Page Dropdown -->
    <select
      v-model.number="localPerPage"
      @change="updatePerPage"
      class="ml-4 px-3 py-2 border rounded"
    >
      <option v-for="n in perPageOptions" :key="n" :value="n">
        {{ n }} / page
      </option>
    </select>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  currentPage: Number,
  lastPage: Number,
  perPage: Number,
  from: Number,
  to: Number,
  total: Number,
  perPageOptions: {
    type: Array,
    default: () => [5, 10, 20, 50, 100]
  }
});

const emit = defineEmits(["change", "update:perPage"]);

const localPerPage = ref(props.perPage);

// Update per page
function updatePerPage() {
  emit("update:perPage", localPerPage.value);
}

// Generate middle pages dynamically
const middlePages = computed(() => {
  const pages = [];
  const cur = props.currentPage;
  const last = props.lastPage;

  const start = Math.max(2, cur - 2);
  const end = Math.min(last - 1, cur + 2);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});
</script>
