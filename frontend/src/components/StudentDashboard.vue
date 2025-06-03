<template>
  <div class="dashboard">
    <nav class="dashboard-nav">
      <div class="container">
        <div class="nav-content">
          <h2>🤖 Askify Student</h2>
          <div class="nav-actions">
            <span>Welcome, {{ userData?.name }}</span>
            <button @click="handleLogout" class="btn btn-secondary">Logout</button>
          </div>
        </div>
      </div>
    </nav>

    <main class="dashboard-main">
      <div class="container">
        <div class="dashboard-header">
          <h1>Available Documents</h1>
          <p>Click on any document to read it or ask questions about it</p>
        </div>

        <!-- Documents List -->
        <div class="documents-grid">
          <div v-if="loading" class="loading">Loading documents...</div>
          <div v-else-if="documents.length === 0" class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No documents available</h3>
            <p>Your teacher hasn't uploaded any documents yet</p>
          </div>
          <div v-else>
            <div v-for="document in documents" :key="document.id" class="document-card">
              <div class="document-header">
                <h3>{{ document.title }}</h3>
                <div class="document-badge">
                  {{ getFileExtension(document.file_path) }}
                </div>
              </div>
              <p v-if="document.description" class="document-description">
                {{ document.description }}
              </p>
              <div class="document-meta">
                <span class="document-date">
                  {{ formatDate(document.created_at) }}
                </span>
              </div>
              <div class="document-actions">
                <router-link 
                  :to="`/document/${document.id}`" 
                  class="btn btn-secondary"
                >
                  📖 Read Document
                </router-link>
                <router-link 
                  :to="`/chat/${document.id}`" 
                  class="btn"
                >
                  💬 Ask Questions
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../utils/api.js'
import { getUserData, removeAuthToken, removeUserData, isAuthenticated } from '../utils/auth.js'


const router = useRouter()

if (!isAuthenticated()){
  router.push('/login')
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
  return new Date(dateString).toLocaleDateString()
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

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f8f9fa;
}

.dashboard-nav {
  background: white;
  border-bottom: 1px solid #e9ecef;
  padding: 1rem 0;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.dashboard-main {
  padding: 2rem 0;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  margin-bottom: 0.5rem;
}

.dashboard-header p {
  color: #666;
}

.documents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.document-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease;
}

.document-card:hover {
  transform: translateY(-2px);
}

.document-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.document-badge {
  background: #667eea;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.document-description {
  color: #666;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.document-meta {
  color: #999;
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

.document-actions {
  display: flex;
  gap: 1rem;
}

.document-actions .btn {
  flex: 1;
  text-align: center;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}
</style>