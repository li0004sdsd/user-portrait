<template>
  <div class="container">
    <h1 class="page-title">Marketing Recommendations</h1>
    <div v-if="loading" class="loading">Loading recommendations...</div>
    <div v-else-if="recommendations.length === 0" class="empty-state">
      <h3>No active portraits</h3>
      <p>Create active user portraits to get marketing recommendations.</p>
      <RouterLink to="/portraits/create" class="btn btn-primary mt-16">Create Portrait</RouterLink>
    </div>
    <div v-else class="rec-grid">
      <div v-for="rec in recommendations" :key="rec.portrait_id" class="rec-card">
        <div class="rec-header">
          <div class="rec-avatar">{{ rec.portrait_name.charAt(0).toUpperCase() }}</div>
          <div>
            <div class="rec-name">{{ rec.portrait_name }}</div>
            <div class="score-bar">
              <div class="score-fill" :style="{ width: rec.score + '%' }"></div>
            </div>
            <div class="score-label">Score: {{ rec.score }}/100</div>
          </div>
        </div>
        <div class="rec-section">
          <div class="rec-label">Recommended Channels</div>
          <div class="chip-row">
            <span v-for="ch in rec.channels" :key="ch" class="chip chip-blue">{{ ch }}</span>
          </div>
        </div>
        <div class="rec-section">
          <div class="rec-label">Content Types</div>
          <div class="chip-row">
            <span v-for="ct in rec.content_types" :key="ct" class="chip chip-green">{{ ct }}</span>
          </div>
        </div>
        <div class="rec-section">
          <div class="rec-label">Best Timing</div>
          <div class="chip-row">
            <span v-for="t in rec.best_timing" :key="t" class="chip chip-amber">{{ t }}</span>
          </div>
        </div>
        <RouterLink :to="`/portraits/${rec.portrait_id}`" class="btn btn-secondary btn-sm mt-16">View Portrait</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getRecommendations } from '@/api/recommendations'
const recommendations = ref([])
const loading = ref(true)
onMounted(async () => {
  const res = await getRecommendations()
  recommendations.value = res.data
  loading.value = false
})
</script>

<style scoped>
.rec-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
.rec-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.rec-header { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 20px; }
.rec-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; flex-shrink: 0; }
.rec-name { font-size: 16px; font-weight: 600; margin-bottom: 6px; }
.score-bar { height: 6px; background: #f3f4f6; border-radius: 3px; width: 160px; overflow: hidden; margin-bottom: 4px; }
.score-fill { height: 100%; background: linear-gradient(90deg, #4f46e5, #7c3aed); border-radius: 3px; }
.score-label { font-size: 12px; color: #9ca3af; }
.rec-section { margin-bottom: 14px; }
.rec-label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.chip-row { display: flex; flex-wrap: wrap; gap: 6px; }
.chip { padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; }
.chip-blue { background: #dbeafe; color: #1d4ed8; }
.chip-green { background: #dcfce7; color: #15803d; }
.chip-amber { background: #fef3c7; color: #b45309; }
</style>
