import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL

axios.defaults.baseURL = API_URL
axios.defaults.withCredentials = true

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('ec_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default axios
