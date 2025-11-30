<template>
  <div class="max-w-lg mx-auto bg-white shadow rounded p-6 mt-6">
    <h2 class="text-xl font-bold mb-4">Change Password</h2>

    <div class="space-y-3">
      <div>
        <label>Old Password</label>
        <input
            v-model="form.old_password"
            type="password"
            :class="['w-full px-3 py-2 rounded', fieldClass('old_password')]"
        />
        <p v-if="errors.old_password" class="error-text">• {{ errors.old_password[0] }}</p>
        </div>

        <div>
        <label>New Password</label>
        <input
            v-model="form.new_password"
            type="password"
            :class="['w-full px-3 py-2 rounded', fieldClass('new_password')]"
        />
        <p v-if="errors.new_password" class="error-text">• {{ errors.new_password[0] }}</p>
        </div>

    </div>

    <button
      @click="submit"
      class="mt-6 px-4 py-2 bg-blue-600 text-white rounded"
    >
      Update Password
    </button>
  </div>
</template>

<script setup>
import axios from "axios"
import { ref } from "vue"
import { useToast } from "@/views/CMN/useToast"

const form = ref({
  old_password: "",
  new_password: ""
})

const errors = ref({})
const { showToast } = useToast()

function fieldClass(field) {
  return errors.value[field] ? "border input-error" : "border"
}

async function submit() {
  errors.value = {}

  try {
    await axios.post("/users/change-password", form.value)
    showToast("Password changed successfully", "success")
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
    } else {
      showToast("Old password incorrect", "error")
    }
  }
}
</script>

