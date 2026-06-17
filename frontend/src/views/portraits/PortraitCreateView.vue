<template>
  <div class="container">
    <div class="flex-between">
      <h1 class="page-title">New Portrait</h1>
      <RouterLink to="/portraits" class="btn btn-secondary">Cancel</RouterLink>
    </div>
    <div class="card mt-16">
      <PortraitForm :tags-store="tagsStore" @submit="handleCreate" :loading="loading" :error="error" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { usePortraitsStore } from '@/stores/portraits'
import { useTagsStore } from '@/stores/tags'
import PortraitForm from '@/components/PortraitForm.vue'
const router = useRouter()
const store = usePortraitsStore()
const tagsStore = useTagsStore()
const loading = ref(false)
const error = ref('')
onMounted(() => { tagsStore.fetchCategories(); tagsStore.fetchTags() })
async function handleCreate(data) {
  error.value = ''
  loading.value = true
  try {
    const portrait = await store.createPortrait(data)
    router.push(`/portraits/${portrait.id}`)
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to create portrait'
  } finally {
    loading.value = false
  }
}
</script>
