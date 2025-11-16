<template>
  <div class="container mx-auto px-4 py-6">

    <!-- Navigation -->
    <div class="flex gap-3 mb-6">
      <button 
        :class="activeView === 'products' ? activeClass : inactiveClass"
        @click="activeView = 'products'"
      >
        Products Dashboard
      </button>

      <button 
        :class="activeView === 'categories' ? activeClass : inactiveClass"
        @click="activeView = 'categories'"
      >
        Categories Dashboard
      </button>
    </div>

    <!-- Pages -->
    <ProductsPage v-if="activeView === 'products'" />
    <CategoriesPage v-if="activeView === 'categories'" />
    <Toast />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

import ProductsPage from '@/views/Admin/Products.vue'
import CategoriesPage from '@/views/Admin/Categories.vue'
import Toast from '@/views/CMN/Toast.vue'

const router = useRouter()

const activeView = ref<'products' | 'categories'>('products')

const activeClass = "px-6 py-3 rounded text-base font-medium bg-blue-500 text-white transition"
const inactiveClass = "px-6 py-3 rounded text-base font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition"

async function logout() {
  try {
    await axios.post('/logout')
  } catch (_) {}

  localStorage.removeItem('ec_token')
  router.push('/login')
}
</script>
