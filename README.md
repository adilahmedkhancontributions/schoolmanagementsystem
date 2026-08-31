# School Management System

A full-featured, multi-role School Management System built with Laravel 11,
Livewire 4, Tailwind CSS 3, Alpine.js 3, and Spatie Laravel Permission.

Designed for private schools (Pakistan/international), with a responsive
mobile-first UI, per-school theming, and a SaaS-ready multi-school
architecture.

See [PROGRESS.md](PROGRESS.md) for the full feature roadmap and status.
See [DEPLOYMENT.md](DEPLOYMENT.md) for step-by-step Hostinger + MySQL
deployment instructions.

---

## Roles

| Role | Description |
|---|---|
| Super Admin | Cross-school platform management |
| School Admin | Full school-level administration |
| Teacher | Class/subject teaching, grading, attendance |
| Student | Own grades, attendance, fees, homework |
| Parent | Per-child views with child switcher |

---

## Features (MVP Complete)

### Core Administration
- **User & Role Management** — Spatie roles (super_admin, school_admin, teacher, student, parent, staff), per-role sidebar + bottom nav
- **Multi-School Architecture** — single database, `school_id` scoping on all tenant data
- **School Theming** — per-school logo, primary/secondary colors applied via CSS variables (no rebuild required)
- **Audit Logs** — generic audit trail on Student, Teacher, FeeInvoice, FeePayment, ExamResult, Attendance

### Admissions
- **Inquiry Intake** — applicant/father name, contact, DOB, address, class, source
- **Pipeline Stages** — interview → test → offer → enroll, with notes at each stage
- **One-click Enrollment** — converts applicant to a real Student account with auto-generated password

### Student & Staff Management
- **Students** — full CRUD, class/section placement, profile + documents
- **Teachers** — CRUD with User + Teacher record, employee ID, qualifications
- **Staff** — non-teaching staff CRUD with designation and department
- **Parent/Guardian** — guardian ↔ student pivot, child switcher

### Academics
- **Classes, Sections, Subjects** — class-teacher assignment, subject-teacher linking, elective flag
- **Timetable Builder** — weekly grid per class/section, auto-fills teacher from subject
- **Timetable Change Requests** — teacher submits → admin approves/rejects, conflict-safe
- **Homework** — assign per class/subject with attachments, student submissions + grading

### Attendance
- **Student Attendance** — daily per-section, mobile-first chip buttons, "mark all" shortcuts
- **Staff Attendance** — daily per-staff, School Admin marking
- **Student/Parent View** — monthly summary, history, circular attendance-rate ring

### Fees & Finance
- **Fee Structures** — reusable templates (name, amount, class scope, frequency)
- **Invoice Generation** — single student, whole class, or whole school
- **Manual Payment Recording** — cash/bank/cheque/online, partial payments, payment history
- **Student/Parent "My Fees"** — invoices, totals, parent child switcher
- **Fee Discounts** — sibling/scholarship/staff-child/need-based/custom (% or fixed)
- **Fee Defaulters** — class-wise outstanding list, CSV export

### Examinations & Grades
- **Exam Setup** — per-class, pick subjects with max/pass marks
- **Grade Entry** — Teacher (own subjects) or School Admin, batch save
- **Report Cards** — per-exam subject breakdown, total %, letter grade, pass/fail
- **Analytics** — class averages, top scorers, per-subject breakdown

### Reporting
- **Attendance Report** — per-student present/absent/late/leave counts, date range + class filter, CSV export
- **Exam Report** — student rankings, class average, pass rate, CSV export
- **Fee Collection Report** — billed/collected/outstanding, per-class breakdown, overdue list, CSV export

### Data Import / Export
- **CSV Student Import** — template download, upload + preview, per-row validation, commit, credentials export
- **CSV Export** — Students, Teachers, Staff, Fee Invoices, Attendance, Exam Results

### Communication
- **Announcements** — audience-scoped (everyone/teachers/students/parents), optional class scope, scheduled publishing
- **Teacher ↔ Parent Messaging** — direct threads per child, derived contact lists (not free-for-all), unread badges, 5s polling

### CMS & Public Site
- **Public School Site** — per-school at `/s/{slug}`, themed with school colors
- **CMS Pages** — admin CRUD with WYSIWYG editor, SEO fields, draft/published
- **Blog Posts** — featured image, scheduled publishing
- **Gallery** — multi-upload, reorder, captions
- **Contact Form** — honeypot + throttle spam protection

### Leave Management
- **Student Leave** — parent submits on behalf of child (date range + reason)
- **Staff/Teacher Leave** — teacher submits for self
- **Admin Approval** — School Admin approves/rejects from one screen with type filter

### Notifications
- **In-App + Email** — timetable change notifications to affected students, guardians, and teachers
- **Notification Bell** — unread count, dropdown, mark-one/mark-all read

### Mobile & PWA
- **Responsive Design** — sidebar → bottom nav, card layouts on mobile, 16px font fix for iOS
- **PWA Manifest** — Add to Home Screen on iOS and Android, standalone display

---

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Livewire 4, Tailwind CSS 3, Alpine.js 3
- **Auth/Scaffold:** Laravel Breeze (Blade)
- **Roles:** Spatie Laravel Permission 6
- **PWA:** erag/laravel-pwa 2
- **Database:** MySQL 8 (or SQLite for local dev)
- **No WebSocket/Redis required** — polling-based real-time (messaging, notifications)

---

## Requirements

- PHP 8.2+
- Composer 2
- Node.js 18+ and npm
- MySQL 8 (or SQLite for local development)

---

## First-time setup

```bash
# 1. Install PHP and JS dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
# SQLite (fastest for local dev):
touch database/database.sqlite
# and in .env set: DB_CONNECTION=sqlite   (comment out DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD)
#
# MySQL: create a database and set DB_* in .env accordingly, then:
php artisan migrate --seed

# 4. Build frontend assets
npm run build

# 5. Create storage symlink (for uploaded files)
php artisan storage:link
```

---

## Running locally

```bash
php artisan serve
npm run dev   # in a separate terminal, for hot-reloading during development
```

Visit `http://localhost:8000` and log in at `/login`.

---

## Demo accounts

Seeded by `database/seeders/DemoDataSeeder.php`. Password for all: `password`.

| Role | Email |
|---|---|
| Super Admin | superadmin@example.com |
| School Admin | admin@demoschool.test |
| Teacher | teacher@demoschool.test |
| Student | student@demoschool.test |
| Parent | parent@demoschool.test |

---

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for the full step-by-step guide
covering Hostinger shared hosting, MySQL setup, file upload, `.env`
configuration, migrations, SSL, cron, and troubleshooting.

**Quick summary:**
1. `composer install --no-dev --optimize-autoloader && npm install && npm run build`
2. Upload everything (minus `node_modules/`, `.git/`) to `public_html/`
3. Create MySQL database in hPanel, update `.env`
4. `php artisan key:generate && php artisan migrate --seed && php artisan storage:link`
5. Install SSL via hPanel, force HTTPS

---

## Useful commands

```bash
php artisan migrate:fresh --seed   # reset the database and reseed demo data
php artisan route:list             # inspect registered routes
php artisan pint                   # code style check/fix (PSR-12)
php artisan test                   # run the test suite
php artisan config:cache           # cache config for production
php artisan route:cache            # cache routes for production
php artisan view:cache             # cache Blade views for production
```

---

## Project structure

```
app/
  Livewire/                        # Livewire 4 components, organized by role/module
    Dashboard.php                  # Role-based dashboard (same component, different renders)
    Attendance/                    # Mark, MyAttendance
    Exams/                         # GradeEntry, ReportCard
    Fees/                          # MyFees
    Homework/                      # Manage, MyHomework
    Leave/                         # MyLeave (teacher/parent)
    Messaging/                     # Inbox (teacher/parent)
    Announcements/                 # Feed (teacher/student/parent)
    Timetable/                     # MyTimetable, AllTimetables
    StaffAttendance/               # Mark, MyAttendance
    SchoolAdmin/                   # All admin CRUD modules
    SuperAdmin/                    # Super Admin schools management
  Models/                          # Eloquent models with relationships
  Support/
    Navigation.php                 # Single source of truth for sidebar/bottom-nav per role
    Auditable.php                  # Generic audit trail trait
    HtmlSanitizer.php              # CMS content sanitization
    TimetableNotifier.php          # Timetable change notification dispatcher
database/
  migrations/                      # All table definitions
  seeders/
    RolePermissionSeeder.php       # Roles + permissions
    DemoDataSeeder.php             # Full demo dataset (60+ students, teachers, etc.)
resources/
  views/livewire/                  # Blade views for each Livewire component
  views/layouts/dashboard.blade.php  # Shared shell (sidebar, topbar, bottom nav)
  css/app.css                      # Tailwind + CSS custom properties (brand theming)
routes/web.php                     # All routes (Livewire component routes)
```

---

## License

Private/proprietary. Not licensed for redistribution.
