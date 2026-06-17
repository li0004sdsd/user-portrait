<template>
  <div class="container">
    <h1 class="page-title">Portrait Analysis</h1>
    <div v-if="loading" class="loading">Loading analysis...</div>
    <template v-else>
      <div class="grid-4">
        <div class="stat-card"><div class="stat-value">{{ data.total_portraits }}</div><div class="stat-label">Total Portraits</div></div>
        <div class="stat-card active"><div class="stat-value">{{ data.active_portraits }}</div><div class="stat-label">Active</div></div>
        <div class="stat-card warning"><div class="stat-value">{{ data.inactive_portraits }}</div><div class="stat-label">Inactive</div></div>
        <div class="stat-card purple"><div class="stat-value">{{ data.total_tags }}</div><div class="stat-label">Tags Used</div></div>
      </div>
      <div class="analysis-grid mt-24">
        <div class="card">
          <h2 class="section-title">Gender Distribution</h2>
          <div v-if="!data.gender_distribution || data.gender_distribution.length === 0" class="empty-state"><p>No data</p></div>
          <div v-else class="dist-list">
            <div v-for="item in data.gender_distribution" :key="item.gender" class="dist-item">
              <span class="dist-label">{{ item.gender }}</span>
              <div class="dist-bar-wrap">
                <div class="dist-bar" :style="{ width: pct(item.count, data.total_portraits) + '%', background: '#4f46e5' }"></div>
              </div>
              <span class="dist-count">{{ item.count }}</span>
            </div>
          </div>
        </div>
        <div class="card">
          <h2 class="section-title">Age Distribution</h2>
          <div v-if="!data.age_distribution || data.age_distribution.length === 0" class="empty-state"><p>No data</p></div>
          <div v-else class="dist-list">
            <div v-for="item in data.age_distribution" :key="item.age_group" class="dist-item">
              <span class="dist-label">{{ item.age_group }}</span>
              <div class="dist-bar-wrap">
                <div class="dist-bar" :style="{ width: pct(item.count, data.total_portraits) + '%', background: '#06b6d4' }"></div>
              </div>
              <span class="dist-count">{{ item.count }}</span>
            </div>
          </div>
        </div>
        <div class="card">
          <h2 class="section-title">Top Locations</h2>
          <div v-if="!data.location_distribution || data.location_distribution.length === 0" class="empty-state"><p>No data</p></div>
          <div v-else class="dist-list">
            <div v-for="item in data.location_distribution" :key="item.location" class="dist-item">
              <span class="dist-label">{{ item.location }}</span>
              <div class="dist-bar-wrap">
                <div class="dist-bar" :style="{ width: pct(item.count, data.total_portraits) + '%', background: '#10b981' }"></div>
              </div>
              <span class="dist-count">{{ item.count }}</span>
            </div>
          </div>
        </div>
        <div class="card">
          <h2 class="section-title">Top Tags</h2>
          <div v-if="!data.top_tags || data.top_tags.length === 0" class="empty-state"><p>No data</p></div>
          <div v-else class="dist-list">
            <div v-for="item in data.top_tags" :key="item.name" class="dist-item">
              <span class="dist-label">{{ item.name }}</span>
              <div class="dist-bar-wrap">
                <div class="dist-bar" :style="{ width: pct(item.count, maxTagCount) + '%', background: '#f59e0b' }"></div>
              </div>
              <span class="dist-count">{{ item.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getAnalysis } from '@/api/analysis'
const data = ref({})
const loading = ref(true)
onMounted(async () => {
  const res = await getAnalysis()
  data.value = res.data
  loading.value = false
})
const maxTagCount = computed(() => Math.max(...(data.value.top_tags || []).map(t => t.count), 1))
function pct(val, total) { return total ? Math.round((val / total) * 100) : 0 }
</script>

<style scoped>
.grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
.stat-card { background: white; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #4f46e5; }
.stat-card.active { border-left-color: #10b981; }
.stat-card.warning { border-left-color: #f59e0b; }
.stat-card.purple { border-left-color: #8b5cf6; }
.stat-value { font-size: 36px; font-weight: 700; color: #4f46e5; }
.stat-card.active .stat-value { color: #10b981; }
.stat-card.warning .stat-value { color: #f59e0b; }
.stat-card.purple .stat-value { color: #8b5cf6; }
.stat-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
.analysis-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
.dist-list { display: flex; flex-direction: column; gap: 10px; }
.dist-item { display: flex; align-items: center; gap: 10px; }
.dist-label { width: 80px; font-size: 13px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex-shrink: 0; }
.dist-bar-wrap { flex: 1; background: #f3f4f6; border-radius: 4px; height: 8px; overflow: hidden; }
.dist-bar { height: 100%; border-radius: 4px; transition: width 0.5s; min-width: 2px; }
.dist-count { font-size: 13px; color: #9ca3af; width: 24px; text-align: right; flex-shrink: 0; }
@media (max-width: 768px) { .analysis-grid { grid-template-columns: 1fr; } }
</style>
