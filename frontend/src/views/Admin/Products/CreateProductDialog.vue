<template>
  <!-- Background -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <!-- MODAL -->
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-lg shadow-lg overflow-y-auto p-6">

      <!-- Title -->
      <h2 class="text-xl font-semibold mb-4">
        {{ mode === "edit" ? "Edit Product" : "Create Product" }}
      </h2>

      <!-- FORM -->
      <div class="space-y-4">

        <!-- SKU -->
        <div>
          <label class="text-sm font-medium">SKU</label>
          <input
            v-model="form.sku"
            type="text"
            :readonly="mode === 'edit'"
            class="w-full border px-3 py-2 rounded"
          />
        </div>

        <!-- Name -->
        <div>
          <label class="text-sm font-medium">Product Name *</label>
          <input
            v-model="form.name"
            type="text"
            :class="inputClass('name')"
          />
          <p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</p>
        </div>

        <!-- Description -->
        <div>
          <label class="text-sm font-medium">Description</label>
          <textarea v-model="form.description" class="w-full border px-3 py-2 rounded" rows="4"></textarea>
        </div>

        <!-- Price + Stock -->
        <div class="grid grid-cols-2 gap-4">

          <div>
            <label class="text-sm font-medium">Price *</label>
            <input
              v-model="form.price"
              type="number"
              :class="inputClass('price')"
            />
            <p v-if="errors.price" class="text-red-600 text-sm mt-1">{{ errors.price }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Stock *</label>
            <input
              v-model="form.stock"
              type="number"
              :class="inputClass('stock')"
            />
            <p v-if="errors.stock" class="text-red-600 text-sm mt-1">{{ errors.stock }}</p>
          </div>

        </div>

        <!-- Unit -->
        <div>
          <label class="text-sm font-medium">Unit</label>
          <select v-model="form.unit" class="w-full border px-3 py-2 rounded">
            <option value="">-- Select Unit --</option>
            <option v-for="u in units" :key="u.value" :value="u.value">
              {{ u.label }}
            </option>
          </select>
        </div>

        <!-- Category -->
        <div>
          <label class="text-sm font-medium">Category *</label>

          <select v-model="form.category_id" :class="inputClass('category_id')">
            <option value="">-- Select Category --</option>

            <template v-for="parent in categories" :key="parent.id">
              <option :value="parent.id">{{ parent.name }}</option>

              <template v-for="child in parent.children" :key="child.id">
                <option :value="child.id">— {{ child.name }}</option>

                <template v-for="sub in child.children" :key="sub.id">
                  <option :value="sub.id">—— {{ sub.name }}</option>
                </template>
              </template>
            </template>
          </select>

          <p v-if="errors.category_id" class="text-red-600 text-sm mt-1">{{ errors.category_id }}</p>
        </div>

        <!-- Image -->
        <div>
          <label class="text-sm font-medium">Product Image</label>
          <input
            type="file"
            accept="image/*"
            @change="onImagePreview"
            class="w-full border px-3 py-2 rounded"
          />
        </div>

        <!-- Preview -->
        <div v-if="imagePreview" class="mt-2 text-center">
          <img :src="imagePreview" class="max-h-40 mx-auto rounded" />
        </div>

        <!-- Published -->
        <div>
          <label class="block font-medium mb-1">Published</label>
          <select v-model.number="form.published" class="w-full px-3 py-2 border rounded">
            <option :value="1">Published</option>
            <option :value="0">Unpublished</option>
          </select>
        </div>

      </div>

      <!-- ACTION BUTTONS -->
      <div class="flex justify-end gap-3 mt-6">
        <button class="px-4 py-2 bg-gray-300 rounded" @click="close">Cancel</button>

        <button
          class="px-4 py-2 bg-blue-600 text-white rounded"
          @click="mode === 'edit' ? updateProduct() : saveProduct()"
        >
          {{ mode === "edit" ? "Update" : "Save" }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import axios from "axios"
import { useToast } from "@/views/CMN/useToast"
import units from "@/data/units.js"

const { showToast } = useToast()
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

const props = defineProps({
  mode: String,
  productData: Object,
  categories: Array
})

const emit = defineEmits(["created", "updated", "close"])

const form = ref({
  sku: "",
  name: "",
  description: "",
  price: "",
  unit: "kg",
  image: null,
  stock: "",
  category_id: "",
  published: 1
})

const imagePreview = ref(null)
const errors = ref({})

/* ---------------------- Input error class ---------------------- */
function inputClass(field: string) {
  return [
    "w-full border px-3 py-2 rounded",
    errors.value[field] ? "border-red-500 bg-red-50" : "border-gray-300"
  ]
}

/* ---------------------- Frontend validation ---------------------- */
function validateForm() {
  errors.value = {}

  if (!form.value.name) errors.value.name = "Product name is required"
  if (!form.value.price) errors.value.price = "Price is required"
  if (!form.value.stock && form.value.stock !== 0) errors.value.stock = "Stock is required"
  if (!form.value.category_id) errors.value.category_id = "Category is required"

  return Object.keys(errors.value).length === 0
}

/* ---------------------- Load edit data ---------------------- */
onMounted(() => {
  if (props.mode === "edit" && props.productData) {
    form.value = { ...props.productData }
    imagePreview.value = form.value.image ? API_BASE_URL + form.value.image : null
  } else {
    generateSKU()
  }
})

function generateSKU() {
  form.value.sku = "SKU-" + Math.random().toString(36).substring(2, 10).toUpperCase()
}

/* ---------------------- Image preview ---------------------- */
function onImagePreview(e) {
  const file = e.target.files[0]
  form.value.image = file
  if (file) imagePreview.value = URL.createObjectURL(file)
}

/* ---------------------- Backend error handler ---------------------- */
function handleApiError(err) {
  if (err.response?.status === 422) {
    errors.value = Object.fromEntries(
      Object.entries(err.response.data.errors).map(([k, v]) => [k, v[0]])
    )
    showToast("Please fix highlighted errors", "error")
  } else {
    showToast("Server error occurred", "error")
  }
}

/* ---------------------- CREATE ---------------------- */
async function saveProduct() {
  if (!validateForm()) return

  const fd = new FormData()
  Object.keys(form.value).forEach(k => fd.append(k, form.value[k]))

  if (form.value.image instanceof File) fd.append("image", form.value.image)

  try {
    await axios.post("/products", fd)
    showToast("Product created successfully", "success")
    emit("created")
    close()
  } catch (err) {
    handleApiError(err)
  }
}

/* ---------------------- UPDATE ---------------------- */
async function updateProduct() {
  if (!validateForm()) return

  const fd = new FormData()

  Object.keys(form.value).forEach(k => {
    if (k !== "image") fd.append(k, form.value[k])
  })

  if (form.value.image instanceof File) {
    fd.append("image", form.value.image)
  } else {
    fd.append("existing_image", form.value.image || "")
  }

  fd.append("_method", "PUT")

  try {
    await axios.post(`/products/${form.value.id}`, fd)
    showToast("Product updated successfully", "success")
    emit("updated")
    close()
  } catch (err) {
    handleApiError(err)
  }
}

function close() {
  emit("close")
}
</script>
