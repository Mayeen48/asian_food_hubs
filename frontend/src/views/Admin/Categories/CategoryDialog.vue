<template>
  <div class="p-6">
    <h2 class="text-xl font-semibold mb-4">
      {{ mode === 'create' ? 'Create Category' : 'Edit Category' }}
    </h2>

    <!-- Category Name -->
    <label class="block font-medium mb-1">Name *</label>
    <input
      v-model="form.name"
      :class="inputClass('name')"
    />
    <p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</p>

    <!-- Description -->
    <label class="block font-medium mt-4 mb-1">Description</label>
    <textarea
      v-model="form.description"
      class="w-full border rounded px-3 py-2"
    ></textarea>

    <!-- Parent Selector -->
    <label class="block font-medium mt-4 mb-1">Parent Category</label>
    <select
      v-model="form.parent_id"
      class="w-full border rounded px-3 py-2"
    >
      <option :value="null">None</option>
      <option
        v-for="c in flatCategories"
        :key="c.id"
        :value="c.id"
      >
        {{ c.label }}
      </option>
    </select>
    <p v-if="errors.parent_id" class="text-red-600 text-sm mt-1">
      {{ errors.parent_id }}
    </p>

    <!-- Buttons -->
    <div class="flex justify-end gap-3 mt-6">
      <button class="px-4 py-2 bg-gray-300 rounded" @click="close">
        Cancel
      </button>

      <button
        v-if="mode === 'create'"
        class="px-4 py-2 bg-green-600 text-white rounded"
        @click="save"
      >
        Save
      </button>

      <button
        v-else
        class="px-4 py-2 bg-blue-600 text-white rounded"
        @click="update"
      >
        Update
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { useToast } from '@/views/CMN/useToast'   // <— Toast composable

const { showToast } = useToast()

const props = defineProps({
  mode: { type: String, default: 'create' }, 
  categories: Array,
  categoryData: { type: Object, default: null }
})

const emit = defineEmits(['created', 'updated', 'close'])

const form = ref({
  name: '',
  description: '',
  parent_id: null
})

const errors = ref<any>({})

// Tailwind input class with validation
const inputClass = (field: string) => {
  return [
    "w-full border rounded px-3 py-2",
    errors.value[field] ? "border-red-500 bg-red-50" : "border-gray-300"
  ]
}

// Flatten nested categories for parent dropdown
const flatCategories = computed(() => {
  const result: any[] = []

  function traverse(list: any[], prefix = '') {
    list.forEach(c => {
      result.push({ id: c.id, label: prefix + c.name })
      if (c.children?.length) {
        traverse(c.children, prefix + '-- ')
      }
    })
  }

  traverse(props.categories)
  return result
})

watch(
  () => props.categoryData,
  (data) => {
    if (props.mode === 'edit' && data) {
      form.value = {
        name: data.name,
        description: data.description,
        parent_id: data.parent_id
      }
    }
  },
  { immediate: true }
)

function validate() {
  errors.value = {}

  if (!form.value.name.trim()) {
    errors.value.name = "Category name is required"
  }

  if (props.mode === "edit" && form.value.parent_id === props.categoryData?.id) {
    errors.value.parent_id = "A category cannot be its own parent"
  }

  return Object.keys(errors.value).length === 0
}

function close() {
  form.value = { name: '', description: '', parent_id: null }
  errors.value = {}
  emit('close')
}

async function save() {
  if (!validate()) return

  try {
    await axios.post('/categories', form.value)
    emit('created')
    close()
  } catch (err: any) {
    showToast("Failed to create category", "error")
  }
}

async function update() {
  if (!validate()) return

  try {
    await axios.put(`/categories/${props.categoryData.id}`, form.value)
    emit('updated')
    close()
  } catch (err: any) {
    showToast("Failed to update category", "error")
  }
}
</script>
