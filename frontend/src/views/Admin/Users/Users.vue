<template>
  <div>
    <div class="flex justify-between mb-4 mt-6 items-center">
      <h2 class="text-xl font-bold">User Management</h2>

      <button
        class="px-4 py-2 bg-green-600 text-white rounded"
        @click="openCreate()"
      >
        + Create User
      </button>
    </div>

    <!-- User Table -->
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-[700px] w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Created</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="u in users.data" :key="u.id" class="border-b">
            <td class="px-4 py-3">{{ u.id }}</td>
            <td class="px-4 py-3">{{ u.name }}</td>
            <td class="px-4 py-3">{{ u.email }}</td>
            <td class="px-4 py-3">{{ u.created_at.slice(0, 10) }}</td>

            <td class="px-4 py-3 text-center space-x-3">
              <button class="text-blue-600" @click="openEdit(u)">Edit</button>
              <button class="text-orange-600" @click="openReset(u)">Reset Password</button>
              <button class="text-red-600" @click="confirmDelete(u)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center gap-2">
      <button
        v-if="users.prev_page_url"
        class="px-3 py-1 border rounded"
        @click="go(users.current_page - 1)"
      >Prev</button>

      <button
        v-if="users.next_page_url"
        class="px-3 py-1 border rounded"
        @click="go(users.current_page + 1)"
      >Next</button>
    </div>

    <!-- Modals -->
    <UserForm
      v-if="showForm"
      :mode="formMode"
      :userData="selectedUser"
      @close="showForm = false"
      @saved="loadUsers"
    />

    <ResetPassword
      v-if="showReset"
      :user="selectedUser"
      @close="showReset = false"
    />

    <!-- Delete -->
    <ConfirmDelete
      v-if="showDeleteModal"
      :itemName="selectedUser?.name"
      @confirm="deleteUser"
      @cancel="showDeleteModal=false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useToast } from "@/views/CMN/useToast";

import UserForm from "./UserForm.vue";
import ResetPassword from "./ResetPassword.vue";
import ConfirmDelete from "@/views/CMN/ConfirmDelete.vue";

const users = ref({ data: [] });
const showForm = ref(false);
const formMode = ref("create");
const showReset = ref(false);
const selectedUser = ref(null);
const showDeleteModal = ref(false);

const { showToast } = useToast();

async function loadUsers(page = 1) {
  const { data } = await axios.get("/users", { params: { page } });
  users.value = data;
}

function go(p) {
  loadUsers(p);
}

function openCreate() {
  formMode.value = "create";
  selectedUser.value = null;
  showForm.value = true;
}

function openEdit(u) {
  formMode.value = "edit";
  selectedUser.value = u;
  showForm.value = true;
}

function openReset(u) {
  selectedUser.value = u;
  showReset.value = true;
}

function confirmDelete(u) {
  selectedUser.value = u;
  showDeleteModal.value = true;
}

async function deleteUser() {
  await axios.delete(`/users/${selectedUser.value.id}`);
  showToast("User deleted", "success");
  showDeleteModal.value = false;
  loadUsers();
}

onMounted(loadUsers);
</script>
