import { createRouter, createWebHistory } from 'vue-router'
import Catalog from '@/views/Users/Catalog.vue'
import Admin from '@/views/Admin/Admin.vue'
import Login from '@/views/Auth/Login.vue'

const routes = [
  { path: '/', name: 'home', component: Catalog },
  { path: '/catalog', name: 'catalog', component: Catalog },
  { path: '/login', name: 'login', component: Login },
  { path: '/admin', name: 'admin', component: Admin, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ---- simple auth guard ----
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('ec_token')
  if (to.meta.requiresAuth && !token) {
    next({ name: 'login' })
  } else if (to.name === 'login' && token) {
    next({ name: 'admin' })
  } else {
    next()
  }
})

export default router
