import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { login as apiLogin, register as apiRegister, logout as apiLogout, getProfile } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
  const token = ref(localStorage.getItem('auth_token') || null)
  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials) {
    const { data } = await apiLogin(credentials)
    user.value = data.user
    token.value = data.token
    localStorage.setItem('auth_token', data.token)
    localStorage.setItem('auth_user', JSON.stringify(data.user))
  }

  async function register(payload) {
    const { data } = await apiRegister(payload)
    user.value = data.user
    token.value = data.token
    localStorage.setItem('auth_token', data.token)
    localStorage.setItem('auth_user', JSON.stringify(data.user))
  }

  async function logout() {
    try { await apiLogout() } catch(e) {}
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
  }

  async function refreshProfile() {
    const { data } = await getProfile()
    user.value = data
    localStorage.setItem('auth_user', JSON.stringify(data))
  }

  return { user, token, isAuthenticated, login, register, logout, refreshProfile }
})
