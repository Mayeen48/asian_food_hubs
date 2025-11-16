<template>
  <nav class="w-full bg-gray-900 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-3">
        <img
          src="@/public/images/logo.png"
          class="h-8 w-auto invert"
          alt="Admin Logo"
        />
        <h1 class="text-lg font-semibold">Asian Food Hubs</h1>
      </router-link>

      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center gap-6">
        <!-- <router-link class="admin-link" to="/admin/products">Products</router-link>
        <router-link class="admin-link" to="/admin/categories">Categories</router-link> -->

        <!-- If logged in show Logout ELSE Login -->
        <button
          v-if="isLoggedIn"
          @click="logout"
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
        >
          Logout
        </button>

        <router-link
          v-else
          to="/login"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Admin Login
        </router-link>
      </div>

      <!-- Mobile Menu Button -->
      <button class="md:hidden text-3xl" @click="open = !open">
        ☰
      </button>
    </div>

    <!-- Mobile Menu -->
    <div v-if="open" class="md:hidden bg-gray-800 px-4 pb-4">

      <!-- <router-link class="mobile-link" to="/admin/products">Products</router-link>
      <router-link class="mobile-link" to="/admin/categories">Categories</router-link> -->

      <!-- Login / Logout in Mobile -->
      <button
        v-if="isLoggedIn"
        @click="logout"
        class="w-full text-left py-2 text-red-400 font-semibold"
      >
        Logout
      </button>

      <router-link
        v-else
        to="/login"
        class="w-full block py-2 text-blue-400 font-semibold"
      >
        Admin Login
      </router-link>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"

const router = useRouter()
const open = ref(false)

// 🔥 Check if logged in
const isLoggedIn = computed(() => !!localStorage.getItem("ec_token"))

async function logout() {
    console.log("Is Admin Logged In:", isLoggedIn)
  try {
    await axios.post("/logout")
  } catch (_) {}

  localStorage.removeItem("ec_token")
  router.push("/login")
}
</script>

<style scoped>
.admin-link {
  @apply text-gray-300 hover:text-white transition;
}
.mobile-link {
  @apply block py-2 border-b border-gray-700 text-gray-300 hover:text-white;
}
</style>
