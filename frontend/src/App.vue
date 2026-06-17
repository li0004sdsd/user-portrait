<template>
  <div id="app">
    <NavBar v-if="authStore.isAuthenticated" />
    <main :class="authStore.isAuthenticated ? 'with-nav' : ''">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import NavBar from '@/components/NavBar.vue'
const authStore = useAuthStore()
</script>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f2f5; color: #1a1a2e; }
#app { min-height: 100vh; }
main { min-height: 100vh; }
main.with-nav { padding-top: 60px; }
.container { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s; text-decoration: none; }
.btn-primary { background: #4f46e5; color: white; }
.btn-primary:hover { background: #4338ca; }
.btn-secondary { background: #e5e7eb; color: #374151; }
.btn-secondary:hover { background: #d1d5db; }
.btn-danger { background: #ef4444; color: white; }
.btn-danger:hover { background: #dc2626; }
.btn-sm { padding: 4px 10px; font-size: 12px; }
.card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151; }
.form-input { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #4f46e5; }
.form-textarea { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; resize: vertical; min-height: 80px; }
.form-textarea:focus { border-color: #4f46e5; }
.form-select { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; background: white; }
.error-msg { color: #ef4444; font-size: 13px; margin-top: 4px; }
.page-title { font-size: 24px; font-weight: 700; color: #1a1a2e; margin-bottom: 24px; }
.badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 500; }
.grid-2 { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
.flex { display: flex; }
.flex-between { display: flex; justify-content: space-between; align-items: center; }
.gap-8 { gap: 8px; }
.gap-12 { gap: 12px; }
.mt-16 { margin-top: 16px; }
.mt-24 { margin-top: 24px; }
.text-muted { color: #6b7280; font-size: 13px; }
.loading { text-align: center; padding: 40px; color: #6b7280; }
.empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
.empty-state h3 { font-size: 18px; margin-bottom: 8px; }
</style>
