<template>
  <div>
    <!-- Create Category Button -->
    <div class="flex justify-end mb-6">
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        @click="openCreate"
      >
        + Create Category
      </button>
    </div>

    <!-- Tree Component -->
    <div class="bg-white rounded-lg shadow p-4">
      <CategoryTree
        :categories="categories"
        @edit="openEdit"
        @delete="confirmDelete"
      />
    </div>

    <!-- Delete Confirmation -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center"
    >
      <div class="bg-white rounded p-6 w-80">
        <ConfirmDelete
          :itemName="deleteItem?.name"
          @cancel="showDeleteModal = false"
          @confirm="deleteCategory"
        />
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showForm"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center"
    >
      <div class="bg-white rounded-lg w-full max-w-lg">
        <CategoryDialog
          :mode="mode"
          :categories="categories"
          :categoryData="selected"
          @created="reload"
          @updated="reload"
          @close="closeForm"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

import CategoryTree from '@/views/Admin/Categories/CategoryTree.vue'
import CategoryDialog from '@/views/Admin/Categories/CategoryDialog.vue'
import ConfirmDelete from '@/views/CMN/ConfirmDelete.vue'
import { useToast } from '@/views/CMN/useToast'

const { showToast } = useToast()

const categories = ref([])
const showForm = ref(false)
const showDeleteModal = ref(false)
const mode = ref<'create' | 'edit'>('create')
const selected = ref<any>(null)
const deleteItem = ref<any>(null)

async function loadCategories() {
  const { data } = await axios.get('/categories')
  categories.value = data
}

function openCreate() {
  mode.value = 'create'
  selected.value = null
  showForm.value = true
}

function openEdit(cat) {
  mode.value = 'edit'
  selected.value = cat
  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

function confirmDelete(cat) {
  deleteItem.value = cat
  showDeleteModal.value = true
}

async function deleteCategory() {
  await axios.delete(`/categories/${deleteItem.value.id}`)
  showDeleteModal.value = false
  loadCategories()
  showToast("Category deleted", "success")
}

function reload() {
  loadCategories()
  showToast("Saved successfully", "success")
}

onMounted(() => {
  loadCategories()
})
</script>
