# Deployment Guide

## Coolify (VPS)

### 1. Create the app in Coolify
- Source: your Git repository
- Build Pack: **Dockerfile** (select this — do NOT use Nixpacks)
- Port: `80`

### 2. Set environment variables
Copy `.env.production` into Coolify's **Environment Variables** panel and fill in:
```
APP_KEY=           ← run: php artisan key:generate --show
APP_URL=https://yourdomain.com
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```
Do **not** set `ASSET_URL` — leave it unset.

### 3. Add persistent storage volume (CRITICAL — fixes 404 on images)
In Coolify → your app → **Storages** tab, add:

| Source path (on server)         | Destination path (in container)              |
|---------------------------------|----------------------------------------------|
| `/data/digitalp/storage`        | `/var/www/html/storage/app/public`           |

This keeps uploaded images across deploys. Without this, every deploy wipes your uploads.

### 4. Deploy
Click **Deploy**. The Docker build will:
- Install PHP extensions, Composer, Node
- Install Composer & npm dependencies
- Build frontend assets (`npm run build`)

On container start, it will automatically:
- Run `php artisan storage:link --force`
- Run `php artisan migrate --force`
- Cache config/routes/views/events
- Start nginx + php-fpm via supervisord

---

## cPanel (Shared Hosting)

### Option A — Subdomain pointing to `public/`
1. Upload project to `/home/user/digitalp/` (outside `public_html`)
2. In cPanel → **Subdomains**, create `app.yourdomain.com` pointing to `/home/user/digitalp/public`
3. SSH in and run:
   ```bash
   cd /home/user/digitalp
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   cp .env.production .env
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Option B — Main domain (`public_html`)
1. Upload project to `/home/user/digitalp/`
2. In cPanel File Manager, move everything inside `public/` to `public_html/`
3. Edit `public_html/index.php` — update the path to `bootstrap/app.php`:
   ```php
   $app = require_once __DIR__.'/../digitalp/bootstrap/app.php';
   ```
4. SSH in and run `php artisan storage:link` (creates symlink inside `public_html/storage`)

---

## Why images were 404

| Cause | Fix |
|---|---|
| `public/storage` symlink missing | `php artisan storage:link --force` runs on every container start |
| Uploaded files lost on redeploy | Persistent volume mounted at `storage/app/public` |
| Wrong image URL generated | `AppServiceProvider` auto-detects base URL from HTTP request — no hardcoded path |
| `ASSET_URL=/digitalp` on VPS | Remove `ASSET_URL` from production `.env` |
