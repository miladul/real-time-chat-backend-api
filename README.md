# Real-Time Chat Application

A real-time chat application built with **Laravel API** (backend) and **Vue 3 + Vite** (frontend), powered by **Pusher** for instant messaging.

---

## ⚡ Features
- Real-time private messaging between users
- Laravel API for authentication & chat handling
- Vue 3 frontend with Vite
- Pusher for real-time broadcasting
- Queue system for async message processing

---

## 📌 Requirements
- PHP >= 8.2
- Composer
- MySQL
- Node.js >= 16
- Pusher account

---

## 🚀 Backend Setup (Laravel API)

### 1. Clone the repository
```bash
git clone https://github.com/miladul/real-time-chat-backend-api.git
cd real-time-chat-backend-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Create `.env` file
```bash
cp .env.example .env
```

### 4. Update `.env` configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

QUEUE_CONNECTION=database
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=app_id
PUSHER_APP_KEY=app_key
PUSHER_APP_SECRET=app_secret
PUSHER_APP_CLUSTER=app_cluster

FRONTEND_URL=your_front_end_url
```

### 5. Generate application key
```bash
php artisan key:generate
```

### 6. Run migrations
```bash
php artisan migrate
```

### 7. Start the server
```bash
php artisan serve
```

### 8. Start the queue worker
```bash
php artisan queue:work
```

### 9. Feature tests by running
```bash
php artisan test --filter=ChatSystemTest

````
## 📡 API Endpoints
### Public Endpoints
| Method | Endpoint       | Description              |
|--------|---------------|--------------------------|
| POST   | `/register`   | Register a new user      |
| POST   | `/login`      | Login and get a token    |

### Protected Endpoints (Require `auth:sanctum`)
| Method | Endpoint                  | Description                    |
|--------|---------------------------|--------------------------------|
| POST   | `/logout`                 | Logout the authenticated user |
| GET    | `/me`                     | Get authenticated user info   |
| GET    | `/users`                  | Get list of all users         |
| GET    | `/messages/{user}`        | Get chat history with a user  |
| POST   | `/messages`               | Send a new message            |
| POST   | `/typing`                 | Send typing indicator         |

---

---

## 💻 Frontend Setup (Vue 3 + Vite)

### 1. Clone the repository
```bash
git clone https://github.com/miladul/real-time-chat-frontend.git
cd real-time-chat-frontend
```

### 2. Install dependencies
```bash
npm install
```

### 3. Create `.env` file
```bash
touch .env
```
Add the following content:
```env
VITE_PUSHER_APP_KEY=app_key
VITE_PUSHER_APP_CLUSTER=app_cluster
VITE_BACKEND_BASE_URL=laravel_backend_url

e.g: 
VITE_BACKEND_BASE_URL=http://localhost:8000

```

### 4. Start the development server
```bash
npm run dev
```

---
**Developed by [Miladul Islam](https://github.com/miladul)**
