<template>
  <div class="catalog-bg w-full">

    <!-- 🔍 SEARCH BAR -->
    <div class="w-full bg-white shadow px-4 py-4 sticky top-0 z-40">
      <input
        v-model="search"
        type="text"
        placeholder="Search products..."
        class="w-full px-4 py-3 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 outline-none"
      />
    </div>

    <!-- CATEGORY LIST -->
    <div v-for="cat in filteredCategories" :key="cat.id">

      <!-- Category Header -->
      <div class="bg-[#d9dde1] px-4 py-3 font-bold text-xl text-gray-800">
        {{ cat.name.toUpperCase() }}
      </div>

      <!-- Subcategories -->
      <div v-for="sub in cat.children" :key="sub.id" class="subcategory-section">

        <!-- Subcategory Header -->
        <div v-if="cat.children.length > 1"
             class="bg-[#eef0f3] px-3 py-2 font-semibold text-gray-700 text-lg">
          {{ sub.name.toUpperCase() }}
        </div>

        <!-- PRODUCT GRID -->
        <div class="flex flex-wrap gap-3 p-3">

          <div
            v-for="p in sub.products"
            :key="p.id"
            class="bg-white border border-gray-300 rounded-lg shadow-sm 
                   min-w-[150px] max-w-[220px] flex-1 relative overflow-hidden cursor-pointer"
          >

            <!-- Price Badge -->
            <div class="absolute top-0 left-0 bg-[#29abe2] text-white font-bold px-2 py-1 rounded-br-md">
              ${{ Number(p.price || 0).toFixed(2) }}
            </div>

            <!-- Product Image -->
            <div class="flex items-center justify-center bg-white h-[180px]"
                 @click="openImage(p)">
              <img
                :src="resolveImage(p.image)"
                :alt="p.name"
                class="max-h-[150px] max-w-[90%] object-contain hover:scale-105 transition"
              />
            </div>

            <!-- Product Name -->
            <div class="text-center text-sm font-semibold p-2 text-gray-800 min-h-[40px]">
              {{ p.name }}
            </div>

            <!-- Stock Row -->
            <div class="bg-gray-300 px-2 py-2 flex items-center justify-between text-sm font-medium">
              <button class="text-blue-700 font-bold text-lg">−</button>
              <span class="text-center flex-1">{{ p.stock ?? 0 }} in stock</span>
              <button class="text-blue-700 font-bold text-lg">+</button>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- PRINT BUTTON -->
    <div class="footer no-print text-center py-6">
      <button
        @click="printPage"
        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
        Print / Save as PDF
      </button>
    </div>
  </div>

  <!-- IMAGE POPUP -->
  <div
    v-if="dialog"
    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 no-print"
  >
    <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-lg w-full">
      <img :src="resolveImage(selectedImage)" alt="" class="w-full max-h-[80vh] object-contain" />

      <div class="flex justify-center py-3">
        <button
          class="px-5 py-2 bg-blue-600 rounded text-white hover:bg-blue-700"
          @click="dialog = false"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>



<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
const placeholder = "https://dummyimage.com/400x400/eeeeee/999999&text=No+Image";

const categories = ref<any[]>([]);
const dialog = ref(false);
const selectedImage = ref("");
const search = ref("");

// Convert Laravel storage paths to full URL
function resolveImage(path: string | null) {
  if (!path) return placeholder;
  if (path.startsWith("http")) return path;
  return API_BASE_URL + path;
}

function openImage(p: any) {
  selectedImage.value = p.image;
  dialog.value = true;
}

function printPage() {
  dialog.value = false;
  requestAnimationFrame(() => window.print());
}

// 🔍 FILTER CATEGORIES + PRODUCTS
const filteredCategories = computed(() => {
  if (!search.value.trim()) return categories.value;

  const keyword = search.value.toLowerCase();

  return categories.value
    .map((cat) => ({
      ...cat,
      children: cat.children
        .map((sub) => ({
          ...sub,
          products: sub.products.filter((p) =>
            p.name.toLowerCase().includes(keyword)
          ),
        }))
        .filter((sub) => sub.products.length > 0),
    }))
    .filter((cat) => cat.children.length > 0);
});

// Fetch catalog
onMounted(async () => {
  const { data } = await axios.get("/catalog");

  data.forEach((cat) => {
    cat.children?.forEach((sub) => {
      sub.products?.forEach((p) => {
        p.price = Number(p.price || 0);
      });
    });
  });

  categories.value = data;
});
</script>



<style>
@media print {
  .no-print {
    display: none !important;
  }
  @page {
    size: A4;
    margin: 10mm;
  }
}

/* Hover effects */
.product-card:hover {
  transform: translateY(-3px);
  transition: 0.2s ease-in-out;
}
.product-img:hover {
  transform: scale(1.05);
}
</style>
