<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../utils/api.js'
import { getUserData, removeAuthToken, removeUserData, isAdmin, isAuthenticated } from '../utils/auth.js'


const router = useRouter()

if (!isAuthenticated()){
  router.push('/login')
}
if (!isAdmin()) {
  router.push('/student');
}

const userData = ref(getUserData())

const documents = ref([])
const loading = ref(true)
const showUploadModal = ref(false)
const uploading = ref(false)
const uploadError = ref('')

const uploadForm = ref({
  title: '',
  description: '',
  file: null
})

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

const handleFileSelect = (event) => {
  uploadForm.value.file = event.target.files[0]
}

const handleUpload = async () => {
  uploading.value = true
  uploadError.value = ''

  try {
    const formData = new FormData()
    formData.append('title', uploadForm.value.title)
    formData.append('description', uploadForm.value.description)
    formData.append('file', uploadForm.value.file)

    await api.post('/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    showUploadModal.value = false
    uploadForm.value = { title: '', description: '', file: null }
    await fetchDocuments()
  } catch (error) {
    uploadError.value = error.response?.data?.message || 'Failed to upload document'
  } finally {
    uploading.value = false
  }
}

const editDocument = (document) => {
  // Implement edit functionality
  console.log('Edit document:', document)
}

const deleteDocument = async (documentId) => {
  if (confirm('Are you sure you want to delete this document?')) {
    try {
      await api.delete(`/documents/${documentId}`)
      await fetchDocuments()
    } catch (error) {
      console.error('Failed to delete document:', error)
    }
  }
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

<template>
  <div class="dashboard">
    <nav class="dashboard-nav">
      <div class="container">
        <div class="nav-content">
          <h2>🤖 Askify Admin</h2>
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
          <h1>Document Management</h1>
          <button @click="showUploadModal = true" class="btn">
            📄 Upload Document
          </button>
        </div>

        <!-- Upload Modal -->
        <div v-if="showUploadModal" class="modal-overlay" @click="showUploadModal = false">
          <div class="modal" @click.stop>
            <div class="modal-header">
              <h3>Upload New Document</h3>
              <button @click="showUploadModal = false" class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="handleUpload">
                <div class="form-group">
                  <label for="title">Document Title</label>
                  <input id="title" v-model="uploadForm.title" type="text" class="form-control" required
                    placeholder="Enter document title" />
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea id="description" v-model="uploadForm.description" class="form-control" rows="3"
                    placeholder="Brief description of the document"></textarea>
                </div>
                <div class="form-group">
                  <label for="file">Upload File</label>
                  <input id="file" type="file" @change="handleFileSelect" class="form-control"
                    accept=".pdf,.doc,.docx,.txt" required />
                </div>
                <div v-if="uploadError" class="alert alert-error">
                  {{ uploadError }}
                </div>
                <div class="modal-actions">
                  <button type="button" @click="showUploadModal = false" class="btn btn-secondary">
                    Cancel
                  </button>
                  <button type="submit" class="btn" :disabled="uploading">
                    {{ uploading ? 'Uploading...' : 'Upload Document' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Documents List -->
        <div class="documents-grid">
          <div v-if="loading" class="loading">Loading documents...</div>
          <div v-else-if="documents.length === 0" class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No documents yet</h3>
            <p>Upload your first document to get started</p>
          </div>
          <div v-else>
            <div v-for="document in documents" :key="document.id" class="document-card">
              <div class="document-header">
                <h3>{{ document.title }}</h3>
                <div class="document-actions">
                  <button @click="editDocument(document)" class="btn-icon" title="Edit">
                    ✏️
                  </button>
                  <button @click="deleteDocument(document.id)" class="btn-icon delete" title="Delete">
                    🗑️
                  </button>
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
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>


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
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

.modal-body {
  padding: 1.5rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
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

.document-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}

.btn-icon:hover {
  background-color: #f8f9fa;
}

.btn-icon.delete:hover {
  background-color: #fee;
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