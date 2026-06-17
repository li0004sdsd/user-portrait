import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as api from '@/api/portraits'

export const usePortraitsStore = defineStore('portraits', () => {
  const portraits = ref([])
  const pagination = ref({})
  const current = ref(null)
  const loading = ref(false)

  async function fetchPortraits(page = 1) {
    loading.value = true
    const { data } = await api.getPortraits(page)
    portraits.value = data.data
    pagination.value = { current_page: data.current_page, last_page: data.last_page, total: data.total }
    loading.value = false
  }

  async function fetchPortrait(id) {
    const { data } = await api.getPortrait(id)
    current.value = data
    return data
  }

  async function createPortrait(payload) {
    const { data } = await api.createPortrait(payload)
    portraits.value.unshift(data)
    return data
  }

  async function updatePortrait(id, payload) {
    const { data } = await api.updatePortrait(id, payload)
    const idx = portraits.value.findIndex(p => p.id === id)
    if (idx !== -1) portraits.value[idx] = data
    current.value = data
    return data
  }

  async function deletePortrait(id) {
    await api.deletePortrait(id)
    portraits.value = portraits.value.filter(p => p.id !== id)
  }

  return { portraits, pagination, current, loading, fetchPortraits, fetchPortrait, createPortrait, updatePortrait, deletePortrait }
})
