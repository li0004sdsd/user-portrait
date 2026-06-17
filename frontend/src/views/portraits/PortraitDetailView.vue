<template>
  <div class="container">
    <div class="flex-between">
      <RouterLink to="/portraits" class="btn btn-secondary">Back</RouterLink>
      <RouterLink v-if="portrait" :to="`/portraits/${portrait.id}/edit`" class="btn btn-primary">Edit</RouterLink>
    </div>
    <div v-if="loading" class="loading mt-24">Loading...</div>
    <div v-else-if="portrait" class="mt-24">
      <div class="card">
        <div class="detail-header">
          <div class="detail-avatar">{{ portrait.name.charAt(0).toUpperCase() }}</div>
          <div>
            <h1 class="detail-name">{{ portrait.name }}</h1>
            <span :class="['badge', portrait.status === 'active' ? 'badge-active' : 'badge-inactive']">{{ portrait.status }}</span>
          </div>
        </div>
        <div class="detail-grid mt-24">
          <div class="detail-field"><div class="field-label">Age</div><div class="field-value">{{ portrait.age || '—' }}</div></div>
          <div class="detail-field"><div class="field-label">Gender</div><div class="field-value">{{ portrait.gender || '—' }}</div></div>
          <div class="detail-field"><div class="field-label">Occupation</div><div class="field-value">{{ portrait.occupation || '—' }}</div></div>
          <div class="detail-field"><div class="field-label">Location</div><div class="field-value">{{ portrait.location || '—' }}</div></div>
          <div class="detail-field"><div class="field-label">Income Level</div><div class="field-value">{{ portrait.income_level ? '$' + Number(portrait.income_level).toLocaleString() : '—' }}</div></div>
        </div>
        <div class="detail-section mt-24" v-if="portrait.interests">
          <div class="field-label">Interests</div>
          <p class="field-text">{{ portrait.interests }}</p>
        </div>
        <div class="detail-section mt-16" v-if="portrait.pain_points">
          <div class="field-label">Pain Points</div>
          <p class="field-text">{{ portrait.pain_points }}</p>
        </div>
        <div class="detail-section mt-16" v-if="portrait.goals">
          <div class="field-label">Goals</div>
          <p class="field-text">{{ portrait.goals }}</p>
        </div>
        <div class="mt-24" v-if="portrait.tags && portrait.tags.length">
          <div class="field-label">Tags</div>
          <div class="tag-list mt-16">
            <span v-for="tag in portrait.tags" :key="tag.id" class="tag-chip" :style="{ background: tag.category?.color + '22', color: tag.category?.color }">{{ tag.name }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { usePortraitsStore } from '@/stores/portraits'
const route = useRoute()
const store = usePortraitsStore()
const portrait = ref(null)
const loading = ref(true)
onMounted(async () => {
  portrait.value = await store.fetchPortrait(route.params.id)
  loading.value = false
})
</script>

<style scoped>
.detail-header { display: flex; align-items: center; gap: 16px; }
.detail-avatar { width: 72px; height: 72px; border-radius: 50%; background: #4f46e5; color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; flex-shrink: 0; }
.detail-name { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
.badge-active { background: #dcfce7; color: #16a34a; }
.badge-inactive { background: #f3f4f6; color: #6b7280; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; }
.detail-field { background: #f9fafb; border-radius: 8px; padding: 12px; }
.field-label { font-size: 12px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-value { font-size: 15px; font-weight: 500; }
.field-text { font-size: 14px; color: #374151; margin-top: 8px; line-height: 1.6; }
.tag-list { display: flex; flex-wrap: wrap; gap: 8px; }
.tag-chip { padding: 4px 10px; border-radius: 999px; font-size: 13px; font-weight: 500; }
</style>
