<template>
  <div class="container">
    <h1 class="page-title">Tag Management</h1>
    <div class="tag-layout">
      <div class="card">
        <div class="flex-between">
          <h2 class="section-title">Categories</h2>
          <button class="btn btn-primary btn-sm" @click="showCategoryForm = !showCategoryForm">+ Category</button>
        </div>
        <form v-if="showCategoryForm" @submit.prevent="saveCategory" class="inline-form mt-16">
          <div class="form-group">
            <label class="form-label">Name</label>
            <input v-model="catForm.name" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Color</label>
            <input v-model="catForm.color" type="color" class="color-input" />
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <input v-model="catForm.description" class="form-input" />
          </div>
          <div class="flex gap-8">
            <button type="submit" class="btn btn-primary btn-sm">{{ editingCat ? 'Update' : 'Create' }}</button>
            <button type="button" class="btn btn-secondary btn-sm" @click="cancelCat">Cancel</button>
          </div>
        </form>
        <div class="mt-16">
          <div v-if="tagsStore.loading" class="loading">Loading...</div>
          <div v-else-if="tagsStore.categories.length === 0" class="empty-state"><p>No categories yet.</p></div>
          <div v-else>
            <div v-for="cat in tagsStore.categories" :key="cat.id" :class="['cat-item', selectedCat?.id === cat.id ? 'cat-item-active' : '']" @click="selectCat(cat)">
              <div class="cat-dot" :style="{ background: cat.color }"></div>
              <div class="cat-name">{{ cat.name }}</div>
              <div class="cat-count">{{ (cat.tags || []).length }}</div>
              <div class="cat-actions" @click.stop>
                <button class="btn btn-sm btn-secondary" @click="editCat(cat)">Edit</button>
                <button class="btn btn-sm btn-danger" @click="removeCat(cat.id)">Del</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="flex-between">
          <h2 class="section-title">Tags{{ selectedCat ? ' — ' + selectedCat.name : '' }}</h2>
          <button class="btn btn-primary btn-sm" :disabled="!selectedCat" @click="showTagForm = !showTagForm">+ Tag</button>
        </div>
        <form v-if="showTagForm && selectedCat" @submit.prevent="saveTag" class="inline-form mt-16">
          <div class="form-group">
            <label class="form-label">Tag Name</label>
            <input v-model="tagForm.name" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Value</label>
            <input v-model="tagForm.value" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">Weight (1-10)</label>
            <input v-model.number="tagForm.weight" type="number" min="1" max="10" class="form-input" />
          </div>
          <div class="flex gap-8">
            <button type="submit" class="btn btn-primary btn-sm">{{ editingTag ? 'Update' : 'Create' }}</button>
            <button type="button" class="btn btn-secondary btn-sm" @click="cancelTag">Cancel</button>
          </div>
        </form>
        <div class="mt-16">
          <div v-if="!selectedCat" class="empty-state"><p>Select a category to manage tags.</p></div>
          <div v-else-if="catTags.length === 0" class="empty-state"><p>No tags in this category.</p></div>
          <div v-else>
            <div v-for="tag in catTags" :key="tag.id" class="tag-row">
              <span class="tag-chip" :style="{ background: selectedCat.color + '22', color: selectedCat.color }">{{ tag.name }}</span>
              <span class="text-muted">{{ tag.value || '' }}</span>
              <span class="weight-badge">w{{ tag.weight }}</span>
              <div class="flex gap-8">
                <button class="btn btn-sm btn-secondary" @click="editTag(tag)">Edit</button>
                <button class="btn btn-sm btn-danger" @click="removeTag(tag.id)">Del</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useTagsStore } from '@/stores/tags'
const tagsStore = useTagsStore()
onMounted(() => { tagsStore.fetchCategories(); tagsStore.fetchTags() })

const selectedCat = ref(null)
const showCategoryForm = ref(false)
const showTagForm = ref(false)
const editingCat = ref(null)
const editingTag = ref(null)
const catForm = ref({ name: '', color: '#4f46e5', description: '' })
const tagForm = ref({ name: '', value: '', weight: 1 })

const catTags = computed(() => selectedCat.value ? tagsStore.tags.filter(t => t.tag_category_id === selectedCat.value.id) : [])

function selectCat(cat) { selectedCat.value = cat; showTagForm.value = false }
function editCat(cat) { editingCat.value = cat; catForm.value = { name: cat.name, color: cat.color, description: cat.description || '' }; showCategoryForm.value = true }
function cancelCat() { showCategoryForm.value = false; editingCat.value = null; catForm.value = { name: '', color: '#4f46e5', description: '' } }
async function saveCategory() {
  if (editingCat.value) { await tagsStore.updateCategory(editingCat.value.id, catForm.value) }
  else { await tagsStore.createCategory(catForm.value) }
  cancelCat()
}
async function removeCat(id) { if (confirm('Delete this category and all its tags?')) { await tagsStore.deleteCategory(id); if (selectedCat.value?.id === id) selectedCat.value = null } }

function editTag(tag) { editingTag.value = tag; tagForm.value = { name: tag.name, value: tag.value || '', weight: tag.weight }; showTagForm.value = true }
function cancelTag() { showTagForm.value = false; editingTag.value = null; tagForm.value = { name: '', value: '', weight: 1 } }
async function saveTag() {
  const payload = { ...tagForm.value, tag_category_id: selectedCat.value.id }
  if (editingTag.value) { await tagsStore.updateTag(editingTag.value.id, payload) }
  else { await tagsStore.createTag(payload) }
  cancelTag()
}
async function removeTag(id) { if (confirm('Delete this tag?')) await tagsStore.deleteTag(id) }
</script>

<style scoped>
.tag-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.section-title { font-size: 16px; font-weight: 600; }
.cat-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; cursor: pointer; transition: background 0.15s; }
.cat-item:hover { background: #f9fafb; }
.cat-item-active { background: #eef2ff; }
.cat-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.cat-name { flex: 1; font-size: 14px; font-weight: 500; }
.cat-count { font-size: 12px; color: #9ca3af; margin-right: 8px; }
.cat-actions { display: flex; gap: 4px; opacity: 0; transition: opacity 0.15s; }
.cat-item:hover .cat-actions { opacity: 1; }
.tag-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.tag-chip { padding: 3px 10px; border-radius: 999px; font-size: 13px; font-weight: 500; }
.weight-badge { font-size: 11px; background: #f3f4f6; color: #6b7280; padding: 2px 6px; border-radius: 4px; margin-left: auto; }
.inline-form { background: #f9fafb; border-radius: 8px; padding: 16px; }
.color-input { width: 60px; height: 36px; border: 1.5px solid #e5e7eb; border-radius: 6px; padding: 2px; cursor: pointer; }
@media (max-width: 768px) { .tag-layout { grid-template-columns: 1fr; } }
</style>
