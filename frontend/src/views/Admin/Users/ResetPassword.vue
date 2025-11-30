<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white w-full max-w-md p-6 rounded">

      <h2 class="text-xl font-bold mb-4">
        Reset Password for {{ user.name }}
      </h2>

      <input
        v-model="newPassword"
        type="password"
        :class="['w-full px-3 py-2 rounded', fieldClass('new_password')]"
        />
        <p v-if="errors.new_password" class="error-text">• {{ errors.new_password[0] }}</p>


      <div class="flex justify-end gap-3 mt-6">
        <button class="px-4 py-2 bg-gray-300 rounded" @click="$emit('close')">Cancel</button>

        <button class="px-4 py-2 bg-orange-600 text-white rounded" @click="reset">
          Reset Password
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import axios from "axios"
import { useToast } from "@/views/CMN/useToast"

const props = defineProps({ user: Object })
const emit = defineEmits(["close"])

const newPassword = ref("")
const errors = ref({})
const { showToast } = useToast()

function fieldClass(field) {
  return errors.value[field] ? "border input-error" : "border"
}

async function reset() {
  errors.value = {}

  try {
    await axios.post(`/users/${props.user.id}/reset-password`, {
      new_password: newPassword.value
    })

    showToast("Password reset successfully", "success")
    emit("close")
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
    } else {
      showToast("Failed to reset password", "error")
    }
  }
}
</script>

