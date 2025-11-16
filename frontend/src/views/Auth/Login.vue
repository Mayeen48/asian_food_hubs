<template>
  <div class="h-screen flex items-center justify-center">
    <div class="p-6 w-full max-w-md bg-white rounded-lg shadow-md">
      <h2 class="text-xl font-bold text-center mb-6">Admin Login</h2>

      <div class="mb-4">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div class="mb-4">
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <button
        @click="login"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors mt-4"
      >
        Login
      </button>

      <p v-if="error" class="text-red-600 text-center mt-3">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
  error.value = ''
  try {
    const { data } = await axios.post('/login', {
      email: email.value,
      password: password.value,
    })

    // ✅ store token under unique name
    localStorage.setItem('ec_token', data.token)
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`

    router.push({ name: 'admin' })
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Login failed'
  }
}
</script>
