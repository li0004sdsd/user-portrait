<template>
  <div class="container">
    <div class="flex-between">
      <h1 class="page-title">User Portraits</h1>
      <RouterLink to="/portraits/create" class="btn btn-primary">+ New Portrait</RouterLink>
    </div>
    <div v-if="store.loading" class="loading">Loading...</div>
    <div v-else-if="store.portraits.length === 0" class="empty-state">
      <h3>No portraits yet</h3>
      <p>Create your first user portrait to get started.</p>
      <RouterLink to="/portraits/create" class="btn btn-primary mt-16">Create Portrait</RouterLink>
    </div>
    <div v-else class="grid-2 mt-16">
      <div v-for="p in store.portraits" :key="p.id" class="portrait-card">
        <div class="portrait-header">
          <div class="portrait-avatar">{{ p.name.charAt(0).toUpperCase() }}</div>
          <div>
            <div class="portrait-name">{{ p.name }}</div>
            <div class="text-muted">{{ p.age ? p.age + ' yrs' : '' }} {{ p.gender ? '· ' + p.gender : '' }}</div>
          </div>
          <span :class="['badge', p.status === 'active' ? 'badge-active' : 'badge-inactive']">{{ p.status }}</span>
        </div>
        <div class="portrait-meta">
          <span v-if="p.occupation">{{ p.occupation }}</span>
          <span v-if="p.location">{{ p.location }}</span>
        </div>
        <div class="portrait-tags" v-if="p.tags && p.tags.length">
          <span v-for="tag in p.tags.slice(0,4)" :key="tag.id" class="tag-chip" :style="{ background: tag.category?.color + '22', color: tag.category?.color }">{{ tag.name }}</span>
          <span v-if="p.tags.length > 4" class="tag-chip">+{{ p.tags.length - 4 }}</span>
        </div>
        <div class="portrait-actions">
          <RouterLink :to="`/portraits/${p.id}`" class="btn btn-sm btn-secondary">View</RouterLink>
          <RouterLink :to="`/portraits/${p.id}/edit`" class="btn btn-sm btn-secondary">Edit</RouterLink>
          <button class="btn btn-sm btn-danger" @click="handleDelete(p.id)">Delete</button>
        </div>
      </div>
    </div>
    <div class="pagination mt-24" v-if="store.pagination.last_page > 1">
      <button class="btn btn-secondary" :disabled="store.pagination.current_page <= 1" @click="loadPage(store.pagination.current_page - 1)">Prev</button>
      <span class="page-info">{{ store.pagination.current_page }} / {{ store.pagination.last_page }}</span>
      <button class="btn btn-secondary" :disabled="store.pagination.current_page >= store.pagination.last_page" @click="loadPage(store.pagination.current_page + 1)">Next</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePortraitsStore } from '@/stores/portraits'
const store = usePortraitsStore()
onMounted(() => store.fetchPortraits())
async function handleDelete(id) {
  if (confirm('Delete this portrait?')) await store.deletePortrait(id)
}
function loadPage(page) { store.fetchPortraits(page) }
</script>

<style scoped>
.portrait-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.portrait-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.portrait-avatar { width: 44px; height: 44px; border-radius: 50%; background: #4f46e5; color: white; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; flex-shrink: 0; }
.portrait-name { font-weight: 600; font-size: 16px; }
.badge-active { background: #dcfce7; color: #16a34a; }
.badge-inactive { background: #f3f4f6; color: #6b7280; }
.portrait-meta { display: flex; gap: 16px; font-size: 13px; color: #6b7280; margin-bottom: 12px; }
.portrait-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px; }
.tag-chip { padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 500; background: #eef2ff; color: #4f46e5; }
.portrait-actions { display: flex; gap: 8px; }
.pagination { display: flex; align-items: center; gap: 12px; justify-content: center; }
.page-info { font-size: 14px; color: #6b7280; }
</style>
