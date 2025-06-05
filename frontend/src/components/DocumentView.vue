<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../utils/api.js'
import { isAuthenticated } from '../utils/auth.js'

const route = useRoute()
const router = useRouter()

if (!isAuthenticated()){
  router.push('/login')
}

const document = ref(null)
const documentContent = ref('')
const loading = ref(true)
const error = ref('')

const isTextFile = computed(() => {
  if (!document.value?.file_path) return false
  const extension = document.value.file_path.split('.').pop().toLowerCase()
  return ['txt', 'md', 'markdown'].includes(extension)
})

const isPdfFile = computed(() => {
  if (!document.value?.file_path) return false
  const extension = document.value.file_path.split('.').pop().toLowerCase()
  return extension === 'pdf'
})

onMounted(async () => {
  await fetchDocument()
})

const fetchDocument = async () => {
  try {
    loading.value = true
    const documentId = route.params.id
    
    // Fetch document metadata
    const response = await api.get(`/documents/${documentId}`)
    document.value = response.data.data || response.data
    
    // If it's a text file, fetch content
    if (isTextFile.value) {
      try {
        const contentResponse = await api.get(`/documents/${documentId}/view`)
        documentContent.value = contentResponse.data
      } catch (contentError) {
        console.error('Failed to load document content:', contentError)
      }
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load document'
  } finally {
    loading.value = false
  }
}

const getDocumentUrl = () => {
  if (document.value?.file_path) {
    return `${api.defaults.baseURL}/documents/${document.value.id}/view`
  }
  return ''
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const goBack = () => {
  router.go(-1)
}
</script>

<template>
  <div class="document-view">
    <!-- Minimal Navigation Header -->
    <nav class="nav">
      <div class="nav-container">
        <button @click="goBack" class="back-btn">
          ← Back
        </button>
        <h2 v-if="document" class="doc-title">{{ document.title }}</h2>
        <router-link 
          v-if="document" 
          :to="`/chat/${document.id}`" 
          class="chat-btn"
        >
          💬 Ask Questions
        </router-link>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="state loading-state">
          <div class="spinner"></div>
          <p>Loading document...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="state error-state">
          <div class="error-icon">⚠️</div>
          <h3>Error Loading Document</h3>
          <p>{{ error }}</p>
          <button @click="goBack" class="btn">Go Back</button>
        </div>

        <!-- Document Content -->
        <div v-else-if="document" class="document">
          <!-- Document Header -->
          <header class="doc-header">
            <h1 class="doc-main-title">{{ document.title }}</h1>
            <p v-if="document.description" class="doc-description">
              {{ document.description }}
            </p>
            <div class="doc-meta">
              Uploaded {{ formatDate(document.created_at) }}
            </div>
          </header>
          
          <!-- Document Viewer -->
          <div class="viewer">
            <!-- Text Content -->
            <div v-if="isTextFile" class="text-content">
              {{ documentContent }}
            </div>
            
            <!-- PDF Viewer -->
            <div v-else-if="isPdfFile" class="pdf-viewer">
              <iframe 
                :src="getDocumentUrl()" 
                class="pdf-frame"
                frameborder="0"
              ></iframe>
            </div>
            
            <!-- File Download -->
            <div v-else class="download-section">
              <div class="download-icon">📄</div>
              <h3>{{ document.title }}</h3>
              <p>This file type cannot be displayed in the browser.</p>
              <a :href="getDocumentUrl()" target="_blank" class="download-btn">
                📥 Download File
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.document-view {
  min-height: 100vh;
  background: #fafafa;
}

/* Navigation */
.nav {
  background: white;
  border-bottom: 1px solid #f0f0f0;
  position: sticky;
  top: 0;
  z-index: 10;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.back-btn {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  font-size: 14px;
  padding: 8px 12px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: #f5f5f5;
  color: #333;
}

.doc-title {
  font-size: 16px;
  font-weight: 500;
  color: #333;
  margin: 0;
  flex: 1;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 0 1rem;
}

.chat-btn {
  background: #007aff;
  color: white;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.chat-btn:hover {
  background: #0056b3;
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

/* States */
.state {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 2px solid #f0f0f0;
  border-top: 2px solid #007aff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state .error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.error-state h3 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.error-state p {
  margin: 0 0 1.5rem 0;
}

.btn {
  background: #007aff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: all 0.2s ease;
}

.btn:hover {
  background: #0056b3;
}

/* Document */
.document {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.doc-header {
  padding: 2rem;
  border-bottom: 1px solid #f0f0f0;
}

.doc-main-title {
  margin: 0 0 0.75rem 0;
  font-size: 1.75rem;
  font-weight: 600;
  color: #333;
  line-height: 1.3;
}

.doc-description {
  margin: 0 0 1rem 0;
  color: #666;
  line-height: 1.5;
  font-size: 15px;
}

.doc-meta {
  color: #999;
  font-size: 13px;
}

/* Viewer */
.viewer {
  padding: 2rem;
}

.text-content {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  line-height: 1.6;
  color: #333;
  white-space: pre-wrap;
  font-size: 15px;
}

.pdf-viewer {
  border-radius: 6px;
  overflow: hidden;
}

.pdf-frame {
  width: 100%;
  height: 800px;
  border-radius: 6px;
}

.download-section {
  text-align: center;
  padding: 3rem 2rem;
}

.download-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.download-section h3 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.download-section p {
  margin: 0 0 1.5rem 0;
  color: #666;
}

.download-btn {
  background: #007aff;
  color: white;
  text-decoration: none;
  padding: 12px 24px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  display: inline-block;
  transition: all 0.2s ease;
}

.download-btn:hover {
  background: #0056b3;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-container {
    padding: 0 1rem;
  }
  
  .doc-title {
    font-size: 14px;
  }
  
  .container {
    padding: 0 1rem;
  }
  
  .doc-header {
    padding: 1.5rem;
  }
  
  .viewer {
    padding: 1.5rem;
  }
  
  .doc-main-title {
    font-size: 1.5rem;
  }
  
  .pdf-frame {
    height: 600px;
  }
}
</style>