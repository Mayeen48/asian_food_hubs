
<template>
  <v-app>
    <v-main>
      <GlobalLoader />  <!-- Auto loader -->
      <NavbarFront v-if="!isAdmin" />
      <NavbarAdmin v-else />
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import GlobalLoader from "@/views/CMN/GlobalLoader.vue";
import NavbarFront from '@/views/CMN/Navbars/NavbarFront.vue'
import NavbarAdmin from '@/views/CMN/Navbars/NavbarAdmin.vue'
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()

const isAdmin = computed(() => {
  return route.path.startsWith('/admin')
})
</script>

<style>
.input-error {
  border-color: #dc2626 !important;
  background-color: #fee2e2 !important;
}

.error-text {
  color: #dc2626;
  font-size: 0.85rem;
  margin-top: 2px;
}
</style>

