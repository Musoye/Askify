import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './style.css'

// Import components
import LandingPage from './components/LandingPage.vue'
import SignUp from './components/SignUp.vue'
import Login from './components/Login.vue'
import AdminDashboard from './components/AdminDashboard.vue'
import StudentDashboard from './components/StudentDashboard.vue'
import DocumentView from './components/DocumentView.vue'
import ChatWithDocument from './components/ChatWithDocument.vue'

const routes = [
  { path: '/', component: LandingPage },
  { path: '/signup', component: SignUp },
  { path: '/login', component: Login },
  { path: '/admin', component: AdminDashboard },
  { path: '/student', component: StudentDashboard },
  { path: '/document/:id', component: DocumentView },
  { path: '/chat/:id', component: ChatWithDocument }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

const app = createApp(App)
app.use(router)
app.mount('#app')
