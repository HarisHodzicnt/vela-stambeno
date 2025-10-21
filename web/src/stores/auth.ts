import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '@/api/client'
import type { User, LoginCredentials, RegisterData } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isOwner = computed(() => user.value?.isOwner || false)
  const isVerified = computed(() => user.value?.isVerified || false)

  // Actions
  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await api.get<User>('/me')
      user.value = response
      localStorage.setItem('user', JSON.stringify(response))
    } catch (err) {
      console.error('Failed to fetch user:', err)
      // This might happen if the token is expired, so we log out
      logout()
    }
  }

  async function login(credentials: LoginCredentials) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post<{ token: string }>('/login_check', credentials)
      token.value = response.token
      localStorage.setItem('token', response.token)
      // After getting the token, fetch the user data
      await fetchUser()
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Prijava nije uspjela. Provjerite email i lozinku.'
      logout() // Clear any invalid state
      throw err
    } finally {
      loading.value = false
    }
  }

  async function register(data: RegisterData) {
    loading.value = true
    error.value = null
    try {
      // Transform password to plainPassword for the API
      const { password, ...rest } = data
      const apiData = { ...rest, plainPassword: password }
      
      // First, create the user
      await api.post<User>('/users', apiData)
      // Then, automatically log them in
      await login({ email: data.email, password: data.password })
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registracija nije uspjela. Molimo pokušajte ponovo.'
      logout() // Clear any invalid state
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    // No API call needed for stateless JWT logout
  }

  async function updateProfile(data: Partial<User>) {
    loading.value = true
    error.value = null
    try {
      const response = await api.patch<User>('/users/me', data)
      user.value = response
      localStorage.setItem('user', JSON.stringify(response))
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ažuriranje profila nije uspjelo.'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Initialize from localStorage on app startup
  function initialize() {
    const storedUser = localStorage.getItem('user')
    if (storedUser && token.value) {
      try {
        user.value = JSON.parse(storedUser)
        // Fetch fresh user data in background to ensure it's up-to-date
        fetchUser()
      } catch (err) {
        console.error('Failed to parse stored user:', err)
        logout()
      }
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    isOwner,
    isVerified,
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
    initialize
  }
})
