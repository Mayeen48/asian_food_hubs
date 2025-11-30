<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white w-full max-w-md rounded-lg p-6 shadow-lg">

      <h2 class="text-xl font-bold mb-4">
        {{ mode === "create" ? "Create User" : "Edit User" }}
      </h2>

      <div class="space-y-3">
        <div>
            <label>Name</label>
            <input
                v-model="form.name"
                :class="['w-full px-3 py-2 rounded', fieldClass('name')]"
            />
            <p v-if="errors.name" class="error-text">• {{ errors.name[0] }}</p>
            </div>

            <div>
            <label>Email</label>
            <input
                v-model="form.email"
                :class="['w-full px-3 py-2 rounded', fieldClass('email')]"
            />
            <p v-if="errors.email" class="error-text">• {{ errors.email[0] }}</p>
            </div>

            <div v-if="mode === 'create'">
            <label>Password</label>
            <input
                type="password"
                v-model="form.password"
                :class="['w-full px-3 py-2 rounded', fieldClass('password')]"
            />
            <p v-if="errors.password" class="error-text">• {{ errors.password[0] }}</p>
            </div>

      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button class="px-4 py-2 bg-gray-300 rounded" @click="$emit('close')">Cancel</button>
        <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="save">
          {{ mode === "create" ? "Create" : "Update" }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
import { useToast } from "@/views/CMN/useToast"

const props = defineProps({
  mode: String,
  userData: Object
})
const emit = defineEmits(["close", "saved"])
const { showToast } = useToast()

const form = ref({
  name: "",
  email: "",
  password: ""
})

const errors = ref({})

// Apply error class
function fieldClass(field) {
  return errors.value[field] ? "border input-error" : "border"
}

onMounted(() => {
  if (props.mode === "edit") {
    form.value.name = props.userData.name
    form.value.email = props.userData.email
  }
})

async function save() {
  errors.value = {} // reset

  try {
    if (props.mode === "create") {
      await axios.post("/users", form.value)
      showToast("User created successfully", "success")
    } else {
      await axios.put(`/users/${props.userData.id}`, form.value)
      showToast("User updated successfully", "success")
    }

    emit("saved")
    emit("close")
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
    } else {
      showToast("Something went wrong", "error")
    }
  }
}
</script>

