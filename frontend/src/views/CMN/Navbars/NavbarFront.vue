<template>
  <nav class="w-full bg-white shadow-sm border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-3" @click="closeMenu">
        <img
          src="/images/logo.png"
          class="h-10 w-auto object-contain"
          alt="Asian Food Hubs Logo"
        />
        <h1 class="text-lg font-semibold text-gray-800">
          Asian Food Hubs
        </h1>
      </router-link>

      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center gap-6">
        <router-link to="/" class="nav-link">Home</router-link>
        <router-link to="/catalog" class="nav-link">Products</router-link>

        <router-link
          to="/login"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Admin Login
        </router-link>
      </div>

      <!-- Mobile Menu Button -->
      <button
        @click="open = !open"
        class="md:hidden text-gray-700 text-3xl"
      >
        ☰
      </button>
    </div>

    <!-- Mobile Menu -->
    <div
      v-if="open"
      class="md:hidden bg-white px-4 pb-4 border-t"
    >
      <router-link to="/" class="mobile-link" @click="closeMenu">Home</router-link>
      <router-link to="/catalog" class="mobile-link" @click="closeMenu">Products</router-link>
      <router-link to="/contact" class="mobile-link" @click="closeMenu">Contact</router-link>

      <router-link
        to="/login"
        class="mobile-link text-blue-600 font-semibold"
        @click="closeMenu"
      >
        Admin Login
      </router-link>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref } from "vue"
const open = ref(false)

// Auto-close menu
function closeMenu() {
  open.value = false
}
</script>

<style scoped>
.nav-link {
  @apply text-gray-700 hover:text-blue-600 transition;
}
.mobile-link {
  @apply block py-2 border-b text-gray-700 hover:text-blue-600;
}
</style>
