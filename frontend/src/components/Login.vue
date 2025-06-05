<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../utils/api.js'
import { setAuthToken, setUserData, isAuthenticated } from '../utils/auth.js'

const router = useRouter()

if (isAuthenticated()){
  router.push('/admin')
}

const form = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await api.post('/login', form.value)
    
    if (response.data.token) {
      setAuthToken(response.data.token)
      setUserData(response.data.user)
      
      // Redirect based on role
      if (response.data.user.role === 'admin') {
        router.push('/admin')
      } else {
        router.push('/student')
      }
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Invalid credentials'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-container">
      <!-- Compact Header -->
      <div class="login-header">
        <router-link to="/" class="back-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Back
        </router-link>
        
        <div class="brand-section">
          <div class="brand-icon">A</div>
          <h1 class="login-title">Welcome back</h1>
          <p class="login-subtitle">Sign in to your Askify account</p>
        </div>
      </div>

      <!-- Compact Form -->
      <div class="login-form-container">
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <div class="input-container">
              <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input 
                id="email"
                v-model="form.email" 
                type="email" 
                class="form-input" 
                placeholder="Enter your email"
                required 
              />
            </div>
          </div>
          
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-container">
              <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="16" r="1" fill="currentColor"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input 
                id="password"
                v-model="form.password" 
                type="password" 
                class="form-input" 
                placeholder="Enter your password"
                required 
              />
            </div>
          </div>
          
          <div v-if="error" class="error-alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ error }}
          </div>
          
          <button type="submit" class="login-button" :disabled="loading">
            <div v-if="loading" class="loading-spinner"></div>
            <span>{{ loading ? 'Signing in...' : 'Sign in' }}</span>
          </button>
        </form>
        
        <div class="login-footer">
          <p class="footer-text">
            Don't have an account? 
            <router-link to="/signup" class="footer-link">Create one</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.login-page {
  min-height: 100vh;
  background: #fafbfc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 1rem 1rem 2rem 1rem;
  overflow-y: auto;
}

.login-container {
  width: 100%;
  max-width: 360px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.06);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  margin-top: 2rem;
}

/* Compact Header */
.login-header {
  padding: 1.25rem 1.5rem 1rem 1.5rem;
  text-align: center;
  border-bottom: 1px solid #f3f4f6;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  color: #6b7280;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 1rem;
  transition: color 0.2s ease;
}

.back-link:hover {
  color: #3b82f6;
}

.brand-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.brand-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 18px;
  margin-bottom: 0.25rem;
}

.login-title {
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin: 0;
  line-height: 1.2;
}

.login-subtitle {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
  line-height: 1.3;
}

/* Compact Form */
.login-form-container {
  padding: 1.5rem;
}

.login-form {
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-label {
  display: block;
  margin-bottom: 0.375rem;
  font-weight: 500;
  color: #374151;
  font-size: 13px;
}

.input-container {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 10px 10px 10px 34px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 15px;
  background: white;
  transition: all 0.2s ease;
  color: #111827;
  height: 40px;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
  font-size: 14px;
}

.error-alert {
  background: #fef2f2;
  color: #dc2626;
  padding: 10px;
  border-radius: 6px;
  font-size: 13px;
  margin-bottom: 1.25rem;
  border: 1px solid #fecaca;
  display: flex;
  align-items: center;
  gap: 6px;
  line-height: 1.3;
}

.login-button {
  width: 100%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  height: 42px;
}

.login-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-1px);
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Compact Footer */
.login-footer {
  text-align: center;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.footer-text {
  color: #6b7280;
  font-size: 13px;
  margin: 0;
  line-height: 1.4;
}

.footer-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.footer-link:hover {
  color: #2563eb;
  text-decoration: underline;
}

/* Mobile Optimized */
@media (max-width: 480px) {
  .login-page {
    padding: 0.75rem 0.75rem 1.5rem 0.75rem;
  }
  
  .login-container {
    max-width: 320px;
    margin-top: 1rem;
  }
  
  .login-header {
    padding: 1rem 1.25rem 0.75rem 1.25rem;
  }
  
  .login-form-container {
    padding: 1.25rem;
  }
  
  .login-title {
    font-size: 18px;
  }
  
  .login-subtitle {
    font-size: 13px;
  }
  
  .brand-icon {
    width: 36px;
    height: 36px;
    font-size: 16px;
  }
  
  .form-input {
    font-size: 16px; /* Prevents zoom on iOS */
  }
}

@media (max-width: 360px) {
  .login-container {
    max-width: 280px;
  }
  
  .login-header,
  .login-form-container {
    padding: 1rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
}

/* Landscape phones */
@media (max-height: 600px) and (orientation: landscape) {
  .login-page {
    align-items: flex-start;
    padding: 0.5rem;
  }
  
  .login-container {
    margin-top: 0.5rem;
  }
  
  .login-header {
    padding: 0.75rem 1.25rem 0.5rem 1.25rem;
  }
  
  .brand-section {
    gap: 0.25rem;
  }
  
  .back-link {
    margin-bottom: 0.5rem;
  }
}
</style>