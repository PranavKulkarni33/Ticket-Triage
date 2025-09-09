import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'

import Tickets from './pages/Tickets.vue'
import TicketDetail from './pages/TicketDetail.vue'
import Dashboard from './pages/Dashboard.vue'

import '../css/app.css'

const routes = [
  { path: '/tickets', component: Tickets },
  { path: '/tickets/:id', component: TicketDetail },
  { path: '/dashboard', component: Dashboard },
  { path: '/:pathMatch(.*)*', redirect: '/tickets' },
]

const router = createRouter({ history: createWebHistory(), routes })
createApp(App).use(router).mount('#app')
