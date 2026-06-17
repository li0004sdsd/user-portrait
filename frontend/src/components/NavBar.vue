<template>
  <nav class="navbar">
    <div class="nav-inner">
      <RouterLink to="/dashboard" class="nav-brand">UserPortrait</RouterLink>
      <div class="nav-links">
        <RouterLink to="/portraits" class="nav-link">Portraits</RouterLink>
        <RouterLink to="/tags" class="nav-link">Tags</RouterLink>
        <RouterLink to="/analysis" class="nav-link">Analysis</RouterLink>
        <RouterLink to="/recommendations" class="nav-link">Marketing</RouterLink>
      </div>
      <div class="nav-user">
        <span class="user-name">{{ authStore.user?.name }}</span>
        <button class="btn btn-sm btn-secondary" @click="handleLogout">Logout</button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
const authStore = useAuthStore()
const router = useRouter()
async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.navbar { position: fixed; top: 0; left: 0; right: 0; height: 60px; background: white; border-bottom: 1px solid #e5e7eb; z-index: 100; }
.nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 16px; height: 100%; display: flex; align-items: center; gap: 24px; }
.nav-brand { font-size: 18px; font-weight: 700; color: #4f46e5; text-decoration: none; }
.nav-links { display: flex; gap: 4px; flex: 1; }
.nav-link { padding: 6px 12px; border-radius: 6px; text-decoration: none; color: #6b7280; font-size: 14px; font-weight: 500; }
.nav-link:hover, .nav-link.router-link-active { background: #eef2ff; color: #4f46e5; }
.nav-user { display: flex; align-items: center; gap: 12px; }
.user-name { font-size: 14px; color: #374151; font-weight: 500; }
</style>
