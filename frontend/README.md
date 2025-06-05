# Askify

>   Askify is an AI-powered learning assistant that helps students ask questions about their lesson documents. Admins can upload and manage documents, while students can query them intelligently using natural language. Askify connects with Gemini to analyze document content and provide smart, instant answers all from a simple Laravel backend.

### Installation

Built using Vue.js. Uses Vue Router for page navigation.

1. Clone the repo

```bash
git clone https://github.com/Musoye/Askify.git
cd Askify
```

2. Install frontend dependencies

```bash
cd frontend
npm install
```

3. Running the App

Run frontend server:

```bash
# Make sure you are in frontend directory
cd ./frontend

# Start frontend dev server
npm run dev

# Compile and Minify for Production
npm run build
```

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