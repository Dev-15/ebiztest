# – Report & Video Job System

This project is a small-scale distributed web application built with **Laravel 11**, showcasing:

-  Authentication (bcrypt/argon2id + pepper, lockouts, rate limiting)  
-  Background job pipeline for **report generation (100k rows)**  
-  Stored procedure for aggregated queries  
-  Video upload & **FFmpeg compression** (server + client demo)  
-  Queue workers (Redis)  
-  Docker-based reproducible setup  
-  Automated tests + logs  

---

##  1. Project Setup

### Requirements
- PHP 8.2+  
- Composer 2+  
- MySQL 8 / PostgreSQL  
- Redis (for queue)  
- FFmpeg installed (`ffmpeg -version`)  
- Node.js (for frontend demo, optional)  
- Docker (optional, for full container setup)

### Installation

```bash
# Clone project
git clone https://github.com/Dev-15/ebiztest.git
cd ebiztest

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env
```

Edit `.env` to match your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ebiztest
DB_USERNAME=root
DB_PASSWORD=secret
```

Generate app key:

```bash
php artisan key:generate
```

Run migrations & seed sample data (~100k rows):

```bash
php artisan migrate --seed
```

---

##  2. Authentication & Security
- User registration & login endpoints:  
  - `POST /api/register`  
  - `POST /api/login`  
- Password hashing: `bcrypt`/`argon2id` + pepper.  
- Login rate limiting: **5 attempts / 1 min**, account lock after configurable failures.  

---

## ⚙️ 3. Report Generation Pipeline
- `POST /api/reports` → Creates job in DB + queue.  
- Worker (`php artisan queue:work`) generates Excel file (~100k records).  
- `GET /api/reports/{id}/status` → Check status.  
- `GET /api/reports/{id}/download` → Download report.  

 Reports stored in `storage/app/reports/`.

---

##  4. Database Stored Procedure
Example: `procedures/report_aggregation.sql`

Run manually:

```sql
SOURCE database/procedures/report_aggregation.sql;
```

Usage in Laravel:

```php
DB::select('CALL report_aggregation(?)', [$userId]);
```

---

##  5. Video Compression
- `POST /api/videos/upload` → Upload original video, compress via FFmpeg, store both.  
- Response returns metadata (file size before/after).  

Client demo:  
`resources/js/video-demo.html` → Uses `MediaRecorder` + `ffmpeg.wasm`.

---

##  6. Observability
- Job lifecycle logged in `storage/logs/laravel.log`.  
- Test coverage includes:  
  - Authentication  
  - Job queue pipeline  
  - Stored procedure call  

Run tests:

```bash
php artisan test
```

---

##  7. Docker Setup (Optional)
```bash
docker-compose up -d
```

Services:
- `php` → Laravel app  
- `mysql` → Database  
- `redis` → Queue  
- `worker` → Laravel queue worker  

---

##  8. Project Structure
```
app/Http/Controllers   → API controllers
app/Jobs               → Report & video jobs
database/migrations    → Schema definitions
database/seeders       → Sample data (~100k rows)
database/procedures    → SQL stored procedure
routes/api.php         → API endpoints
resources/js           → Client-side demo
tests/Feature          → Automated tests
```

---

##  9. Security Notes
- Passwords = `argon2id`/`bcrypt` + pepper (APP_PEPPER).  
- Account lockout on repeated failures.  
- Reports/videos stored outside `public/` for security.  
- API responses filtered (no sensitive fields).

---

##  10. Benchmarks (Sample)
- Report generation (100k rows): **~3.2s** with queue worker.  
- Video compression (10MB → 3MB): **~1.8s** on local FFmpeg.

---

##  11. Next Steps / Bonus
- Streaming Excel (Spout) for low memory usage.  
- Role-based access control.  
- Resumable uploads for large files.  
- File encryption for stored reports & videos.  

---

 Quick Start:

```bash
php artisan serve
php artisan queue:work
```

Open: [http://127.0.0.1:8000](http://127.0.0.1:8000) 🎉
