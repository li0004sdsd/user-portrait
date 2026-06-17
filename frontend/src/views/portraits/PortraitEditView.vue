<template>
  <div class="container">
    <div class="flex-between">
      <h1 class="page-title">Edit Portrait</h1>
      <RouterLink :to="`/portraits/${route.params.id}`" class="btn btn-secondary">Cancel</RouterLink>
    </div>
    <div v-if="loadingPortrait" class="loading mt-24">Loading...</div>
    <div v-else-if="initial" class="card mt-16">
      <PortraitForm :tags-store="tagsStore" :initial="initial" @submit="handleUpdate" :loading="saving" :error="error" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePortraitsStore } from '@/stores/portraits'
import { useTagsStore } from '@/stores/tags'
import PortraitForm from '@/components/PortraitForm.vue'
const route = useRoute()
const router = useRouter()
const store = usePortraitsStore()
const tagsStore = useTagsStore()
const initial = ref(null)
const loadingPortrait = ref(true)
const saving = ref(false)
const error = ref('')
onMounted(async () => {
  await Promise.all([tagsStore.fetchCategories(), tagsStore.fetchTags()])
  initial.value = await store.fetchPortrait(route.params.id)
  loadingPortrait.value = false
})
async function handleUpdate(data) {
  error.value = ''
  saving.value = true
  try {
    await store.updatePortrait(route.params.id, data)
    router.push(`/portraits/${route.params.id}`)
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to update portrait'
  } finally {
    saving.value = false
  }
}
</script>
