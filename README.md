# Askify

> Askify is an AI-powered learning assistant that helps students ask questions about their lesson documents. Admins can upload and manage documents, while students can query them intelligently using natural language. Askify connects with Gemini to analyze document content and provide smart, instant answers all from a simple Laravel backend.

---

## Table of Contents

- [Project Overview](#project-overview)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)

  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Running the App](#running-the-app)

- [Frontend](#frontend)

  - [Routing](#routing)

- [Backend](#backend)

  - [API](#api)
  - [Environment Variables](#environment-variables)

- [Database](#database)
- [AI Integration](#ai-integration)
- [Roles & Features](#roles--features)
- [How It Works](#how-it-works)
- [License](#license)

---

## Project Overview

This project is a full-stack web application built with a Vue frontend, Laravel backend API, and integrated AI services using the Gemini API.

---

## Tech Stack

- **Frontend:** Vue.js with Vue Router for client-side routing
- **Backend:** Laravel 12.0.9 (PHP framework)
- **Database:** MySQL/PostgreSQL (or your choice)
- **AI Tool:** Gemini API for AI-driven functionalities
- **Environment:** Laravel `.env` file for environment variables

---

## Getting Started

### Prerequisites

- PHP >= 8.2 with Composer
- Node.js (v14+ recommended)
- npm or yarn
- MySQL/PostgreSQL running locally or remote
- Gemini API key (you must get this from the Gemini provider)

### Installation

1. Clone the repo

```bash
git clone https://github.com/Musoye/Askify.git
cd Askify
```

2. Install backend dependencies

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

3. Install frontend dependencies

```bash
cd ../frontend
npm install
```

### Running the App

Run frontend and backend servers:

```bash
# Start backend server
cd backend
php artisan serve

# Start frontend dev server
cd ../frontend
npm run dev
```

Frontend app runs on `http://localhost:5173` and backend API on `http://localhost:8000` (or configured ports).

---

## Frontend

Built using Vue.js. Uses Vue Router for page navigation.

### Routing

Routing is handled with `vue-router`:

```js
const routes = [
  { path: "/", component: LandingPage },
  { path: "/signup", component: SignUp },
  { path: "/login", component: Login },
  { path: "/admin", component: AdminDashboard },
  { path: "/student", component: StudentDashboard },
  { path: "/document/:id", component: DocumentView },
  { path: "/chat/:id", component: ChatWithDocument },
];
```

---

## Backend

Laravel 12.0.9 powers the backend with a RESTful API structure.

### API

#### Authentication

We use **Bearer Token** authentication via Laravel Sanctum to keep things secure and simple.

- `POST /api/register` — Register a new user
- `POST /api/login` — Login user
- `GET /api/logout` — Logout authenticated user _(requires `auth:sanctum`)_

When registering users, include:

- `name` (string)
- `email` (string)
- `password` (string)
- `role` (string)

#### Documents

- `POST /api/documents` — Create a new document _(auth + admin)_
- `GET /api/documents` — List all documents _(auth)_
- `GET /api/documents/{id}` — Show single document _(auth)_
- `PUT /api/documents/{id}` — Update document _(auth + admin)_
- `DELETE /api/documents/{id}` — Delete document _(auth + admin)_
- `GET /api/documents/{id}/view` — Public view of document

To create a document, send:

- `title` (string)
- `description` (string)
- `file` (file)

Other values like `filepath`, `gemini_id`, and `expires_at` will be automatically handled by the backend.

#### Questions

- `POST /api/questions` — Create a question _(auth)_
- `GET /api/questions` — List all questions _(auth + admin)_
- `GET /api/questions/{id}` — Show a question _(auth)_
- `PUT /api/questions/{id}` — Update a question _(auth)_
- `DELETE /api/questions/{id}` — Delete a question _(auth + admin)_
- `GET /api/questions/{document_id}/questions` — Get questions by document _(auth + admin)_
- `GET /api/questions/{document_id}/users` — Get user-submitted questions for document _(auth)_

To submit a question, send:

- `user_id` (will be get from the autenticated user making the request)
- `document_id` (int)
- `question` (string)

Answers are generated automatically by the AI using your question and the document.

All routes are defined in `routes/api.php` and protected appropriately using middleware.

### Environment Variables

Create a `.env` file in the Laravel root folder:

```env
APP_NAME=YourApp
APP_ENV=local
APP_KEY=base64:generatedkey
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=
GEMINI_API_KEY=your_gemini_api_key_here
```

Keep `.env` out of version control.

---

## Database

Use Laravel migrations to set up tables. Configure connection in `.env`. Example:

```bash
php artisan migrate
```

Models and migrations are located in `/app/Models` and `/database/migrations`.

---

## AI Integration

We integrate the Gemini API to power AI-based features in this app. Here’s how it works:

Initially, we explored two different methods as contained in Gemini Documentation::

1. **File Upload with URI:**

   - We uploaded document files to Gemini
   - Then used the URI for making AI queries
   - Unfortunately, this approach gave inconsistent results, so we moved on

2. **Base64 Document + Query (Current & Preferred):**

   - We convert the document to base64 and send it along with the user’s question to Gemini
   - This method has proven much more reliable
   - It requires a `GEMINI_API_KEY`, which must be added to your backend `.env`

All Gemini logic lives in a dedicated service class on the backend to keep things clean and modular.

---

## Roles & Features

### 👤 User Features

**Frontend:**

- Register and log in
- View available documents
- Ask questions related to documents
- View their own questions and AI-generated answers
- Logout and secure session handling

**Backend:**

- Register/login/logout via API
- Fetch documents
- Submit and retrieve questions
- AI responses automatically returned

---

### 🛠️ Admin Features

**Frontend:**

- Admin login
- Access dashboard with documents and questions overview
- Add/edit/remove documents
- Moderate or delete any question

**Backend:**

- Full document CRUD (Create, Read, Update, Delete)
- View all user-submitted questions
- Delete or manage specific questions

Admin access is protected by middleware and only granted based on user roles.

---

## How It Works

1. Users access the Vue frontend
2. Vue Router handles route navigation
3. Vue makes API requests to Laravel backend
4. Backend authenticates, queries DB, or fetches from Gemini API
5. AI responses are forwarded to the frontend for display
6. State is managed by ref of Vue

This setup ensures clean separation of concerns, secure AI key usage, and flexible fullstack performance.

---
