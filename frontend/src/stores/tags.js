import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as api from '@/api/tags'

export const useTagsStore = defineStore('tags', () => {
  const categories = ref([])
  const tags = ref([])
  const loading = ref(false)

  async function fetchCategories() {
    loading.value = true
    const { data } = await api.getCategories()
    categories.value = data
    loading.value = false
  }

  async function fetchTags() {
    const { data } = await api.getTags()
    tags.value = data
  }

  async function createCategory(payload) {
    const { data } = await api.createCategory(payload)
    categories.value.push(data)
    return data
  }

  async function updateCategory(id, payload) {
    const { data } = await api.updateCategory(id, payload)
    const idx = categories.value.findIndex(c => c.id === id)
    if (idx !== -1) categories.value[idx] = data
    return data
  }

  async function deleteCategory(id) {
    await api.deleteCategory(id)
    categories.value = categories.value.filter(c => c.id !== id)
  }

  async function createTag(payload) {
    const { data } = await api.createTag(payload)
    tags.value.push(data)
    const cat = categories.value.find(c => c.id === data.tag_category_id)
    if (cat) cat.tags = [...(cat.tags || []), data]
    return data
  }

  async function updateTag(id, payload) {
    const { data } = await api.updateTag(id, payload)
    const idx = tags.value.findIndex(t => t.id === id)
    if (idx !== -1) tags.value[idx] = data
    return data
  }

  async function deleteTag(id) {
    await api.deleteTag(id)
    tags.value = tags.value.filter(t => t.id !== id)
    categories.value.forEach(c => {
      if (c.tags) c.tags = c.tags.filter(t => t.id !== id)
    })
  }

  return { categories, tags, loading, fetchCategories, fetchTags, createCategory, updateCategory, deleteCategory, createTag, updateTag, deleteTag }
})
