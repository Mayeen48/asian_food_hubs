
# E‑Commerce Catalog (Laravel 12 + Vue 3 + Vuetify + Docker)

## Quick Start
```bash
# 1) Start containers
docker compose up -d --build

# 2) Bootstrap Laravel inside backend/
docker compose exec php bash -lc "cd backend && ./bootstrap.sh"

# 3) Open
# Frontend: http://localhost:5173
# API:      http://localhost:8083/api
```

## Admin Login (create user)
```bash
# Create admin user
docker compose exec php bash -lc "cd backend && php artisan tinker --execute='\\App\\Models\\User::updateOrCreate(["email"=>"admin@example.com"],["name"=>"Admin","password"=>bcrypt("password")]);'"
```

## Notes
- Admin form: http://localhost:5173/admin
- Catalog (PDF-like): http://localhost:5173 (use Print to save as PDF)
- MySQL host (from PHP): mysql / DB ec_shop / user ec_user / pass password
