<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../utils/api.js'
import { isAuthenticated } from '../utils/auth.js'

const route = useRoute()
const router = useRouter()

if (!isAuthenticated()){
  router.push('/login')
}

const document = ref(null)
const messages = ref([])
const newMessage = ref('')
const isLoading = ref(false)
const isLoadingHistory = ref(false)
const messagesContainer = ref(null)

onMounted(async () => {
  await fetchDocument()
  await loadPreviousChats()
})

const fetchDocument = async () => {
  try {
    const documentId = route.params.id
    const response = await api.get(`/documents/${documentId}`)
    document.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to load document:', error)
  }
}

const loadPreviousChats = async () => {
  try {
    isLoadingHistory.value = true
    const documentId = route.params.id
    const response = await api.get(`/questions/${documentId}/users`)
    
    if (response.data.data && response.data.data.length > 0) {
      const previousMessages = []
      
      response.data.data.forEach(questionData => {
        // Add user question
        previousMessages.push({
          id: `q-${questionData.id}`,
          type: 'user',
          text: questionData.question,
          timestamp: new Date(questionData.created_at)
        })
        
        // Add AI answer if exists
        if (questionData.answer) {
          previousMessages.push({
            id: `a-${questionData.id}`,
            type: 'ai',
            text: questionData.answer,
            timestamp: new Date(questionData.updated_at || questionData.created_at)
          })
        }
      })
      
      // Sort by timestamp
      previousMessages.sort((a, b) => a.timestamp - b.timestamp)
      messages.value = previousMessages
      
      await scrollToBottom()
    }
  } catch (error) {
    console.error('Failed to load previous chats:', error)
  } finally {
    isLoadingHistory.value = false
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || isLoading.value) return
  
  const userMessage = {
    id: `user-${Date.now()}`,
    type: 'user',
    text: newMessage.value,
    timestamp: new Date()
  }
  
  messages.value.push(userMessage)
  const questionText = newMessage.value
  newMessage.value = ''
  isLoading.value = true
  
  await scrollToBottom()
  
  try {
    const response = await api.post('/questions', {
      document_id: document.value.id,
      question: questionText
    })
    console.log(response.data)
    
    const aiMessage = {
      id: `ai-${Date.now()}`,
      type: 'ai',
      text: response.data.data.answer || 'I received your question and will provide an answer based on the document.',
      timestamp: new Date()
    }
    
    messages.value.push(aiMessage)
  } catch (error) {
    const errorMessage = {
      id: `error-${Date.now()}`,
      type: 'ai',
      text: 'Sorry, I encountered an error while processing your question. Please try again.',
      timestamp: new Date()
    }
    
    messages.value.push(errorMessage)
    console.error('Failed to send question:', error)
  } finally {
    isLoading.value = false
    await scrollToBottom()
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatTime = (timestamp) => {
  return timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const goBack = () => {
  router.go(-1)
}
</script>

<template>
  <div class="chat-view">
    <!-- Minimal Navigation Header -->
    <nav class="nav">
      <div class="nav-container">
        <button @click="goBack" class="back-btn">
          ← Back
        </button>
        <h2 v-if="document" class="doc-title">{{ document.title }}</h2>
        <router-link 
          v-if="document" 
          :to="`/document/${document.id}`" 
          class="view-btn"
        >
          📖 View Document
        </router-link>
      </div>
    </nav>

    <!-- Main Chat Area -->
    <main class="main">
      <div class="container">
        <div class="chat-container">
          <!-- Chat Messages -->
          <div class="messages" ref="messagesContainer">
            <!-- Welcome Message -->
            <div v-if="messages.length === 0 && !isLoadingHistory" class="welcome">
              <div class="welcome-icon">💬</div>
              <h3>Ask questions about this document</h3>
              <p>I'll provide answers based on the content of "{{ document?.title }}"</p>
            </div>

            <!-- Loading History -->
            <div v-if="isLoadingHistory" class="loading">
              <div class="spinner"></div>
              <p>Loading previous conversations...</p>
            </div>

            <!-- Message History -->
            <div v-for="message in messages" :key="message.id" class="message" :class="message.type">
              <div class="message-bubble">
                <div class="message-text">{{ message.text }}</div>
                <div class="message-time">{{ formatTime(message.timestamp) }}</div>
              </div>
            </div>
            
            <!-- Typing Indicator -->
            <div v-if="isLoading" class="message ai">
              <div class="message-bubble">
                <div class="typing">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Chat Input -->
          <div class="input-area">
            <form @submit.prevent="sendMessage" class="input-form">
              <div class="input-group">
                <input 
                  v-model="newMessage"
                  type="text" 
                  placeholder="Ask a question about the document..."
                  class="input"
                  :disabled="isLoading"
                />
                <button 
                  type="submit" 
                  class="send-btn"
                  :disabled="!newMessage.trim() || isLoading"
                >
                  <span v-if="isLoading">⏳</span>
                  <span v-else>→</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>


<style scoped>
.chat-view {
  min-height: 100vh;
  background: #fafafa;
  display: flex;
  flex-direction: column;
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

.view-btn {
  background: #f5f5f5;
  color: #666;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.view-btn:hover {
  background: #e8e8e8;
  color: #333;
}

/* Main Chat */
.main {
  flex: 1;
  padding: 1.5rem 0;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: calc(100vh - 120px);
}

.chat-container {
  background: white;
  border-radius: 8px;
  height: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Messages */
.messages {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  scroll-behavior: smooth;
}

.welcome {
  text-align: center;
  padding: 3rem 2rem;
  color: #666;
}

.welcome-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.welcome h3 {
  margin: 0 0 0.5rem 0;
  color: #333;
  font-weight: 500;
}

.welcome p {
  margin: 0;
  font-size: 14px;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  color: #666;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #f0f0f0;
  border-top: 2px solid #007aff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.message {
  margin-bottom: 1rem;
  max-width: 80%;
  display: flex;
}

.message.user {
  justify-content: flex-end;
}

.message.ai {
  justify-content: flex-start;
}

.message-bubble {
  padding: 12px 16px;
  border-radius: 16px;
  max-width: 100%;
}

.message.user .message-bubble {
  background: #007aff;
  color: white;
  border-bottom-right-radius: 4px;
}

.message.ai .message-bubble {
  background: #f5f5f5;
  color: #333;
  border-bottom-left-radius: 4px;
}

.message-text {
  line-height: 1.4;
  font-size: 15px;
  margin-bottom: 4px;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
}

.typing {
  display: flex;
  gap: 4px;
  padding: 4px 0;
}

.typing span {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #007aff;
  animation: typing 1.4s infinite ease-in-out;
}

.typing span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-4px);
  }
}

/* Input Area */
.input-area {
  padding: 1rem 1.5rem;
  border-top: 1px solid #f0f0f0;
}

.input-group {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.input {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 20px;
  font-size: 15px;
  outline: none;
  transition: border-color 0.2s ease;
  background: #fafafa;
}

.input:focus {
  border-color: #007aff;
  background: white;
}

.input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.send-btn {
  background: #007aff;
  color: white;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.send-btn:hover:not(:disabled) {
  background: #0056b3;
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none;
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
    height: calc(100vh - 100px);
  }
  
  .messages {
    padding: 1rem;
  }
  
  .input-area {
    padding: 1rem;
  }
  
  .message {
    max-width: 90%;
  }
  
  .welcome {
    padding: 2rem 1rem;
  }
}
</style>