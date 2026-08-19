# School Management System — Scope & Progress Log
** main Prompt is in /projectprompt.md **
This document is the single source of truth for what has been built, what is in
progress, and what remains. Update it at the end of every work session.

Stack (already scaffolded before this log started): Laravel 11, PHP 8.2+,
Livewire 4, Tailwind CSS 3, Alpine.js 3, Spatie Laravel Permission 6,
erag/laravel-pwa 2, Breeze (Blade auth scaffolding).

> **Environment note:** development in this sandbox cannot execute
> `php`, `composer`, or `npm run dev`/build commands — only file edits. Every
> change below is written to disk but **not yet executed**. Before first run:
> `composer install`, `cp .env.example .env`, `php artisan key:generate`,
> configure `DB_*` in `.env`, `php artisan migrate --seed`, `npm install && npm run build`.

## Architecture decisions (assumed, revisit if wrong)

- **Multi-school, single database**: a `schools` table + `school_id` scoping
  column on tenant-owned tables (users, students, teachers, classes, ...).
  Super Admin operates across schools; School Admin/Teacher/Student/Parent are
  scoped to one school. Chosen because the spec explicitly asks Super Admin to
  "manage schools" while also asking for shared-hosting/single-DB friendliness.
- **Roles**: `super_admin`, `school_admin`, `teacher`, `student`, `parent`
  (Spatie Permission, guard `web`).
- **Single `/dashboard` route** that renders a different Livewire component
  based on `auth()->user()->getRoleNames()`, matching Breeze's existing
  `redirect()->intended(route('dashboard'))` flow (no controller changes
  needed).
- Third-party integrations (Stripe/Razorpay/PayPal, Twilio/MSG91, S3,
  Google Maps/Analytics, social) are **not wired yet** — deferred until core
  modules exist and real credentials are available. Config placeholders will
  be added when each module needing them is built (Fees → payments, etc).

## Phase 1 — Foundation (Auth, Roles, Schema, Dashboards)

| Item | Status |
|---|---|
| Scope/progress doc (this file) | ✅ Done |
| Spatie roles/permissions migration + config + seeder | ✅ Done |
| Middleware aliases (`role`, `permission`) registered | ✅ Done |
| Core schema: schools, school_classes, sections, subjects | ✅ Done |
| Core schema: teachers, students, guardians, student_guardian pivot | ✅ Done |
| Users table: school_id, phone, avatar, status columns | ✅ Done |
| Eloquent models + relationships | ✅ Done |
| Demo data seeder (1 school, 1 user per role) | ✅ Done |
| Tailwind design tokens (colors/fonts per spec) | ✅ Done |
| Responsive dashboard shell (sidebar/topbar/bottom nav) | ✅ Done |
| Role-based dashboard Livewire components (5 roles) | ✅ Done |
| Old Breeze `layouts/app.blade.php` + top nav replaced by sidebar/topbar/bottom-nav shell | ✅ Done |
| Auth pages (login/register/etc.) re-themed with brand fonts/colors | ✅ Done |
| Login redirect verified to reach role dashboard | ⏳ Needs `composer install` + manual test (see Next Steps) |

### Files added/changed this session

- `app/Models/{School,SchoolClass,Section,Subject,Teacher,Student,Guardian}.php`, `app/Models/User.php`
- `app/Support/Navigation.php` (per-role sidebar/bottom-nav menu source of truth)
- `app/Livewire/Dashboard.php` + `resources/views/livewire/dashboard.blade.php`
- `resources/views/layouts/dashboard.blade.php` (new shell; `x-app-layout` now points here too)
- `database/migrations/2024_01_*` (permission tables, schools, user profile fields, classes, sections, subjects, teachers, students, guardians, guardian_student pivot)
- `database/seeders/{RolePermissionSeeder,DemoDataSeeder}.php`, updated `DatabaseSeeder.php`
- `config/permission.php`, `bootstrap/app.php` (role/permission middleware aliases)
- `tailwind.config.js` (brand colors, Inter/Nunito Sans, 44px touch tokens), `resources/css/app.css` (component utility classes)
- Removed: `resources/views/dashboard.blade.php`, `resources/views/layouts/app.blade.php`, `resources/views/layouts/navigation.blade.php` (superseded by the new shell)

### Demo credentials (password: `password` for all)

- Super Admin: `superadmin@example.com`
- School Admin: `admin@demoschool.test`
- Teacher: `teacher@demoschool.test`
- Student: `student@demoschool.test`
- Parent: `parent@demoschool.test`

## Phase 2 — Core Data Modules (IN PROGRESS)

| Item | Status |
|---|---|
| Class management (create/edit/delete) | ✅ Done |
| Section management (create/edit/delete, class-teacher assignment) | ✅ Done |
| Subject management (create/edit/delete, class + teacher assignment, elective flag) | ✅ Done |
| Teacher management (CRUD, creates User + Teacher + assigns role) | ✅ Done |
| Student management (CRUD, creates User + Student + assigns role, class/section placement) | ✅ Done |
| Search on Teachers/Students/Subjects, class+section filter on Students | ✅ Done |
| Role-scoped routes (`role:school_admin` middleware) + sidebar/bottom-nav wired to real routes | ✅ Done |
| Bulk import/promotion of students | ❌ Not started |
| Advanced search (multi-field/filter combos beyond name/email/admission no.) | ❌ Not started |
| Timetable builder (manual or automated) | ❌ Not started |
| Student/Teacher profile detail pages (currently list + modal edit only, no dedicated profile view) | ❌ Not started |
| Email notification of generated password to new Teacher/Student accounts | ❌ Not started — password is shown once in a dismissible banner after creation instead (no mail integration yet) |

### Notes / design choices made this session

- Creating a Teacher or Student creates a `User` (role-assigned) + the
  profile row in one action, with a random 12-char password shown once in a
  dismissible success banner (no email/SMS integration yet, tracked in Phase 4).
- Deleting a Teacher/Student deletes both the profile row and its `User`
  account (full removal), since there's no "deactivate" flow yet.
- All queries are manually scoped by `auth()->user()->school_id` inside each
  Livewire component (no global Eloquent scope yet) — fine while only
  School Admin uses these screens; revisit if Super Admin needs cross-school
  views of the same data later.
- CRUD modals reuse two new Blade components: `x-crud-modal` (backdrop + close
  button, Livewire-boolean driven) and `x-floating-input`/`x-floating-select`
  (floating label form fields per the UI spec).

### Files added/changed this session (Phase 2)

- `app/Livewire/SchoolAdmin/{Classes,Subjects,Teachers,Students}/Manage.php`
- `resources/views/livewire/school-admin/{classes,subjects,teachers,students}/manage.blade.php`
- `resources/views/components/{crud-modal,floating-input,floating-select}.blade.php`
- `routes/web.php` (new `school-admin.*` route group, `role:school_admin` middleware)
- `app/Support/Navigation.php` (Students/Teachers/Classes/Subjects now link to real routes)
- `README.md` (setup/run instructions), this file

## Phase 3 — Operational Modules (NOT started)

- Attendance (mobile bulk marking, reports, absence notifications)
- Fees & Finance (fee structures, invoices, payment gateway integration)
- Examinations & Grades (exam setup, grade entry, report cards, analytics)
- Reporting (student/academic/attendance/financial/admin reports, exports)

## Phase 4 — Enhancement (NOT started)

- Public CMS front page (hero/about/announcements/admissions/blog/gallery/contact)
- Admin CMS (WYSIWYG, media library, SEO, scheduled publishing)
- Communication (email/SMS/in-app/push, real-time chat, notices)
- PWA polish (manifest, service worker, offline shell) — package installed,
  not yet configured
- Analytics integrations (Google Analytics/Mixpanel)

## Phase 5 — Polish & Deployment (NOT started)

- i18n (multi-language, currency, timezone, academic calendar)
- Security hardening (2FA, audit trails, rate limiting review)
- Performance (indexing review, caching config, asset minification)
- Automated tests (Unit/Feature/Browser)
- Deployment guide for shared hosting (Hostinger), backups, cron, SSL
- User manuals + technical docs

## Next steps (pick up here next session)

1. Run `composer install && npm install`, copy `.env`, generate key.
2. Set `DB_CONNECTION` (sqlite is fine for local dev) and run
   `php artisan migrate --seed`.
3. `npm run build` (or `npm run dev`), then log in with the seeded demo users
   (see `database/seeders/DemoDataSeeder.php` for credentials) and click
   through all 5 dashboards to confirm role redirects and styling render
   correctly.
4. Phase 2 core CRUD (Classes/Sections/Subjects/Teachers/Students) is done —
   test it in the browser as School Admin (`admin@demoschool.test`).
5. Start Phase 3: Attendance is the natural next module since it depends
   directly on Students/Sections/Teachers, which now exist. Fees and
   Examinations can follow in any order after that.
