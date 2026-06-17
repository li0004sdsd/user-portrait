<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1 class="auth-title">Create account</h1>
      <p class="auth-sub">User Portrait Marketing System</p>
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label class="form-label">Name</label>
          <input v-model="form.name" type="text" class="form-input" placeholder="Your name" required />
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-input" placeholder="you@example.com" required />
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input v-model="form.password" type="password" class="form-input" placeholder="Min 8 characters" required />
        </div>
        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="form-input" placeholder="••••••••" required />
        </div>
        <p v-if="error" class="error-msg">{{ error }}</p>
        <button type="submit" class="btn btn-primary full-width" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create account' }}
        </button>
      </form>
      <p class="auth-footer">Have an account? <RouterLink to="/login">Sign in</RouterLink></p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
const router = useRouter()
const authStore = useAuthStore()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)
async function handleRegister() {
  error.value = ''
  loading.value = true
  try {
    await authStore.register(form.value)
    router.push('/dashboard')
  } catch (e) {
    const errs = e.response?.data?.errors
    error.value = errs ? Object.values(errs).flat()[0] : (e.response?.data?.message || 'Registration failed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%); }
.auth-card { background: white; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
.auth-title { font-size: 28px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
.auth-sub { color: #6b7280; font-size: 14px; margin-bottom: 28px; }
.full-width { width: 100%; justify-content: center; padding: 12px; font-size: 15px; }
.auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
.auth-footer a { color: #4f46e5; text-decoration: none; font-weight: 500; }
</style>
