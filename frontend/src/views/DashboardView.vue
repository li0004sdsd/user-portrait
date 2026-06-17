<template>
  <div class="container">
    <h1 class="page-title">Dashboard</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <template v-else>
      <div class="grid-4">
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_portraits ?? 0 }}</div>
          <div class="stat-label">Total Portraits</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.active_portraits ?? 0 }}</div>
          <div class="stat-label">Active Portraits</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_tags ?? 0 }}</div>
          <div class="stat-label">Total Tags</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_categories ?? 0 }}</div>
          <div class="stat-label">Tag Categories</div>
        </div>
      </div>
      <div class="dash-grid mt-24">
        <div class="card">
          <h2 class="section-title">Recent Portraits</h2>
          <div v-if="portraits.length === 0" class="empty-state"><p>No portraits yet.</p></div>
          <div v-else>
            <div v-for="p in portraits.slice(0,5)" :key="p.id" class="portrait-row">
              <div>
                <div class="portrait-name">{{ p.name }}</div>
                <div class="text-muted">{{ p.occupation || 'No occupation' }} · {{ p.location || 'No location' }}</div>
              </div>
              <RouterLink :to="`/portraits/${p.id}`" class="btn btn-sm btn-secondary">View</RouterLink>
            </div>
          </div>
          <RouterLink to="/portraits" class="btn btn-primary mt-16">All Portraits</RouterLink>
        </div>
        <div class="card">
          <h2 class="section-title">Quick Actions</h2>
          <div class="quick-actions">
            <RouterLink to="/portraits/create" class="action-btn">
              <span class="action-icon">+</span>
              <div>
                <div class="action-title">New Portrait</div>
                <div class="text-muted">Create a user portrait</div>
              </div>
            </RouterLink>
            <RouterLink to="/tags" class="action-btn">
              <span class="action-icon">#</span>
              <div>
                <div class="action-title">Manage Tags</div>
                <div class="text-muted">Organize tag categories</div>
              </div>
            </RouterLink>
            <RouterLink to="/analysis" class="action-btn">
              <span class="action-icon">~</span>
              <div>
                <div class="action-title">View Analysis</div>
                <div class="text-muted">Portrait insights</div>
              </div>
            </RouterLink>
            <RouterLink to="/recommendations" class="action-btn">
              <span class="action-icon">*</span>
              <div>
                <div class="action-title">Marketing Recs</div>
                <div class="text-muted">Channel recommendations</div>
              </div>
            </RouterLink>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getAnalysis } from '@/api/analysis'
import { getPortraits } from '@/api/portraits'
const stats = ref({})
const portraits = ref([])
const loading = ref(true)
onMounted(async () => {
  try {
    const [analysisRes, portraitsRes] = await Promise.all([getAnalysis(), getPortraits()])
    stats.value = analysisRes.data
    portraits.value = portraitsRes.data.data || []
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
.stat-card { background: white; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #4f46e5; }
.stat-value { font-size: 36px; font-weight: 700; color: #4f46e5; }
.stat-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
.dash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
.portrait-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
.portrait-name { font-weight: 500; font-size: 14px; }
.quick-actions { display: flex; flex-direction: column; gap: 12px; }
.action-btn { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb; text-decoration: none; color: inherit; transition: background 0.2s; }
.action-btn:hover { background: #eef2ff; }
.action-icon { width: 36px; height: 36px; background: #4f46e5; color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; flex-shrink: 0; }
.action-title { font-weight: 500; font-size: 14px; }
@media (max-width: 768px) { .dash-grid { grid-template-columns: 1fr; } }
</style>
