<template>
  <div class="landing-page">
    <nav class="navbar">
      <div class="container">
        <div class="nav-content">
          <div class="logo">
            <h2>🤖 Askify</h2>
          </div>
          <div class="nav-links">
            <router-link to="/login" class="btn btn-secondary">Login</router-link>
            <router-link to="/signup" class="btn">Get Started</router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="hero">
      <div class="container">
        <div class="hero-content">
          <h1>AI-Powered Learning Assistant</h1>
          <p class="hero-subtitle">
            Upload your lesson documents and get instant, intelligent answers to your questions. 
            Askify makes learning interactive and engaging.
          </p>
          <div class="hero-actions">
            <router-link to="/signup" class="btn btn-large">Start Learning Today</router-link>
            <router-link to="/login" class="btn btn-secondary btn-large">Already have an account?</router-link>
          </div>
        </div>
        
        <div class="features">
          <div class="feature-grid">
            <div class="feature-card">
              <div class="feature-icon">📚</div>
              <h3>Upload Documents</h3>
              <p>Easily upload and manage your lesson materials</p>
            </div>
            <div class="feature-card">
              <div class="feature-icon">💬</div>
              <h3>Ask Questions</h3>
              <p>Get instant answers using natural language</p>
            </div>
            <div class="feature-card">
              <div class="feature-icon">🧠</div>
              <h3>AI-Powered</h3>
              <p>Powered by advanced AI for smart responses</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-section">
            <div class="footer-logo">
              <h3>🤖 Askify</h3>
              <p>AI-powered learning made simple and interactive. Transform your documents into knowledge.</p>
            </div>
          </div>
          
          <div class="footer-section">
            <h4>Product</h4>
            <ul class="footer-links">
              <li><a href="#features">Features</a></li>
              <li><a href="#pricing">Pricing</a></li>
              <li><a href="#demo">Demo</a></li>
              <li><a href="#integrations">Integrations</a></li>
            </ul>
          </div>
          
          <div class="footer-section">
            <h4>Company</h4>
            <ul class="footer-links">
              <li><a href="#about">About Us</a></li>
              <li><a href="#careers">Careers</a></li>
              <li><a href="#blog">Blog</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div>
          
          <div class="footer-section">
            <h4>Support</h4>
            <ul class="footer-links">
              <li><a href="#help">Help Center</a></li>
              <li><a href="#docs">Documentation</a></li>
              <li><a href="#community">Community</a></li>
              <li><a href="#status">Status</a></li>
            </ul>
          </div>
          
          <div class="footer-section">
            <h4>Connect</h4>
            <div class="social-links">
              <a href="#twitter" class="social-link">🐦</a>
              <a href="#linkedin" class="social-link">💼</a>
              <a href="#github" class="social-link">💻</a>
              <a href="#discord" class="social-link">💬</a>
            </div>
            <div class="newsletter">
              <p>Stay updated with our latest features</p>
              <div class="newsletter-form">
                <input type="email" placeholder="Enter your email" class="newsletter-input">
                <button class="newsletter-btn">Subscribe</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="footer-bottom">
          <div class="footer-bottom-content">
            <p>&copy; 2025 Askify. All rights reserved.</p>
            <div class="footer-bottom-links">
              <a href="#privacy">Privacy Policy</a>
              <a href="#terms">Terms of Service</a>
              <a href="#cookies">Cookie Policy</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

import { isAuthenticated } from '../utils/auth.js'

const router = useRouter()

if (isAuthenticated()){
  router.push('/admin')
}
</script>

<style scoped>
.landing-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 25%, #60a5fa  50%, #93c5fd 75%, #dbeafe 100%);
  position: relative;
  overflow: hidden;
}

.landing-page::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(147, 197, 253, 0.2) 0%, transparent 50%),
    radial-gradient(circle at 40% 80%, rgba(30, 58, 138, 0.1) 0%, transparent 50%);
  pointer-events: none;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  position: relative;
  z-index: 1;
}

.navbar {
  padding: 1.5rem 0;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h2 {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav-links {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn {
  padding: 12px 24px;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn:not(.btn-secondary) {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  color: #1e3a8a;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.btn:not(.btn-secondary):hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
}

.hero {
  padding: 6rem 0;
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 4rem;
  font-weight: 900;
  margin-bottom: 1.5rem;
  line-height: 1.1;
  background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 50%, #b3e5fc 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  letter-spacing: -0.02em;
}

.hero-subtitle {
  font-size: 1.3rem;
  margin-bottom: 3rem;
  opacity: 0.95;
  max-width: 650px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
  font-weight: 400;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-actions {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  margin-bottom: 6rem;
  flex-wrap: wrap;
}

.btn-large {
  padding: 18px 36px;
  font-size: 1.1rem;
  font-weight: 700;
  border-radius: 60px;
}

.features {
  margin-top: 5rem;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2.5rem;
  margin-top: 3rem;
}

.feature-card {
  background: rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 3rem 2rem;
  text-align: center;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.feature-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  background: rgba(255, 255, 255, 0.18);
  border-color: rgba(255, 255, 255, 0.3);
}

.feature-card:hover::before {
  opacity: 1;
}

.feature-icon {
  font-size: 3.5rem;
  margin-bottom: 1.5rem;
  display: block;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.feature-card h3 {
  font-size: 1.6rem;
  margin-bottom: 1rem;
  font-weight: 700;
  color: white;
  position: relative;
  z-index: 1;
}

.feature-card p {
  opacity: 0.9;
  line-height: 1.6;
  font-size: 1rem;
  position: relative;
  z-index: 1;
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.8rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
    padding: 0 1rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }
  
  .nav-links {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .feature-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .feature-card {
    padding: 2rem 1.5rem;
  }
  
  .container {
    padding: 0 1rem;
  }
}

/* Footer Styles */
.footer {
  background: rgba(30, 58, 138, 0.95);
  backdrop-filter: blur(20px);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
  position: relative;
  overflow: hidden;
}

.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 90% 80%, rgba(147, 197, 253, 0.1) 0%, transparent 50%);
  pointer-events: none;
}

.footer-content {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr;
  gap: 3rem;
  padding: 4rem 0 2rem;
  position: relative;
  z-index: 1;
}

.footer-section h3,
.footer-section h4 {
  margin-bottom: 1.5rem;
  font-weight: 700;
}

.footer-logo h3 {
  font-size: 1.8rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.footer-logo p {
  opacity: 0.8;
  line-height: 1.6;
  max-width: 280px;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.8rem;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.footer-links a:hover {
  color: white;
  padding-left: 0.5rem;
}

.social-links {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 45px;
  height: 45px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  text-decoration: none;
  font-size: 1.2rem;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.social-link:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.newsletter p {
  font-size: 0.9rem;
  opacity: 0.8;
  margin-bottom: 1rem;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.newsletter-input {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 25px;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  font-size: 0.9rem;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.newsletter-input::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.newsletter-input:focus {
  outline: none;
  border-color: rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.15);
}

.newsletter-btn {
  padding: 12px 20px;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  color: #1e3a8a;
  border: none;
  border-radius: 25px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.newsletter-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 2rem 0;
  position: relative;
  z-index: 1;
}

.footer-bottom-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.footer-bottom p {
  margin: 0;
  opacity: 0.7;
  font-size: 0.9rem;
}

.footer-bottom-links {
  display: flex;
  gap: 2rem;
}

.footer-bottom-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
  color: white;
}

@media (max-width: 1024px) {
  .footer-content {
    grid-template-columns: 2fr 1fr 1fr 2fr;
    gap: 2rem;
  }
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.8rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
    padding: 0 1rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }
  
  .nav-links {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .feature-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .feature-card {
    padding: 2rem 1.5rem;
  }
  
  .container {
    padding: 0 1rem;
  }
  
  .footer-content {
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }
  
  .footer-bottom-content {
    flex-direction: column;
    text-align: center;
  }
  
  .footer-bottom-links {
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .hero-content h1 {
    font-size: 2.2rem;
  }
  
  .logo h2 {
    font-size: 1.6rem;
  }
  
  .btn-large {
    padding: 16px 28px;
    font-size: 1rem;
  }
  
  .footer-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
  }
  
  .newsletter-form {
    flex-direction: column;
  }
  
  .social-links {
    justify-content: center;
  }
  
  .footer-bottom-links {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>