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
    <!-- Search -->
      <input
        v-model="search"
        @input="searchNow()"
        type="text"
        placeholder="Search users..."
        class="border px-4 py-2 rounded w-full md:w-1/3 shadow-sm"
      />

      <!-- Pagination -->
    <Pagination
          v-if="users.data.length"
          :currentPage="users.current_page"
          :lastPage="users.last_page"
          :perPage="perPage"
          :perPageOptions="[5,10,20,50,100]"
          :from="users.from"
          :to="users.to"
          :total="users.total"
          @change="changePage"
          @update:perPage="updatePerPage"
        />


    <!-- User Table -->
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-[700px] w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('id')">
              SL
              <span v-if="sortBy === 'id'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('name')">
              Name
              <span v-if="sortBy === 'name'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('email')">
              Email
              <span v-if="sortBy === 'email'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-3 cursor-pointer whitespace-nowrap" @click="sort('created_at')">
              Created
              <span v-if="sortBy === 'created_at'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-3">Creator</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="u in users.data" :key="u.id" class="border-b">
            <td class="px-4 py-3">{{ u.id }}</td>
            <td class="px-4 py-3">{{ u.name }}</td>
            <td class="px-4 py-3">{{ u.email }}</td>
            <td class="px-4 py-3">{{ u.created_at.slice(0, 10) }}</td>

            <td class="px-4 py-3">{{ u.creator?.name }}</td>

            <td class="px-4 py-3 text-center space-x-3">
              <button class="text-blue-600" @click="openEdit(u)">Edit</button>
              <button class="text-orange-600" @click="openReset(u)">Reset Password</button>
              <button class="text-red-600" @click="confirmDelete(u)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
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
import Pagination from "@/views/CMN/Pagination.vue";

const users = ref({ data: [] });
const showForm = ref(false);
const formMode = ref("create");
const showReset = ref(false);
const selectedUser = ref(null);
const showDeleteModal = ref(false);

const page = ref(1);
const perPage = ref(10);
const search = ref("");
const sortBy = ref("id");
const sortDir = ref("desc");

const { showToast } = useToast();

async function loadUsers() {
  const { data } =await axios.get("/users", {
    params: {
        page: page.value,
        per_page: perPage.value,
        search: search.value,
        sort_by: sortBy.value,
        sort_dir: sortDir.value
      }
    })
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
function sort(col) {
  if (sortBy.value === col) {
    sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
  } else {
    sortBy.value = col;
    sortDir.value = "asc";
  }
  loadUsers();
}
let timer;
function searchNow() {
  clearTimeout(timer);
  timer = setTimeout(() => {
    page.value = 1;
    loadUsers();
  }, 400);
}

function changePage(p) {
  page.value = p
  loadUsers()
}
function updatePerPage(n) {
  perPage.value = n;
  page.value = 1;
  loadUsers();
}


onMounted(loadUsers);
</script>
