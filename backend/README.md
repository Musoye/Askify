# Askify

>   Askify is an AI-powered learning assistant that helps students ask questions about their lesson documents. Admins can upload and manage documents, while students can query them intelligently using natural language. Askify connects with Gemini to analyze document content and provide smart, instant answers all from a simple Laravel backend.

### Installation

Laravel 12.0.9 powers the backend with a RESTful API structure.

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

3. Running the App

Run backend server:

```bash
# Start backend server
php artisan serve
```

### API

#### Authentication

We use **Bearer Token** authentication via Laravel Sanctum to keep things secure and simple.

* `POST /api/register` — Register a new user
* `POST /api/login` — Login user
* `GET /api/logout` — Logout authenticated user *(requires `auth:sanctum`)*

When registering users, include:

* `name` (string)
* `email` (string)
* `password` (string)
* `role` (string)

#### Documents

* `POST /api/documents` — Create a new document *(auth + admin)*
* `GET /api/documents` — List all documents *(auth)*
* `GET /api/documents/{id}` — Show single document *(auth)*
* `PUT /api/documents/{id}` — Update document *(auth + admin)*
* `DELETE /api/documents/{id}` — Delete document *(auth + admin)*
* `GET /api/documents/{id}/view` — Public view of document
* `GET /api/documents/{id}/recommend` — Recommend based on the documents id passed *(auth)*

To create a document, send:

* `title` (string)
* `description` (string)
* `file` (file)

Other values like `filepath`, `gemini_id`, and `expires_at` will be automatically handled by the backend.

#### Questions

* `POST /api/questions` — Create a question *(auth)*
* `GET /api/questions` — List all questions *(auth + admin)*
* `GET /api/questions/{id}` — Show a question *(auth)*
* `PUT /api/questions/{id}` — Update a question *(auth)*
* `DELETE /api/questions/{id}` — Delete a question *(auth + admin)*
* `GET /api/questions/{document_id}/questions` — Get questions by document *(auth + admin)*
* `GET /api/questions/{document_id}/users` — Get user-submitted questions for document *(auth)*

To submit a question, send:

* `user_id` (This will be get from user making the request)
* `document_id` (int)
* `question` (string)

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

Initially, we explored two different methods as contained in Gemini Documentation:

1. **File Upload with URI:**

   * We uploaded document files to Gemini
   * Then used the URI for making AI queries
   * Unfortunately, this approach gave inconsistent results, so we moved on

2. **Base64 Document + Query (Current & Preferred):**

   * We convert the document to base64 and send it along with the user’s question to Gemini
   * This method has proven much more reliable
   * It requires a `GEMINI_API_KEY`, which must be added to your backend `.env`

All Gemini logic lives in a dedicated service class on the backend to keep things clean and modular.

---