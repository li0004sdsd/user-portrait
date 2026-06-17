<template>
  <form @submit.prevent="handleSubmit">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Name *</label>
        <input v-model="form.name" type="text" class="form-input" required />
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select v-model="form.status" class="form-select">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Age</label>
        <input v-model.number="form.age" type="number" class="form-input" min="1" max="120" />
      </div>
      <div class="form-group">
        <label class="form-label">Gender</label>
        <select v-model="form.gender" class="form-select">
          <option value="">Select...</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
          <option value="Prefer not to say">Prefer not to say</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Occupation</label>
        <input v-model="form.occupation" type="text" class="form-input" />
      </div>
      <div class="form-group">
        <label class="form-label">Location</label>
        <input v-model="form.location" type="text" class="form-input" />
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Income Level (annual)</label>
      <input v-model.number="form.income_level" type="number" class="form-input" min="0" />
    </div>
    <div class="form-group">
      <label class="form-label">Interests</label>
      <textarea v-model="form.interests" class="form-textarea"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Pain Points</label>
      <textarea v-model="form.pain_points" class="form-textarea"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Goals</label>
      <textarea v-model="form.goals" class="form-textarea"></textarea>
    </div>
    <div class="form-group" v-if="tagsStore.tags.length">
      <label class="form-label">Tags</label>
      <div class="tag-select">
        <div v-for="cat in tagsStore.categories" :key="cat.id" class="tag-group">
          <div class="tag-group-name" :style="{ color: cat.color }">{{ cat.name }}</div>
          <div class="tag-options">
            <label v-for="tag in (cat.tags || []).filter(t => tagsStore.tags.find(tt => tt.id === t.id))" :key="tag.id" class="tag-option">
              <input type="checkbox" :value="tag.id" v-model="form.tag_ids" />
              <span class="tag-chip" :style="{ background: cat.color + '22', color: cat.color }">{{ tag.name }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>
    <p v-if="error" class="error-msg">{{ error }}</p>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary" :disabled="loading">{{ loading ? 'Saving...' : 'Save Portrait' }}</button>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
const props = defineProps({ initial: Object, tagsStore: Object, loading: Boolean, error: String })
const emit = defineEmits(['submit'])
const form = ref({
  name: '', age: null, gender: '', occupation: '', location: '',
  income_level: null, interests: '', pain_points: '', goals: '',
  status: 'active', tag_ids: []
})
watch(() => props.initial, (val) => {
  if (val) {
    form.value = {
      name: val.name || '', age: val.age || null, gender: val.gender || '',
      occupation: val.occupation || '', location: val.location || '',
      income_level: val.income_level || null, interests: val.interests || '',
      pain_points: val.pain_points || '', goals: val.goals || '',
      status: val.status || 'active',
      tag_ids: (val.tags || []).map(t => t.id)
    }
  }
}, { immediate: true })
function handleSubmit() { emit('submit', { ...form.value }) }
</script>

<style scoped>
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.tag-select { border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 12px; max-height: 240px; overflow-y: auto; }
.tag-group { margin-bottom: 12px; }
.tag-group-name { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.tag-options { display: flex; flex-wrap: wrap; gap: 6px; }
.tag-option { display: flex; align-items: center; gap: 4px; cursor: pointer; }
.tag-option input { display: none; }
.tag-chip { padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; cursor: pointer; border: 2px solid transparent; }
.tag-option input:checked + .tag-chip { border-color: currentColor; }
.form-actions { display: flex; justify-content: flex-end; margin-top: 24px; }
</style>
