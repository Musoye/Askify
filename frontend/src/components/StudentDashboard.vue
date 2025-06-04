<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../utils/api.js'
import { getUserData, removeAuthToken, removeUserData, isAuthenticated, isAdmin } from '../utils/auth.js'


const router = useRouter()

if (!isAuthenticated()){
  router.push('/login')
}

if (isAdmin()){
  router.push('/admin')
}

const userData = ref(getUserData())
const documents = ref([])
const loading = ref(true)

onMounted(async () => {
  await fetchDocuments()
})

const fetchDocuments = async () => {
  try {
    loading.value = true
    const response = await api.get('/documents')
    documents.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch documents:', error)
  } finally {
    loading.value = false
  }
}

const getFileExtension = (filePath) => {
  if (!filePath) return 'FILE'
  const extension = filePath.split('.').pop().toUpperCase()
  return extension
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const handleLogout = async () => {
  try {
    await api.get('/logout')
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    removeAuthToken()
    removeUserData()
    router.push('/')
  }
}
</script>

<template>
  <div class="dashboard">
    <!-- Navigation -->
    <nav class="nav">
      <div class="nav-container">
        <div class="nav-brand">
          <div class="brand-icon">A</div>
          <span class="brand-text">Askify</span>
        </div>
        <div class="nav-user">
          <span class="user-name">{{ userData?.name }}</span>
          <button @click="handleLogout" class="logout-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main">
      <div class="container">
        <!-- Header -->
        <div class="header">
          <div>
            <h1 class="title">Available Documents</h1>
            <p class="subtitle">Click on any document to read it or ask questions about it</p>
          </div>
        </div>

        <!-- Content -->
        <div class="content">
          <div v-if="loading" class="loading">
            <div class="loading-spinner"></div>
            <span>Loading documents...</span>
          </div>
          
          <div v-else-if="documents.length === 0" class="empty">
            <div class="empty-icon">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3 class="empty-title">No documents available</h3>
            <p class="empty-text">Your teacher hasn't uploaded any documents yet</p>
          </div>

          <div v-else class="documents-grid">
            <div v-for="document in documents" :key="document.id" class="document-card">
              <div class="card-header">
                <h3 class="card-title">{{ document.title }}</h3>
                <div class="file-badge">
                  {{ getFileExtension(document.file_path) }}
                </div>
              </div>
              <p v-if="document.description" class="card-description">
                {{ document.description }}
              </p>
              <div class="card-meta">
                <span class="card-date">{{ formatDate(document.created_at) }}</span>
              </div>
              <div class="card-actions">
                <router-link 
                  :to="`/document/${document.id}`" 
                  class="btn-secondary"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Read Document
                </router-link>
                <router-link 
                  :to="`/chat/${document.id}`" 
                  class="btn-primary"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Ask Questions
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.dashboard {
  min-height: 100vh;
  background: #fafbfc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

/* Navigation */
.nav {
  background: white;
  border-bottom: 1px solid #e1e8ed;
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-icon {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
}

.brand-text {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.nav-user {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
}

.logout-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.logout-btn:hover {
  color: #3b82f6;
  background: #f3f4f6;
}

/* Main Content */
.main {
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.header {
  margin-bottom: 2rem;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.subtitle {
  color: #6b7280;
  margin: 0;
  font-size: 16px;
}

/* Content */
.content {
  min-height: 400px;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  color: #6b7280;
  gap: 1rem;
}

.loading-spinner {
  width: 24px;
  height: 24px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty {
  text-align: center;
  padding: 4rem 2rem;
  color: #6b7280;
}

.empty-icon {
  margin-bottom: 1.5rem;
  color: #d1d5db;
}

.empty-title {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-text {
  margin: 0;
  font-size: 16px;
}

/* Documents Grid */
.documents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.document-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.2s ease;
}

.document-card:hover {
  border-color: #cbd5e1;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  line-height: 1.3;
  flex: 1;
  margin-right: 1rem;
}

.file-badge {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
  flex-shrink: 0;
}

.card-description {
  color: #6b7280;
  margin: 0 0 1rem 0;
  line-height: 1.5;
  font-size: 14px;
}

.card-meta {
  border-top: 1px solid #f3f4f6;
  padding-top: 1rem;
  margin-bottom: 1.5rem;
}

.card-date {
  color: #9ca3af;
  font-size: 13px;
  font-weight: 500;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
}

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  font-size: 14px;
  text-decoration: none;
  flex: 1;
  min-height: 44px;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-1px);
  text-decoration: none;
  color: white;
}

.btn-secondary {
  background: white;
  color: #6b7280;
  border: 1px solid #d1d5db;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  font-size: 14px;
  text-decoration: none;
  flex: 1;
  min-height: 44px;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  text-decoration: none;
  color: #374151;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .documents-grid {
    grid-template-columns: 1fr;
  }
  
  .nav-container {
    padding: 0 1rem;
  }
  
  .user-name {
    display: none;
  }
  
  .card-actions {
    flex-direction: column;
  }
  
  .card-header {
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }
  
  .file-badge {
    align-self: flex-start;
  }
}

@media (max-width: 480px) {
  .main {
    padding: 1rem 0;
  }
  
  .document-card {
    padding: 1rem;
  }
  
  .title {
    font-size: 24px;
  }
  
  .subtitle {
    font-size: 14px;
  }
}
</style>