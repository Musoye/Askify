


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
const messagesContainer = ref(null)

onMounted(async () => {
  await fetchDocument()
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

const sendMessage = async () => {
  if (!newMessage.value.trim() || isLoading.value) return
  
  const userMessage = {
    id: Date.now(),
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
    
    const aiMessage = {
      id: Date.now() + 1,
      type: 'ai',
      text: response.data.answer || 'I received your question and will provide an answer based on the document.',
      timestamp: new Date()
    }
    
    messages.value.push(aiMessage)
  } catch (error) {
    const errorMessage = {
      id: Date.now() + 1,
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
</script><template>
  <div class="chat-view">
    <nav class="chat-nav">
      <div class="container">
        <div class="nav-content">
          <button @click="goBack" class="back-btn">← Back</button>
          <h2 v-if="document">Chat: {{ document.title }}</h2>
          <router-link 
            v-if="document" 
            :to="`/document/${document.id}`" 
            class="btn btn-secondary"
          >
            📖 View Document
          </router-link>
        </div>
      </div>
    </nav>

    <main class="chat-main">
      <div class="container">
        <div class="chat-container">
          <div class="chat-header">
            <h3>Ask questions about: {{ document?.title }}</h3>
            <p>Ask any question about the document and get instant AI-powered answers</p>
          </div>
          
          <div class="chat-messages" ref="messagesContainer">
            <div v-if="messages.length === 0" class="welcome-message">
              <div class="welcome-icon">🤖</div>
              <h4>Hello! I'm here to help</h4>
              <p>Ask me anything about the document "{{ document?.title }}" and I'll provide detailed answers.</p>
            </div>
            
            <div v-for="message in messages" :key="message.id" class="message" :class="message.type">
              <div class="message-content">
                <div class="message-text">{{ message.text }}</div>
                <div class="message-time">{{ formatTime(message.timestamp) }}</div>
              </div>
            </div>
            
            <div v-if="isLoading" class="message ai">
              <div class="message-content">
                <div class="typing-indicator">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="chat-input">
            <form @submit.prevent="sendMessage" class="input-form">
              <div class="input-group">
                <input 
                  v-model="newMessage"
                  type="text" 
                  placeholder="Ask a question about the document..."
                  class="message-input"
                  :disabled="isLoading"
                />
                <button 
                  type="submit" 
                  class="send-btn"
                  :disabled="!newMessage.trim() || isLoading"
                >
                  <span v-if="isLoading">⏳</span>
                  <span v-else>🚀</span>
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
  background: #f8f9fa;
  display: flex;
  flex-direction: column;
}

.chat-nav {
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

.chat-main {
  flex: 1;
  padding: 2rem 0;
}

.chat-container {
  background: white;
  border-radius: 12px;
  height: 70vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chat-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
}

.chat-header h3 {
  margin-bottom: 0.5rem;
  color: #333;
}

.chat-header p {
  color: #666;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
}

.welcome-message {
  text-align: center;
  padding: 3rem 1rem;
  color: #666;
}

.welcome-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.message {
  margin-bottom: 1rem;
  max-width: 80%;
}

.message.user {
  margin-left: auto;
}

.message.user .message-content {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 18px 18px 4px 18px;
}

.message.ai .message-content {
  background: #f8f9fa;
  color: #333;
  border-radius: 18px 18px 18px 4px;
  border: 1px solid #e9ecef;
}

.message-content {
  padding: 1rem 1.5rem;
  display: inline-block;
}

.message-text {
  line-height: 1.5;
  margin-bottom: 0.5rem;
}

.message-time {
  font-size: 0.75rem;
  opacity: 0.7;
}

.typing-indicator {
  display: flex;
  gap: 4px;
}

.typing-indicator span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #667eea;
  animation: typing 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-10px);
  }
}

.chat-input {
  padding: 1rem;
  border-top: 1px solid #e9ecef;
}

.input-group {
  display: flex;
  gap: 1rem;
}

.message-input {
  flex: 1;
  padding: 1rem;
  border: 2px solid #e9ecef;
  border-radius: 25px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s ease;
}

.message-input:focus {
  border-color: #667eea;
}

.send-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  cursor: pointer;
  font-size: 1.2rem;
  transition: transform 0.2s ease;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>