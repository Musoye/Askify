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
  <div class="auth-page">
    <div class="background-elements">
      <div class="bg-circle bg-circle-1"></div>
      <div class="bg-circle bg-circle-2"></div>
      <div class="bg-circle bg-circle-3"></div>
    </div>
    
    <div class="container">
      <div class="auth-card">
        <div class="auth-header">
          <router-link to="/" class="back-link">
            <span class="back-icon">←</span>
            Back to Home
          </router-link>
          <div class="brand-section">
            <div class="brand-icon">🤖</div>
            <h2>Welcome Back</h2>
            <p>Sign in to your Askify account</p>
          </div>
        </div>
        
        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrapper">
              <span class="input-icon">📧</span>
              <input 
                id="email"
                v-model="form.email" 
                type="email" 
                class="form-control" 
                required 
                placeholder="Enter your email"
              />
            </div>
          </div>
          
          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <span class="input-icon">🔒</span>
              <input 
                id="password"
                v-model="form.password" 
                type="password" 
                class="form-control" 
                required 
                placeholder="Enter your password"
              />
            </div>
          </div>
          
          <div v-if="error" class="alert alert-error">
            <span class="alert-icon">⚠️</span>
            {{ error }}
          </div>
          
          <button type="submit" class="btn btn-full" :disabled="loading">
            <span v-if="loading" class="loading-spinner"></span>
            {{ loading ? 'Signing In...' : 'Sign In' }}
          </button>
        </form>
        
        <div class="auth-footer">
          <p>Don't have an account? <router-link to="/signup">Create one</router-link></p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 25%, #60a5fa 50%, #93c5fd 75%, #dbeafe 100%);
  position: relative;
  overflow: hidden;
}

.background-elements {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 0;
}

.bg-circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(20px);
}

.bg-circle-1 {
  width: 150px;
  height: 150px;
  top: 15%;
  left: 10%;
  animation: float 6s ease-in-out infinite;
}

.bg-circle-2 {
  width: 120px;
  height: 120px;
  top: 65%;
  right: 15%;
  animation: float 8s ease-in-out infinite reverse;
}

.bg-circle-3 {
  width: 80px;
  height: 80px;
  bottom: 25%;
  left: 20%;
  animation: float 7s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}

.container {
  max-width: 400px;
  width: 100%;
  position: relative;
  z-index: 1;
}

.auth-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.15),
    0 0 0 1px rgba(255, 255, 255, 0.2);
  padding: 1.75rem 1.5rem;
  width: 100%;
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
}

.auth-card:hover {
  transform: translateY(-3px);
  box-shadow: 
    0 25px 50px rgba(0, 0, 0, 0.2),
    0 0 0 1px rgba(255, 255, 255, 0.3);
}

.auth-header {
  text-align: center;
  margin-bottom: 1.25rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  color: #3b82f6;
  text-decoration: none;
  margin-bottom: 1rem;
  font-weight: 500;
  padding: 0.375rem 0.75rem;
  border-radius: 50px;
  background: rgba(59, 130, 246, 0.1);
  transition: all 0.3s ease;
  border: 1px solid rgba(59, 130, 246, 0.2);
  font-size: 0.9rem;
}

.back-link:hover {
  background: rgba(59, 130, 246, 0.15);
  transform: translateX(-2px);
}

.back-icon {
  font-size: 1.1rem;
  transition: transform 0.3s ease;
}

.back-link:hover .back-icon {
  transform: translateX(-2px);
}

.brand-section {
  margin-bottom: 0.5rem;
}

.brand-icon {
  font-size: 2.25rem;
  margin-bottom: 0.5rem;
  display: inline-block;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.auth-header h2 {
  color: #1e3a8a;
  margin-bottom: 0.25rem;
  font-size: 1.625rem;
  font-weight: 800;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.auth-header p {
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 500;
  margin: 0;
}

.auth-form {
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.375rem;
  color: #1e3a8a;
  font-weight: 600;
  font-size: 0.875rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 0.75rem;
  z-index: 2;
  font-size: 1rem;
  opacity: 0.7;
}

.form-control {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 2px solid rgba(59, 130, 246, 0.2);
  border-radius: 10px;
  font-size: 0.9rem;
  background: rgba(255, 255, 255, 0.9);
  transition: all 0.3s ease;
  color: #1e3a8a;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.form-control::placeholder {
  color: #94a3b8;
}

.alert {
  padding: 0.75rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  font-size: 0.875rem;
}

.alert-error {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.alert-icon {
  font-size: 1rem;
}

.btn-full {
  width: 100%;
  margin-top: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-full::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.btn-full:hover::before {
  left: 100%;
}

.btn-full:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}

.btn-full:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.auth-footer {
  text-align: center;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(59, 130, 246, 0.2);
}

.auth-footer p {
  color: #64748b;
  margin: 0;
  font-size: 0.9rem;
}

.auth-footer a {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.auth-footer a:hover {
  color: #1e3a8a;
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
  .auth-card {
    padding: 1.5rem 1.25rem;
    margin: 0.5rem;
  }
  
  .auth-header h2 {
    font-size: 1.5rem;
  }
  
  .brand-icon {
    font-size: 2rem;
  }
}

@media (max-width: 480px) {
  .auth-page {
    padding: 0.5rem;
  }
  
  .container {
    max-width: 350px;
  }
  
  .auth-card {
    padding: 1.25rem 1rem;
  }
  
  .auth-header h2 {
    font-size: 1.4rem;
  }
  
  .form-control {
    padding: 0.625rem 0.625rem 0.625rem 2.25rem;
    font-size: 0.875rem;
  }
  
  .input-icon {
    left: 0.625rem;
    font-size: 0.9rem;
  }
  
  .btn-full {
    padding: 0.75rem 1.25rem;
    font-size: 0.9rem;
  }
  
  .back-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.825rem;
  }
  
  .bg-circle-1 {
    width: 100px;
    height: 100px;
  }
  
  .bg-circle-2 {
    width: 80px;
    height: 80px;
  }
  
  .bg-circle-3 {
    width: 60px;
    height: 60px;
  }
}

@media (max-width: 360px) {
  .container {
    max-width: 300px;
  }
  
  .auth-card {
    padding: 1rem 0.75rem;
  }
}
</style>