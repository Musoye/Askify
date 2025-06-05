<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../utils/api.js'
import { setAuthToken, setUserData, isAuthenticated } from '../utils/auth.js'

const router = useRouter()

if (isAuthenticated()){
  router.push('/admin')
}

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'student' // Default to student
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const isScrolled = ref(false)

const handleSignUp = async () => {
  loading.value = true
  error.value = ''
  success.value = ''
  
  try {
    const response = await api.post('/register', form.value)
    
    if (response.data.token) {
      setAuthToken(response.data.token)
      setUserData(response.data.user)
      
      success.value = 'Account created successfully!'
      
      // Redirect based on role
      setTimeout(() => {
        if (response.data.user.role === 'admin') {
          router.push('/admin')
        } else {
          router.push('/student')
        }
      }, 1000)
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create account'
  } finally {
    loading.value = false
  }
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  // Check initial scroll position
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <div class="signup-page" :class="{ 'scrolled': isScrolled }">
    <div class="signup-container" :class="{ 'compact': isScrolled }">
      <!-- Header Section -->
      <div class="signup-header">
        <router-link to="/" class="back-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Back to Home
        </router-link>
        
        <div class="brand-section">
          <div class="brand-icon">A</div>
          <h1 class="signup-title">Create your account</h1>
          <p class="signup-subtitle">Join Askify and start learning</p>
        </div>
      </div>

      <!-- Signup Form -->
      <div class="signup-form-container">
        <form @submit.prevent="handleSignUp" class="signup-form">
          <div class="form-group">
            <label for="name" class="form-label">Full name</label>
            <div class="input-container">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input 
                id="name"
                v-model="form.name" 
                type="text" 
                class="form-input" 
                placeholder="Enter your full name"
                required 
              />
            </div>
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email address</label>
            <div class="input-container">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
          
          <!-- Password and Role on the same line -->
          <div class="form-row">
            <div class="form-group form-group-half">
              <label for="password" class="form-label">Password</label>
              <div class="input-container">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="12" cy="16" r="1" fill="currentColor"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input 
                  id="password"
                  v-model="form.password" 
                  type="password" 
                  class="form-input" 
                  placeholder="Password"
                  required 
                />
              </div>
            </div>

            <div class="form-group form-group-half">
              <label for="role" class="form-label">Account type</label>
              <div class="input-container">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 14l9-5-9-5-9 5 9 5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <select id="role" v-model="form.role" class="form-input form-select" required>
                  <option value="student">Student</option>
                  <option value="admin">Teacher/Admin</option>
                </select>
              </div>
            </div>
          </div>
          
          <div v-if="success" class="success-alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <polyline points="22,4 12,14.01 9,11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ success }}
          </div>

          <div v-if="error" class="error-alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ error }}
          </div>
          
          <button type="submit" class="signup-button" :disabled="loading">
            <div v-if="loading" class="loading-spinner"></div>
            <span>{{ loading ? 'Creating account...' : 'Create account' }}</span>
          </button>
        </form>
        
        <div class="signup-footer">
          <p class="footer-text">
            Already have an account? 
            <router-link to="/login" class="footer-link">Sign in</router-link>
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

.signup-page {
  min-height: 100vh;
  background: #fafbfc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem 1rem;
  transition: padding 0.3s ease;
}

.signup-page.scrolled {
  padding: 1rem;
  align-items: flex-start;
  padding-top: 1rem;
}

.signup-container {
  width: 100%;
  max-width: 420px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  transition: all 0.3s ease;
}

.signup-container.compact {
  border-radius: 12px;
  box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
}

/* Header Section */
.signup-header {
  padding: 1.5rem 2rem 1.25rem 2rem;
  text-align: center;
  border-bottom: 1px solid #f3f4f6;
  transition: padding 0.3s ease;
}

.compact .signup-header {
  padding: 1rem 1.5rem 0.75rem 1.5rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 1.25rem;
  transition: all 0.3s ease;
}

.compact .back-link {
  margin-bottom: 0.75rem;
  font-size: 13px;
}

.back-link:hover {
  color: #3b82f6;
}

.brand-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  transition: gap 0.3s ease;
}

.compact .brand-section {
  gap: 0.375rem;
}

.brand-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 18px;
  margin-bottom: 0.25rem;
  transition: all 0.3s ease;
}

.compact .brand-icon {
  width: 36px;
  height: 36px;
  font-size: 16px;
  margin-bottom: 0.125rem;
}

.signup-title {
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  margin: 0;
  transition: font-size 0.3s ease;
}

.compact .signup-title {
  font-size: 18px;
}

.signup-subtitle {
  font-size: 15px;
  color: #6b7280;
  margin: 0;
  transition: font-size 0.3s ease;
}

.compact .signup-subtitle {
  font-size: 13px;
}

/* Form Section */
.signup-form-container {
  padding: 1.5rem 2rem;
  transition: padding 0.3s ease;
}

.compact .signup-form-container {
  padding: 1.25rem 1.5rem;
}

.signup-form {
  margin-bottom: 1.25rem;
  transition: margin 0.3s ease;
}

.compact .signup-form {
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1.25rem;
  transition: margin 0.3s ease;
}

.compact .form-group {
  margin-bottom: 1rem;
}

/* Form row for inline fields */
.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.compact .form-row {
  margin-bottom: 1rem;
  gap: 0.75rem;
}

.form-group-half {
  flex: 1;
  margin-bottom: 0;
}

.form-label {
  display: block;
  margin-bottom: 0.375rem;
  font-weight: 500;
  color: #374151;
  font-size: 13px;
  transition: all 0.3s ease;
}

.compact .form-label {
  margin-bottom: 0.25rem;
  font-size: 12px;
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
  transition: left 0.3s ease;
}

.compact .input-icon {
  left: 8px;
}

.form-input {
  width: 100%;
  padding: 10px 10px 10px 32px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 15px;
  background: white;
  transition: all 0.3s ease;
  color: #111827;
}

.compact .form-input {
  padding: 8px 8px 8px 28px;
  border-radius: 6px;
  font-size: 14px;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
}

.form-select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 10px center;
  background-repeat: no-repeat;
  background-size: 16px 12px;
  padding-right: 32px;
  transition: all 0.3s ease;
}

.compact .form-select {
  background-position: right 8px center;
  padding-right: 28px;
}

.success-alert {
  background: #f0fdf4;
  color: #16a34a;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 1.25rem;
  border: 1px solid #bbf7d0;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.3s ease;
}

.compact .success-alert {
  padding: 8px;
  font-size: 12px;
  margin-bottom: 1rem;
  border-radius: 6px;
}

.error-alert {
  background: #fef2f2;
  color: #dc2626;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 1.25rem;
  border: 1px solid #fecaca;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.3s ease;
}

.compact .error-alert {
  padding: 8px;
  font-size: 12px;
  margin-bottom: 1rem;
  border-radius: 6px;
}

.signup-button {
  width: 100%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 11px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 44px;
}

.compact .signup-button {
  padding: 9px 16px;
  border-radius: 6px;
  font-size: 14px;
  min-height: 40px;
}

.signup-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-1px);
}

.signup-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Footer */
.signup-footer {
  text-align: center;
  padding-top: 1.25rem;
  border-top: 1px solid #f3f4f6;
  transition: padding 0.3s ease;
}

.compact .signup-footer {
  padding-top: 1rem;
}

.footer-text {
  color: #6b7280;
  font-size: 13px;
  margin: 0;
  transition: font-size 0.3s ease;
}

.compact .footer-text {
  font-size: 12px;
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

/* Responsive Design */
@media (max-width: 480px) {
  .signup-page {
    padding: 1rem 0.5rem;
  }
  
  .signup-page.scrolled {
    padding: 0.5rem;
  }
  
  .signup-container {
    max-width: 370px;
  }
  
  .signup-header {
    padding: 1.25rem 1.5rem 1rem 1.5rem;
  }
  
  .compact .signup-header {
    padding: 1rem 1.25rem 0.75rem 1.25rem;
  }
  
  .signup-form-container {
    padding: 1.25rem 1.5rem;
  }
  
  .compact .signup-form-container {
    padding: 1rem 1.25rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 1rem;
  }
  
  .compact .form-row {
    gap: 0.75rem;
  }
  
  .form-group-half {
    margin-bottom: 0;
  }
  
  .signup-title {
    font-size: 18px;
  }
  
  .compact .signup-title {
    font-size: 16px;
  }
  
  .signup-subtitle {
    font-size: 13px;
  }
  
  .compact .signup-subtitle {
    font-size: 12px;
  }
  
  .brand-icon {
    width: 36px;
    height: 36px;
    font-size: 16px;
  }
  
  .compact .brand-icon {
    width: 32px;
    height: 32px;
    font-size: 14px;
  }
  
  .form-input {
    font-size: 16px; /* Prevents zoom on iOS */
  }
  
  .compact .form-input {
    font-size: 15px;
  }
}

@media (max-width: 360px) {
  .signup-container {
    max-width: 320px;
  }
  
  .signup-header,
  .signup-form-container {
    padding: 1rem 1.25rem;
  }
  
  .compact .signup-header,
  .compact .signup-form-container {
    padding: 0.75rem 1rem;
  }
}

/* Alternative approach using CSS media queries for viewport height */
@media (max-height: 700px) {
  .signup-page {
    padding: 1rem;
    align-items: flex-start;
  }
  
  .signup-container {
    border-radius: 12px;
  }
  
  .signup-header {
    padding: 1rem 1.5rem 0.75rem 1.5rem;
  }
  
  .signup-form-container {
    padding: 1.25rem 1.5rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
  
  .brand-icon {
    width: 36px;
    height: 36px;
    font-size: 16px;
  }
  
  .signup-title {
    font-size: 18px;
  }
  
  .signup-subtitle {
    font-size: 13px;
  }
}
</style>