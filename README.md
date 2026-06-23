# Emitrack

## Pembuat: 2602068170 - Yodi Satria

## Stack
- **Laravel**
- **Vite**
- **Mysql**

---

## Requirements
- PHP **8.4** and Composer **2.8.x**

---

## Quick Start (Local)

```bash
# 1) Clone
git clone https://github.com/kakuryouh/emitrack.git
cd emitrack

# 2) Install PHP deps
composer install --no-interaction --prefer-dist

# 3) Install JS deps
npm install

# 4) Copy env & generate key
cp .env.example .env          # Windows PowerShell: copy .env.example .env
php artisan key:generate

# 5) Configure DB in .env
#   DB_CONNECTION=sqlite or any other DB you prefer
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=your_db
#   DB_USERNAME=your_user
#   DB_PASSWORD=your_pass

# 6) Migrate (and optionally seed)
php artisan migrate --seed

# 7) Link storage for public files (user uploads, images, etc.)
php artisan storage:link

# 8) Start servers (use two terminals)
php artisan serve         # http://127.0.0.1:8000

#or use Herd
```