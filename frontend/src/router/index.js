import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/login', component: () => import('@/views/LoginView.vue'), meta: { guest: true } },
  { path: '/register', component: () => import('@/views/RegisterView.vue'), meta: { guest: true } },
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', component: () => import('@/views/DashboardView.vue'), meta: { auth: true } },
  { path: '/portraits', component: () => import('@/views/portraits/PortraitListView.vue'), meta: { auth: true } },
  { path: '/portraits/create', component: () => import('@/views/portraits/PortraitCreateView.vue'), meta: { auth: true } },
  { path: '/portraits/:id', component: () => import('@/views/portraits/PortraitDetailView.vue'), meta: { auth: true } },
  { path: '/portraits/:id/edit', component: () => import('@/views/portraits/PortraitEditView.vue'), meta: { auth: true } },
  { path: '/tags', component: () => import('@/views/tags/TagManagementView.vue'), meta: { auth: true } },
  { path: '/analysis', component: () => import('@/views/analysis/AnalysisView.vue'), meta: { auth: true } },
  { path: '/recommendations', component: () => import('@/views/recommendations/RecommendationsView.vue'), meta: { auth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  if (to.meta.auth && !token) return next('/login')
  if (to.meta.guest && token) return next('/dashboard')
  next()
})

export default router
