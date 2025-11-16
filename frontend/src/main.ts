import './assets/main.css' // Tailwind CSS
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import './plugins/axios'


createApp(App).use(router).use(createPinia()).mount('#app')
