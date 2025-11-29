// import axios from 'axios'

// const API_URL = import.meta.env.VITE_API_URL

// axios.defaults.baseURL = API_URL
// axios.defaults.withCredentials = true

// axios.interceptors.request.use(config => {
//   const token = localStorage.getItem('ec_token')
//   if (token) {
//     config.headers.Authorization = `Bearer ${token}`
//   }
//   return config
// })

// export default axios


import axios from "axios";
import { useLoaderStore } from "@/stores/loader";
const API_URL = import.meta.env.VITE_API_URL

axios.defaults.baseURL = API_URL;

// Add loader on every request
axios.interceptors.request.use(
  (config) => {
      const token = localStorage.getItem('ec_token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
    const loader = useLoaderStore();
    loader.show();
    return config;
  },
  (error) => {
    const loader = useLoaderStore();
    loader.hide();
    return Promise.reject(error);
  }
);

// Hide loader on response
axios.interceptors.response.use(
  (response) => {
    const loader = useLoaderStore();
    loader.hide();
    return response;
  },
  (error) => {
    const loader = useLoaderStore();
    loader.hide();
    return Promise.reject(error);
  }
);

export default axios;

