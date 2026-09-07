# Deployment Guide — Hostinger Shared Hosting (MySQL)

Step-by-step instructions to deploy the School Management System on
Hostinger's shared hosting plan using a MySQL database.

---

## Prerequisites

- A Hostinger **Premium** or **Business** shared hosting plan (PHP 8.2+ required)
- Access to Hostinger **hPanel** (control panel)
- **File Manager** or **FTP client** (FileZilla / WinSCP) for file uploads
- **phpMyAdmin** (included in hPanel) or SSH access for database operations
- Composer installed locally (for building production assets)

---

## 1. Prepare locally

```bash
# From the project root on your local machine
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

This compiles Tailwind CSS and all frontend assets into `public/build/`.

---

## 2. Create a MySQL database in Hostinger hPanel

1. Log in to **hPanel** → **Databases** → **MySQL Databases**.
2. Click **Create Database**.
3. Note down:
   - **Database name** (e.g., `u123456789_school`)
   - **Database username** (e.g., `u123456789_admin`)
   - **Database password** (set a strong password)
4. Grant **All Privileges** to the user on that database.

---

## 3. Upload files

Upload the **entire project folder** (excluding `node_modules/`, `.git/`,
`vendor/`) to your Hostinger hosting via one of:

### Option A — File Manager (hPanel)
1. hPanel → **Files** → **File Manager** → navigate to `public_html/`.
2. Upload the project files. For large uploads, zip first, upload, then
   extract via File Manager.

### Option B — Git (if SSH is available)
```bash
cd ~/public_html
git clone <your-repo-url> .
```

### Option C — FTP
Use FileZilla or WinSCP to upload to `public_html/`.

**Important:** Upload the entire project to `public_html/` so that
`public_html/public/` contains Laravel's `index.php` entry point.

---

## 4. Configure `.env`

In `public_html/`, copy `.env.example` to `.env` and edit:

```ini
APP_NAME="School Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# MySQL (use the credentials from Step 2)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u456734894_smsdb
DB_USERNAME=u456734894_smsdb
DB_PASSWORD=Smsdbpassword5388

# Sessions (database-backed, configured by default)
SESSION_DRIVER=database

# Cache (use database since Redis is not available on shared hosting)
CACHE_STORE=database

# Queue (database since no Redis/SQS on shared hosting)
QUEUE_CONNECTION=database

# Mail (optional — update if you have SMTP credentials)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=you@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 5. Generate application key

If you have SSH access:
```bash
cd ~/public_html
php artisan key:generate
```

If no SSH, generate a key locally with `php artisan key:generate`, then
paste the resulting `base64:...` string into `APP_KEY=` in `.env`.

---

## 6. Create storage symlink

If you have SSH:
```bash
cd ~/public_html
php artisan storage:link
```

If no SSH, the uploaded school logos, CMS images, and documents need to be
accessible via `/storage/...` URLs. On Hostinger shared hosting, `storage:link`
may fail due to symlink restrictions. As a workaround:

1. In hPanel **File Manager**, navigate to `public_html/public/`.
2. Create a folder called `storage` inside `public/`.
3. Copy (not move) the contents of `storage/app/public/` into `public/storage/`.
4. This is a manual sync — repeat after each upload.

Alternatively, if SSH is available, create the symlink manually:
```bash
ln -s ~/storage/app/public ~/public_html/public/storage
```

---

## 7. Run database migrations and seed

### Option A — SSH (recommended)
```bash
cd ~/public_html
php artisan migrate --seed
```

### Option B — phpMyAdmin (hPanel)
1. hPanel → **Databases** → **phpMyAdmin**.
2. Select your database.
3. Import the migration SQL files manually (not recommended for ongoing use).

### Option C — Tinker (limited, no seeder)
Not recommended — use SSH or set up the database locally and export.

**After seeding, the demo credentials are:**

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@example.com | password |
| School Admin | admin@demoschool.test | password |
| Teacher | teacher@demoschool.test | password |
| Student | student@demoschool.test | password |
| Parent | parent@demoschool.test | password |

> **Security:** After verifying the app works, change all passwords or
> remove demo users in production.

---

## 8. Set directory permissions

Hostinger typically has correct defaults, but if uploads fail:

```
storage/           → 775 (recursive)
bootstrap/cache/   → 775 (recursive)
public/            → 755
```

Use File Manager → right-click folder → Permissions, or via SSH:
```bash
chmod -R 775 storage bootstrap/cache
chmod -R 755 public
```

---

## 9. Configure the web root

Hostinger's default document root is `public_html/`. Since Laravel's
entry point is `public/index.php`, you have two options:

### Option A — Move public/ contents up (simplest)
Copy the contents of `public/` into the root `public_html/` directory:
```
public_html/
  index.php          ← from public/index.php
  build/             ← from public/build/
  storage/           ← symlink or copy from storage/app/public/
  ...
app/
bootstrap/
config/
database/
resources/
routes/
vendor/
.env
```

Then edit `public_html/index.php` and change:
```php
require __DIR__.'/../vendor/autoload.php';
```
to:
```php
require __DIR__.'/vendor/autoload.php';
```
and:
```php
$app = require_once __DIR__.'/../bootstrap/app.php';
```
to:
```php
$app = require_once __DIR__.'/bootstrap/app.php';
```

### Option B — Point document root to public/ (cleaner)
In hPanel → **Advanced** → **Domains** or **PHP Settings**, if your host
lets you set the document root, point it to `public_html/public/` instead
of `public_html/`. No file moves needed. (Not all Hostinger plans support
this — check your hPanel settings.)

---

## 10. Set up cron job (optional but recommended)

The database-backed queue and cache are handled automatically. For session
cleanup and any future scheduled tasks:

```bash
# SSH
crontab -e
```

Add:
```
* * * * * cd ~/public_html && php artisan schedule:run >> /dev/null 2>&1
```

If no SSH, Hostinger's hPanel → **Cron Jobs** lets you add a cron entry.

---

## 11. SSL certificate

Hostinger provides free **Let's Encrypt SSL** via hPanel:
1. hPanel → **SSL** → **Install SSL**.
2. Select your domain → **Install**.
3. Force HTTPS: add to `.env`:
   ```ini
   APP_URL=https://yourdomain.com
   ```
4. Optionally redirect HTTP → HTTPS via `.htaccess` in `public_html/`:
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

---

## 12. Post-deployment checklist

- [ ] Visit `https://yourdomain.com` — the welcome page should load.
- [ ] Log in as `admin@demoschool.test` — the School Admin dashboard should
      render with sidebar, topbar, and metric cards.
- [ ] Click through every sidebar link (Admissions, Students, Teachers,
      Classes, Subjects, Timetable, Attendance, Fees, Exams, Homework,
      Leave Requests, Reports, CMS, Announcements, Data Import/Export,
      School Profile).
- [ ] Log in as `teacher@demoschool.test` — check Attendance, Grades,
      Homework, Leave, Messages, Timetable.
- [ ] Log in as `student@demoschool.test` — check Attendance, Grades,
      Homework, Fees, Timetable.
- [ ] Log in as `parent@demoschool.test` — check Attendance, Grades,
      Homework, Leave, Fees, Messages.
- [ ] Log in as `superadmin@example.com` — check Schools list.
- [ ] Test a file upload (e.g., School Profile logo) to confirm
      `storage:link` or manual sync works.
- [ ] Visit `/s/demo-public-school` — the public school site should load.

---

## Troubleshooting

| Symptom | Fix |
|---|---|
| White screen / 500 error | Check `storage/logs/laravel.log`. Usually a missing `.env` key or wrong DB credentials. |
| `APP_KEY` missing error | Run `php artisan key:generate` or manually set `APP_KEY` in `.env`. |
| Uploaded images show 404 | Run `php artisan storage:link` or manually copy `storage/app/public/` to `public/storage/`. |
| Migrations fail | Confirm MySQL credentials in `.env` match what hPanel shows. Check the database user has ALL PRIVILEGES. |
| `composer` not found | Install via SSH: `curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer`. Or build locally and upload `vendor/`. |
| `npm` not found | Always run `npm install && npm run build` locally, then upload the compiled `public/build/` directory. |
| Session errors | Confirm `SESSION_DRIVER=database` in `.env` and that the `sessions` table exists (migrations create it). |
| Assets not loading | Confirm `public/build/` was uploaded and contains the compiled CSS/JS. Clear browser cache. |

---

## Environment variables reference

| Variable | Value for production |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://yourdomain.com` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` (Hostinger MySQL is local) |
| `DB_PORT` | `3306` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `FILESYSTEM_DISK` | `local` |
| `MAIL_MAILER` | `smtp` (if you have SMTP credentials) |

---

## Updating the app after deployment

```bash
# SSH into the server
cd ~/public_html
git pull origin main           # if using git
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If no SSH, re-upload changed files and run migrations via phpMyAdmin or
a one-off SSH session from hPanel.

---

## Backups

- **Database:** hPanel → **Databases** → **Backups** → download a `.sql` file.
- **Files:** hPanel → **Files** → **Backups** → download a zip of `public_html/`.
- **Automated:** Hostinger's Business plan includes weekly automated backups.
  For more frequent backups, use SSH + cron to dump the database daily:
  ```bash
  0 2 * * * mysqldump -uUSER -pPASSWORD DATABASE > ~/backups/school-$(date +\%F).sql
  ```
