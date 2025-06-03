<template>
  <div class="document-view">
    <nav class="document-nav">
      <div class="container">
        <div class="nav-content">
          <button @click="goBack" class="back-btn">← Back</button>
          <h2 v-if="document">{{ document.title }}</h2>
          <router-link 
            v-if="document" 
            :to="`/chat/${document.id}`" 
            class="btn"
          >
            💬 Ask Questions
          </router-link>
        </div>
      </div>
    </nav>

    <main class="document-main">
      <div class="container">
        <div v-if="loading" class="loading">
          Loading document...
        </div>
        <div v-else-if="error" class="error-state">
          <div class="error-icon">⚠️</div>
          <h3>Error Loading Document</h3>
          <p>{{ error }}</p>
          <button @click="goBack" class="btn">Go Back</button>
        </div>
        <div v-else-if="document" class="document-content">
          <div class="document-header">
            <h1>{{ document.title }}</h1>
            <p v-if="document.description" class="document-description">
              {{ document.description }}
            </p>
            <div class="document-meta">
              <span>Uploaded: {{ formatDate(document.created_at) }}</span>
            </div>
          </div>
          
          <div class="document-viewer">
            <div v-if="isTextFile" class="text-content">
              {{ documentContent }}
            </div>
            <div v-else-if="isPdfFile" class="pdf-viewer">
              <iframe 
                :src="getDocumentUrl()" 
                width="100%" 
                height="800px"
                frameborder="0"
              ></iframe>
            </div>
            <div v-else class="file-download">
              <div class="download-icon">📄</div>
              <h3>{{ document.title }}</h3>
              <p>This file type cannot be displayed in the browser.</p>
              <a :href="getDocumentUrl()" target="_blank" class="btn">
                📥 Download File
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

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

<style scoped>
.document-view {
  min-height: 100vh;
  background: #f8f9fa;
}

.document-nav {
  background: white;
  border-bottom: 1px solid #e9ecef;
  padding: 1rem 0;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.back-btn {
  background: none;
  border: none;
  color: #667eea;
  cursor: pointer;
  font-size: 1rem;
  padding: 0.5rem;
}

.document-main {
  padding: 2rem 0;
}

.document-content {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.document-header {
  padding: 2rem;
  border-bottom: 1px solid #e9ecef;
}

.document-header h1 {
  margin-bottom: 0.5rem;
  color: #333;
}

.document-description {
  color: #666;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.document-meta {
  color: #999;
  font-size: 0.875rem;
}

.document-viewer {
  padding: 2rem;
}

.text-content {
  font-family: 'Georgia', serif;
  line-height: 1.6;
  color: #333;
  white-space: pre-wrap;
}

.pdf-viewer iframe {
  border-radius: 8px;
}

.file-download {
  text-align: center;
  padding: 3rem;
}

.download-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #666;
}
</style>
