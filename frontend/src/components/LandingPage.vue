<script setup>
import { useRouter } from 'vue-router'
import { ref } from 'vue'
import { isAuthenticated } from '../utils/auth.js'

const router = useRouter()

if (isAuthenticated()){
  router.push('/admin')
}

const email = ref('')
const isSubscribing = ref(false)

const handleNewsletterSubscribe = async () => {
  if (!email.value) return
  
  isSubscribing.value = true
  // Simulation of Suscription
  setTimeout(() => {
    alert('Thank you for subscribing!')
    email.value = ''
    isSubscribing.value = false
  }, 1000)
}
</script>

<template>
  <div class="landing-page">
    <!-- Navigation -->
    <nav class="navbar">
      <div class="container">
        <div class="nav-content">
          <div class="brand">
            <div class="brand-icon">A</div>
            <span class="brand-name">Askify</span>
          </div>
          <div class="nav-links">
            <router-link to="/login" class="nav-link">Sign in</router-link>
            <router-link to="/signup" class="btn btn-primary">Get Started</router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <div class="hero-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            AI-Powered Learning
          </div>
          
          <h1 class="hero-title">
            Transform Your Documents Into
            <span class="hero-highlight">Interactive Knowledge</span>
          </h1>
          
          <p class="hero-subtitle">
            Upload your lesson materials and get instant, intelligent answers to your questions. 
            Askify makes learning more engaging and efficient with AI-powered assistance.
          </p>
          
          <div class="hero-actions">
            <router-link to="/signup" class="btn btn-primary btn-large">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Start Learning Free
            </router-link>
            <router-link to="/login" class="btn btn-secondary btn-large">
              Already have an account?
            </router-link>
          </div>
          
          <div class="hero-stats">
            <div class="stat">
              <div class="stat-number">10K+</div>
              <div class="stat-label">Documents Processed</div>
            </div>
            <div class="stat">
              <div class="stat-number">50K+</div>
              <div class="stat-label">Questions Answered</div>
            </div>
            <div class="stat">
              <div class="stat-number">99%</div>
              <div class="stat-label">Accuracy Rate</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">How Askify Works</h2>
          <p class="section-subtitle">Three simple steps to transform your learning experience</p>
        </div>
        
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-number">01</div>
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="10,9 9,9 8,9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3 class="feature-title">Upload Documents</h3>
            <p class="feature-description">
              Easily upload your lesson materials, PDFs, or study guides. Our AI processes them instantly.
            </p>
          </div>
          
          <div class="feature-card">
            <div class="feature-number">02</div>
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="9" cy="10" r="1" fill="currentColor"/>
                <circle cx="15" cy="10" r="1" fill="currentColor"/>
                <path d="M9 13s1 2 3 2 3-2 3-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3 class="feature-title">Ask Questions</h3>
            <p class="feature-description">
              Ask questions in natural language about your documents. Get precise, contextual answers instantly.
            </p>
          </div>
          
          <div class="feature-card">
            <div class="feature-number">03</div>
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 3c0 1-1 3-3 3s-3-2-3-3 1-3 3-3 3 2 3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 21c0-1 1-3 3-3s3 2 3 3-1 3-3 3-3-2-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3 class="feature-title">Learn Smarter</h3>
            <p class="feature-description">
              Get AI-powered insights, explanations, and summaries that adapt to your learning style.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2 class="cta-title">Ready to Transform Your Learning?</h2>
          <p class="cta-subtitle">Join thousands of students already using Askify to learn more effectively</p>
          <div class="cta-actions">
            <router-link to="/signup" class="btn btn-primary btn-large">
              Get Started for Free
            </router-link>
            <router-link to="/login" class="btn btn-secondary btn-large">
              Sign In
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-brand">
            <div class="brand">
              <div class="brand-icon">A</div>
              <span class="brand-name">Askify</span>
            </div>
            <p class="footer-description">
              AI-powered learning assistant that transforms your documents into interactive knowledge.
            </p>
            <div class="social-links">
              <a href="#" class="social-link" aria-label="Twitter">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
              <a href="#" class="social-link" aria-label="GitHub">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
              <a href="#" class="social-link" aria-label="LinkedIn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <rect x="2" y="9" width="4" height="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="4" cy="4" r="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            </div>
          </div>
          
          <div class="footer-links">
            <div class="footer-column">
              <h4 class="footer-heading">Product</h4>
              <ul class="footer-list">
                <li><a href="#features" class="footer-link">Features</a></li>
                <li><a href="#pricing" class="footer-link">Pricing</a></li>
                <li><a href="#demo" class="footer-link">Demo</a></li>
                <li><a href="#api" class="footer-link">API</a></li>
              </ul>
            </div>
            
            <div class="footer-column">
              <h4 class="footer-heading">Company</h4>
              <ul class="footer-list">
                <li><a href="#about" class="footer-link">About</a></li>
                <li><a href="#blog" class="footer-link">Blog</a></li>
                <li><a href="#careers" class="footer-link">Careers</a></li>
                <li><a href="#contact" class="footer-link">Contact</a></li>
              </ul>
            </div>
            
            <div class="footer-column">
              <h4 class="footer-heading">Support</h4>
              <ul class="footer-list">
                <li><a href="#help" class="footer-link">Help Center</a></li>
                <li><a href="#docs" class="footer-link">Documentation</a></li>
                <li><a href="#status" class="footer-link">Status</a></li>
                <li><a href="#community" class="footer-link">Community</a></li>
              </ul>
            </div>
          </div>
          
          <div class="footer-newsletter">
            <h4 class="footer-heading">Stay Updated</h4>
            <p class="newsletter-text">Get the latest features and updates delivered to your inbox.</p>
            <form @submit.prevent="handleNewsletterSubscribe" class="newsletter-form">
              <div class="newsletter-input-container">
                <svg class="newsletter-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input 
                  v-model="email"
                  type="email" 
                  placeholder="Enter your email"
                  class="newsletter-input"
                  required
                />
              </div>
              <button type="submit" class="newsletter-btn" :disabled="isSubscribing">
                <div v-if="isSubscribing" class="loading-spinner"></div>
                <span>{{ isSubscribing ? 'Subscribing...' : 'Subscribe' }}</span>
              </button>
            </form>
          </div>
        </div>
        
        <div class="footer-bottom">
          <div class="footer-bottom-content">
            <p class="footer-copyright">&copy; 2025 Askify. All rights reserved.</p>
            <div class="footer-legal">
              <a href="#privacy" class="footer-link">Privacy Policy</a>
              <a href="#terms" class="footer-link">Terms of Service</a>
              <a href="#cookies" class="footer-link">Cookie Policy</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.landing-page {
  min-height: 100vh;
  background: #fafbfc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  color: #111827;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Navigation */
.navbar {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 50;
  backdrop-filter: blur(8px);
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: inherit;
}

.brand-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 18px;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-link {
  color: #6b7280;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
  padding: 0.5rem 1rem;
}

.nav-link:hover {
  color: #3b82f6;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s ease;
  border: 1px solid transparent;
  cursor: pointer;
  min-height: 40px;
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
  background: white;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-large {
  padding: 14px 28px;
  font-size: 16px;
  min-height: 48px;
}

/* Hero Section */
.hero {
  padding: 6rem 0 4rem;
  text-align: center;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 2rem;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  color: #111827;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.hero-highlight {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 3rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 4rem;
  flex-wrap: wrap;
}

.hero-stats {
  display: flex;
  gap: 3rem;
  justify-content: center;
  flex-wrap: wrap;
}

.stat {
  text-align: center;
}

.stat-number {
  font-size: 2rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

/* Features Section */
.features {
  padding: 6rem 0;
  background: white;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.125rem;
  color: #6b7280;
  max-width: 500px;
  margin: 0 auto;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.feature-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 2rem;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.feature-card:hover {
  border-color: #3b82f6;
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
  transform: translateY(-2px);
}

.feature-number {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  font-size: 3rem;
  font-weight: 800;
  color: #f3f4f6;
  line-height: 1;
}

.feature-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin-bottom: 1.5rem;
}

.feature-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.75rem;
}

.feature-description {
  color: #6b7280;
  line-height: 1.6;
}

/* CTA Section */
.cta-section {
  padding: 6rem 0;
  background: #f9fafb;
  text-align: center;
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 1rem;
}

.cta-subtitle {
  font-size: 1.125rem;
  color: #6b7280;
  margin-bottom: 2rem;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.cta-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.footer {
  background: white;
  border-top: 1px solid #e5e7eb;
  padding: 4rem 0 2rem;
}

.footer-content {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1.5fr;
  gap: 3rem;
  margin-bottom: 3rem;
}

.footer-brand .brand {
  margin-bottom: 1rem;
}

.footer-description {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  max-width: 300px;
}

.social-links {
  display: flex;
  gap: 0.75rem;
}

.social-link {
  width: 40px;
  height: 40px;
  background: #f3f4f6;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s ease;
}

.social-link:hover {
  background: #3b82f6;
  color: white;
  transform: translateY(-1px);
}

.footer-heading {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.footer-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-list li {
  margin-bottom: 0.5rem;
}

.footer-link {
  color: #6b7280;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s ease;
}

.footer-link:hover {
  color: #3b82f6;
}

.footer-newsletter {
  grid-column: span 1;
}

.newsletter-text {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.newsletter-input-container {
  position: relative;
}

.newsletter-input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

.newsletter-input {
  width: 100%;
  padding: 10px 12px 10px 40px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  transition: all 0.2s ease;
}

.newsletter-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.newsletter-input::placeholder {
  color: #9ca3af;
}

.newsletter-btn {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 40px;
}

.newsletter-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-1px);
}

.newsletter-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.footer-bottom {
  border-top: 1px solid #e5e7eb;
  padding-top: 2rem;
}

.footer-bottom-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.footer-copyright {
  color: #6b7280;
  font-size: 14px;
  margin: 0;
}

.footer-legal {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.footer-legal .footer-link {
  font-size: 14px;
  position: relative;
}

.footer-legal .footer-link:not(:last-child)::after {
  content: "•";
  position: absolute;
  right: -12px;
  color: #d1d5db;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .footer-content {
    grid-template-columns: 1fr 1fr 1fr;
    gap: 2rem;
  }
  
  .footer-newsletter {
    grid-column: span 3;
    max-width: 400px;
  }
}

@media (max-width: 768px) {
  .footer {
    padding: 3rem 0 1.5rem;
  }
  
  .footer-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
  }
  
  .footer-newsletter {
    grid-column: span 1;
    max-width: none;
  }
  
  .footer-bottom-content {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .footer-legal {
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .social-links {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }
  
  .footer-legal {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .footer-legal .footer-link:not(:last-child)::after {
    display: none;
  }
  
  .newsletter-form {
    gap: 1rem;
  }
}
</style>