<template>
  <div>
    <!-- Top Bar (Search + Create Button + Per Page) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3">

      <!-- Search -->
      <input
        v-model="search"
        @input="debouncedSearch()"
        type="text"
        placeholder="Search products..."
        class="border px-4 py-2 rounded w-full md:w-1/3 shadow-sm"
      />

      <!-- Create Btn -->
      <button 
        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"
        @click="showProductForm = true"
      >
        + Create Product
      </button>
    </div>

    <!-- CREATE MODAL -->
    <div 
      v-if="showProductForm"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto p-4">
        <CreateProductDialog
          mode="create"
          :categories="categories"
          @created="reload"
          @close="showProductForm = false"
        />
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div 
      v-if="editProductForm"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto p-4">
        <CreateProductDialog
          mode="edit"
          :categories="categories"
          :productData="selectedProduct"
          @updated="reload"
          @close="editProductForm = false"
        />
      </div>
    </div>

    <!-- Product Table -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Products</h2>
      </div>

      <div class="overflow-x-auto">
        <div v-if="loading" class="p-6 text-center text-gray-500">Loading...</div>

        <table v-else class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('id')">
              SL
              <span v-if="sortBy === 'id'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>

            <th class="px-4 py-3 whitespace-nowrap">Image</th>

            <!-- NAME -->
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('name')">
              Name
              <span v-if="sortBy === 'name'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>

            <!-- PRICE -->
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('price')">
              Price
              <span v-if="sortBy === 'price'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>

            <!-- CATEGORY -->
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('category_id')">
              Category
              <span v-if="sortBy === 'category_id'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>

            <!-- ACTIONS -->
            <th class="px-4 py-3 whitespace-nowrap">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="p in products.data" :key="p.id">
              <td class="px-6 py-4">{{ p.id }}</td>

              <td class="px-6 py-4">
                <img 
                  :src="p.image ? p.image.replace('/storage', `${API_BASE_URL}/storage`) : placeholder" 
                  class="w-20 h-16 object-cover rounded"
                />
              </td>

              <td class="px-6 py-4">{{ p.name }}</td>
              <td class="px-6 py-4">${{ p.price }}</td>

              <td class="px-6 py-4">
                {{ p.category?.parent?.name ? p.category.parent.name + " / " + p.category.name : p.category?.name }}
              </td>

              <td class="px-6 py-4">
                <button @click="editProduct(p)" class="text-blue-500 hover:underline">
                  Edit
                </button>
                <button @click="askDelete(p)" class="text-red-500 hover:underline ml-2">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- DELETE CONFIRMATION -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <ConfirmDelete
          :itemName="deleteItem?.name"
          @confirm="deleteProduct"
          @cancel="showDeleteModal = false"
        />
      </div>
    </div>
    <!-- Pagination -->
    <Pagination
          v-if="products.data.length"
          :currentPage="products.current_page"
          :lastPage="products.last_page"
          :perPage="perPage"
          :perPageOptions="[5,10,20,50,100]"
          :from="products.from"
          :to="products.to"
          :total="products.total"
          @change="changePage"
          @update:perPage="updatePerPage"
        />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import axios from "axios"
import CreateProductDialog from "@/views/Admin/Products/CreateProductDialog.vue"
import ConfirmDelete from "@/views/CMN/ConfirmDelete.vue"
import { useToast } from "@/views/CMN/useToast"
import Pagination from "@/views/CMN/Pagination.vue";

const { showToast } = useToast()
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

const products = ref({ data: [] })
const categories = ref([])

const search = ref("")
const page = ref(1)
const perPage = ref(10)
const loading = ref(false)
const sortBy = ref("id");
const sortDir = ref("asc");


const showProductForm = ref(false)
const editProductForm = ref(false)
const selectedProduct = ref(null)

const showDeleteModal = ref(false)
const deleteItem = ref(null)

const placeholder = "https://dummyimage.com/150x120/eeeeee/999999&text=No+Image"

async function fetchProducts() {
  loading.value = true

  try {
    const { data } = await axios.get("/products", {
      params: {
        page: page.value,
        per_page: perPage.value,
        search: search.value,
      }
    })
    products.value = data
  } catch (err) {
    showToast("Failed to load products", "error")
  }

  loading.value = false
}

async function fetchCategories() {
  try {
    const { data } = await axios.get("/categories")
    categories.value = data
  } catch (err) {
    showToast("Failed to load categories", "error")
  }
}

function changePage(p) {
  page.value = p
  fetchProducts()
}

function changePerPage() {
  page.value = 1
  fetchProducts()
}

function editProduct(p) {
  selectedProduct.value = p
  editProductForm.value = true
}

function askDelete(p) {
  deleteItem.value = p
  showDeleteModal.value = true
}

async function deleteProduct() {
  try {
    await axios.delete(`/products/${deleteItem.value.id}`)
    showDeleteModal.value = false
    fetchProducts()
    showToast("Product deleted", "success")
  } catch (err) {
    showToast("Failed to delete product", "error")
  }
}

function reload() {
  showProductForm.value = false
  editProductForm.value = false
  fetchProducts()
}

let timeout
function debouncedSearch() {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    page.value = 1
    fetchProducts()
  }, 400)
}
/* -----------------------
  SORTING
----------------------- */
function sort(column) {
  if (sortBy.value === column) {
    sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
  } else {
    sortBy.value = column;
    sortDir.value = "asc";
  }
  fetchProducts();
}
function updatePerPage(n) {
  perPage.value = n;
  page.value = 1;
  fetchProducts();
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>
