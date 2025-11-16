import { defineStore } from 'pinia'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL

axios.defaults.baseURL = API_URL
axios.defaults.withCredentials = true

export const useAuth = defineStore('auth', {
  state: () => ({ token: localStorage.getItem('token') || '' }),
  actions: {
    async login(email: string, password: string) {
      const { data } = await axios.post('/login', { email, password })
      this.token = data.token
      localStorage.setItem('token', data.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
    },
    logout() {
      this.token = ''
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    }
  }
})
