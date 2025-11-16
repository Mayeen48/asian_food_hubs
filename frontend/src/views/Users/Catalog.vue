<template>
  <div class="catalog-bg w-full">

    <!-- 🔍 SEARCH + CATEGORY TABS -->
    <div class="w-full bg-white shadow px-4 py-4 sticky top-0 z-40">

      <!-- Search Bar -->
      <input
        v-model="search"
        type="text"
        placeholder="Search products..."
        class="w-full px-4 py-3 border rounded-lg shadow-sm mb-4 
               focus:ring-2 focus:ring-blue-500 outline-none"
      />

      <!-- CATEGORY TABS -->
      <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1">
        
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="selectCategory(cat.id)"
          class="px-4 py-2 rounded-lg whitespace-nowrap font-medium border transition"
          :class="activeCategory === cat.id
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
        >
          {{ cat.name }}
        </button>

      </div>

    </div>

    <!-- PRODUCTS FOR SELECTED CATEGORY -->
    <div v-if="loadingProducts" class="py-10 text-center text-gray-500">
      <Loader />
    </div>

    <div v-else-if="activeCategoryData">

      <!-- SUBCATEGORY GROUPS -->
      <div v-for="sub in filteredSubcategories" :key="sub.id">

        <!-- Subcategory Header -->
        <div
          v-if="activeCategoryData.children && activeCategoryData.children.length >= 1"
          class="bg-[#eef0f3] px-3 py-2 font-semibold text-gray-700 text-lg"
        >
          {{ sub.name.toUpperCase() }}
        </div>

        <!-- PRODUCTS -->
        <div class="flex flex-wrap gap-3 p-3">

          <div
            v-for="p in sub.products"
            :key="p.id"
            class="bg-white border border-gray-300 rounded-lg shadow-sm 
                   min-w-[150px] max-w-[220px] flex-1 relative overflow-hidden cursor-pointer"
          >

            <!-- Price badge -->
            <div class="absolute top-0 left-0 bg-[#29abe2] text-white font-bold px-2 py-1 rounded-br-md">
              ${{ Number(p.price).toFixed(2) }}
            </div>

            <!-- Image -->
            <div class="flex items-center justify-center bg-white h-[180px]"
                 @click="openImage(p)">
              <img
                :src="resolveImage(p.image)"
                class="max-h-[150px] max-w-[90%] object-contain hover:scale-105 transition"
              />
            </div>

            <!-- Name -->
            <div class="text-center text-sm font-semibold p-2 text-gray-800 min-h-[40px]">
              {{ p.name }}
            </div>

            <!-- Stock -->
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
        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
      >
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
      <img :src="resolveImage(selectedImage)" class="w-full max-h-[80vh] object-contain" />
      <div class="flex justify-center py-3">
        <button @click="dialog = false" class="px-5 py-2 bg-blue-600 text-white rounded">
          Close
        </button>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import Loader from "@/views/CMN/Loader.vue"

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

const categories = ref([]);
const activeCategory = ref<number | null>(null);
const activeCategoryData = ref<any>(null);

const loadingProducts = ref(false);
const search = ref("");

const dialog = ref(false);
const selectedImage = ref("");

function resolveImage(path: string | null) {
  if (!path) return "https://dummyimage.com/400x400/eee/666&text=No+Image";
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

/* -------------------------------
   LOAD CATEGORY LIST ONLY
---------------------------------- */
async function loadCategories() {
  const { data } = await axios.get("/categories"); // Make API only return ID & name
  categories.value = data;

  // Select first category automatically
  if (categories.value.length > 0) {
    selectCategory(categories.value[0].id);
  }
}

/* -------------------------------
   LOAD PRODUCTS WHEN TAB CLICKED
---------------------------------- */
async function selectCategory(categoryId: number) {
  activeCategory.value = categoryId;
  loadingProducts.value = true;

  try {
    const { data } = await axios.get(`/products/by-category/${categoryId}`);

    data.children?.forEach((sub) => {
      sub.products?.forEach((p) => (p.price = Number(p.price || 0)));
    });

    activeCategoryData.value = data;

  } finally {
    loadingProducts.value = false;
  }
}


/* -------------------------------
   SEARCH FILTER (INSIDE ACTIVE CATEGORY)
---------------------------------- */
const filteredSubcategories = computed(() => {
  if (!activeCategoryData.value) return [];

  const keyword = search.value.toLowerCase().trim();

  if (!keyword) return activeCategoryData.value.children;

  return activeCategoryData.value.children
    .map((sub) => ({
      ...sub,
      products: sub.products.filter((p) =>
        p.name.toLowerCase().includes(keyword)
      ),
    }))
    .filter((sub) => sub.products.length > 0);
});

onMounted(() => {
  loadCategories();
});
</script>


<style>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  scrollbar-width: none;
}

@media print {
  .no-print {
    display: none !important;
  }
}
</style>
