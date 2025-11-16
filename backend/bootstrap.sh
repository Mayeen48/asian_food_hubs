#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"

if [ ! -f artisan ]; then
  echo ">>> Installing Laravel 12 into backend/"
  composer create-project laravel/laravel .

  echo ">>> Requiring packages"
  composer require laravel/sanctum spatie/laravel-permission

  echo ">>> Publishing vendors"
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --force
  php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force

  echo ">>> Writing .env (if missing)"
  if [ ! -f .env ]; then
    cp .env.example .env
  fi

  # write sane defaults into .env
  php -r '
    $env = file_get_contents(".env");
    $env = preg_replace("/^APP_NAME=.*/m", "APP_NAME=ECShop", $env);
    $env = preg_replace("/^APP_URL=.*/m", "APP_URL=http://localhost:8083", $env);
    $env = preg_replace("/^DB_HOST=.*/m", "DB_HOST=mysql", $env);
    $env = preg_replace("/^DB_PORT=.*/m", "DB_PORT=3306", $env);
    $env = preg_replace("/^DB_DATABASE=.*/m", "DB_DATABASE=ec_shop", $env);
    $env = preg_replace("/^DB_USERNAME=.*/m", "DB_USERNAME=ec_user", $env);
    $env = preg_replace("/^DB_PASSWORD=.*/m", "DB_PASSWORD=password", $env);
    if (strpos($env, "SANCTUM_STATEFUL_DOMAINS=") === false) {
      $env .= "\nSANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173\n";
    } else {
      $env = preg_replace("/^SANCTUM_STATEFUL_DOMAINS=.*/m", "SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173", $env);
    }
    if (strpos($env, "FRONTEND_URL=") === false) {
      $env .= "FRONTEND_URL=http://localhost:5173\n";
    } else {
      $env = preg_replace("/^FRONTEND_URL=.*/m", "FRONTEND_URL=http://localhost:5173", $env);
    }
    file_put_contents(".env", $env);
  '

  php artisan key:generate

  echo ">>> Copying API stubs"
  cp -R ./_stubs/app ./
  cp -R ./_stubs/routes ./
  cp -R ./_stubs/database ./

  echo ">>> Migrate & seed"
  php artisan migrate --seed || true
fi

echo ">>> Backend bootstrap complete."
