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
const isUserAdmin = ref(isAdmin())

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
  const file = event.target.files[0];
  const maxSizeBytes = 10 * 1024 * 1024

  if (file && file.size > maxSizeBytes) {
    alert('File size exceeds 10MB. Please select a smaller file.');
    event.target.value = null
    uploadForm.value.file = null
    return;
  }

  uploadForm.value.file = file;
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

const viewDocument = (documentId) => {
  router.push(`/document/${documentId}`)
}

const chatWithDocument = (documentId) => {
  router.push(`/chat/${documentId}`)
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
            <h1 class="title">Documents</h1>
            <p class="subtitle">Browse and interact with your document library</p>
          </div>
          <button 
            v-if="isUserAdmin" 
            @click="showUploadModal = true" 
            class="btn-primary"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Upload
          </button>
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
            <p class="empty-text">
              {{ isUserAdmin ? 'Upload your first document to get started' : 'Please wait for documents to be uploaded by an administrator' }}
            </p>
          </div>

          <div v-else class="documents-grid">
            <div v-for="document in documents" :key="document.id" class="document-card">
              <div class="card-header">
                <div class="file-info">
                  <div class="file-badge">
                    {{ getFileExtension(document.file_path) }}
                  </div>
                  <h3 class="card-title">{{ document.title }}</h3>
                </div>
                <div v-if="isUserAdmin" class="card-actions admin-actions">
                  <button @click="editDocument(document)" class="action-btn" title="Edit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                  <button @click="deleteDocument(document.id)" class="action-btn delete" title="Delete">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </div>
              
              <p v-if="document.description" class="card-description">
                {{ document.description }}
              </p>
              
              <div class="card-footer">
                <span class="card-date">{{ formatDate(document.created_at) }}</span>
                <div class="card-actions">
                  <button @click="viewDocument(document.id)" class="btn-action view" title="View Document">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    View
                  </button>
                  <button @click="chatWithDocument(document.id)" class="btn-action chat" title="Chat with Document">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Chat
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Upload Modal (Admin Only) -->
    <div v-if="showUploadModal && isUserAdmin" class="modal-overlay" @click="showUploadModal = false">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Upload Document</h3>
          <button @click="showUploadModal = false" class="modal-close">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="handleUpload" class="modal-form">
          <div class="form-group">
            <label for="title" class="form-label">Title</label>
            <input 
              id="title" 
              v-model="uploadForm.title" 
              type="text" 
              class="form-input" 
              placeholder="Document title"
              required 
            />
          </div>
          <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea 
              id="description" 
              v-model="uploadForm.description" 
              class="form-textarea" 
              rows="3"
              placeholder="Brief description (optional)"
            ></textarea>
          </div>
          <div class="form-group">
            <label for="file" class="form-label">File</label>
            <input 
              id="file" 
              type="file" 
              @change="handleFileSelect" 
              class="form-file"
              accept=".pdf,.doc,.docx,.txt" 
              required 
            />
          </div>
          <div v-if="uploadError" class="alert-error">
            {{ uploadError }}
          </div>
          <div class="modal-actions">
            <button type="button" @click="showUploadModal = false" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary" :disabled="uploading">
              {{ uploading ? 'Uploading...' : 'Upload' }}
            </button>
          </div>
        </form>
      </div>
    </div>
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
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
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

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  font-size: 14px;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-1px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: white;
  color: #6b7280;
  border: 1px solid #d1d5db;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 14px;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-action {
  background: white;
  color: #6b7280;
  border: 1px solid #e5e7eb;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  font-size: 13px;
}

.btn-action.view:hover {
  background: #f0f7ff;
  border-color: #3b82f6;
  color: #3b82f6;
}

.btn-action.chat:hover {
  background: #f0fdf4;
  border-color: #10b981;
  color: #10b981;
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
  max-width: 400px;
  margin: 0 auto;
  line-height: 1.5;
}

/* Documents Grid */
.documents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
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

.file-info {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  flex: 1;
}

.file-badge {
  background: #f3f4f6;
  color: #6b7280;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  flex-shrink: 0;
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  line-height: 1.3;
  flex: 1;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

.admin-actions {
  gap: 0.25rem;
}

.action-btn {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.action-btn:hover {
  color: #3b82f6;
  background: #f3f4f6;
}

.action-btn.delete:hover {
  color: #ef4444;
  background: #fef2f2;
}

.card-description {
  color: #6b7280;
  margin: 0 0 1rem 0;
  line-height: 1.5;
  font-size: 14px;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #f3f4f6;
  padding-top: 1rem;
}

.card-date {
  color: #9ca3af;
  font-size: 13px;
  font-weight: 500;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #f3f4f6;
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.modal-close:hover {
  color: #6b7280;
  background: #f3f4f6;
}

.modal-form {
  padding: 1.5rem;
}

/* Form Elements */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: white;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.form-file {
  width: 100%;
  padding: 0.75rem;
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background: #fafbfc;
  cursor: pointer;
  transition: all 0.2s ease;
}

.form-file:hover {
  border-color: #3b82f6;
  background: #f0f7ff;
}

.alert-error {
  background: #fef2f2;
  color: #dc2626;
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 14px;
  margin-bottom: 1.5rem;
  border: 1px solid #fecaca;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
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
  
  .modal-actions {
    flex-direction: column-reverse;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
  
  .card-footer {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .card-actions {
    width: 100%;
    justify-content: stretch;
  }
  
  .btn-action {
    flex: 1;
    justify-content: center;
  }
}
</style>