<!-- <template>
  <v-app>
    <v-main>
      <NavbarFront v-if="!isAdmin" />
      <NavbarAdmin v-else />
      <router-view />
    </v-main>
  </v-app>
</template>
<script setup lang="ts">

import NavbarFront from '@/views/CMN/Navbars/NavbarFront.vue'
import NavbarAdmin from '@/views/CMN/Navbars/NavbarAdmin.vue'
import { useRoute } from 'vue-router'
import { computed, watch } from "vue"

const route = useRoute()
const isAdmin = computed(() => route.path.startsWith("/admin"))
watch(() => route.path, (newVal) => {
  console.log("Route:", newVal)
  console.log("Is Admin:", isAdmin.value)
})
</script> -->


<template>
  <v-app>
    <v-main>
      <NavbarFront v-if="!isAdmin" />
      <NavbarAdmin v-else />
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import NavbarFront from '@/views/CMN/Navbars/NavbarFront.vue'
import NavbarAdmin from '@/views/CMN/Navbars/NavbarAdmin.vue'
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()

const isAdmin = computed(() => {
  return route.path.startsWith('/admin')
})
</script>

