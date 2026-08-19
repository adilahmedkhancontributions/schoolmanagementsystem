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

## Phase 3 — Operational Modules (IN PROGRESS)

| Item | Status |
|---|---|
| Attendance schema (`attendances` table, one row per student/day) | ✅ Done |
| Attendance marking UI (mobile-first chip buttons, "mark all" shortcuts) shared by Teacher (own class-teacher sections only) and School Admin (all sections in school) | ✅ Done |
| Student/Parent "My Attendance" view (monthly summary + history, parent has a child switcher) | ✅ Done |
| Routes + nav wired for school-admin/teacher/student/parent | ✅ Done |
| Absence notifications (SMS/email/push) | ❌ Not started — deferred to Phase 4 (Communication) |
| Attendance analytics/exports (CSV/PDF reports) | ❌ Not started |
| Fees & Finance (fee structures, invoices, payment gateway integration) | ❌ Not started |
| Examinations & Grades (exam setup, grade entry, report cards, analytics) | ❌ Not started |
| Reporting (student/academic/attendance/financial/admin reports, exports) | ❌ Not started |

### Notes / design choices (Attendance)

- Attendance is **daily, per section**, not per subject/period — there's no
  timetable module yet, so a single status per student per day is recorded
  (`present`/`absent`/`late`/`half_day`/`leave`), unique on `(student_id, date)`.
- "Class teacher" access: a Teacher can only mark attendance for sections
  where `sections.teacher_id` is their own `teachers.id` row (homeroom
  model already implied by the existing `Section::classTeacher()` relation).
  School Admin can mark/view any section in their school. Both roles reuse
  the same `App\Livewire\Attendance\Mark` component; it detects the role via
  `auth()->user()->teacher` being present or not.
- `App\Livewire\Attendance\MyAttendance` is reused for both Student (own
  record only) and Parent (per-child switcher, guardian_student pivot).
  `studentId` is re-validated against the caller's own/children IDs on every
  render to prevent a tampered Livewire property from exposing another
  student's attendance.
- Added `mark attendance` permission to the `school_admin` role in
  `RolePermissionSeeder` (previously only `teacher`/`super_admin` had it).
- Section tenant scoping still has to go through `school_classes.school_id`
  since `sections` has no direct `school_id` column (pre-existing schema
  decision, unchanged).

### Files added/changed this session (Phase 3 — Attendance)

- `database/migrations/2024_01_04_000000_create_attendances_table.php`
- `app/Models/Attendance.php`; added `attendances()` relation to `app/Models/Student.php` and `app/Models/Section.php`
- `app/Livewire/Attendance/{Mark,MyAttendance}.php`
- `resources/views/livewire/attendance/{mark,my-attendance}.blade.php`
- `routes/web.php` (school-admin/teacher/student/parent attendance routes; new `teacher.*`, `student.*`, `parent.*` route groups)
- `app/Support/Navigation.php` (Attendance nav items now point to real routes for school_admin/teacher/student/parent)
- `database/seeders/RolePermissionSeeder.php` (`mark attendance` added to `school_admin`)

## UI/UX Refresh & School Theming (this session)

| Item | Status |
|---|---|
| Per-school theme colors (`primary_color`/`secondary_color`) + logo, applied app-wide via CSS variables | ✅ Done |
| Super Admin → Schools CRUD (branding: logo upload, color pickers, contact info, status) | ✅ Done |
| School Admin → School Profile settings (logo + colors + contact info; no slug/code/status — those stay Super-Admin-only) | ✅ Done |
| Redesigned public landing page (`welcome.blade.php`) | ✅ Done |
| Redesigned login/auth layout (gradient background, glass card, restyled shared Breeze input/button components) | ✅ Done |
| Refreshed dashboard shell (brand-gradient sidebar/topbar accents, school logo, gradient welcome banner, per-metric icons, role-relevant "Quick links" section) | ✅ Done |
| Attendance UI overhaul (gradient header, icon-coded status chips, live tally cards, circular attendance-rate ring on My Attendance) | ✅ Done |

### Notes / design choices (Theming)

- Theme is implemented as **CSS custom properties** (`--brand-primary`,
  `--brand-secondary`), set inline on `<html style="...">` in
  `layouts/dashboard.blade.php` from `auth()->user()->school`, with defaults
  in `resources/css/app.css`. This means a school's colors take effect
  immediately after saving, **without an `npm run build`/Tailwind rebuild**
  — important since Tailwind utility classes are static at build time and
  this app targets shared hosting.
- `.btn-primary`, `.sidebar-link.active`, `.bottom-nav-link.active`, and the
  new `.brand-gradient`/`.brand-text` utility classes in `app.css` consume
  these variables; hover state uses `filter: brightness(0.92)` instead of a
  second "dark" color to keep the color model to just two inputs.
- The public welcome page and login screen intentionally use the **default**
  indigo/sky brand (no school context pre-login); only the authenticated
  dashboard shell is school-themed.
- Logo uploads use Livewire's `WithFileUploads`, stored on the `public` disk
  under `school-logos/` — requires `php artisan storage:link` (not yet run
  in this sandbox).
- Super Admin can edit `name`/`slug`/`code`/`status`/contact/branding for any
  school; School Admin's own "School Profile" screen intentionally excludes
  `slug`, `code`, and `status` (tenant identity/lifecycle stays Super-Admin
  controlled), matching the request that theme/logo settings live in Super
  Admin and School Admin only — not Teacher/Student/Parent.

### Files added/changed this session (UI/UX Refresh & Theming)

- `database/migrations/2024_01_05_000000_add_theme_fields_to_schools_table.php`
- `app/Models/School.php` (`primary_color`, `secondary_color` fillable + `logoUrl()` accessor)
- `app/Livewire/SuperAdmin/Schools/Manage.php` + `resources/views/livewire/super-admin/schools/manage.blade.php`
- `app/Livewire/SchoolAdmin/Settings/Profile.php` + `resources/views/livewire/school-admin/settings/profile.blade.php`
- `routes/web.php` (`super-admin.schools`, `school-admin.settings`)
- `app/Support/Navigation.php` (Schools nav wired for super_admin; new "School Profile" nav item for school_admin only)
- `resources/css/app.css` (CSS variable theme system, `.brand-gradient`/`.brand-text`/`.status-chip`/`.glass-card` utilities)
- `resources/views/layouts/dashboard.blade.php` (brand vars injected on `<html>`, school logo in sidebar, brand-gradient avatar/active states)
- `resources/views/layouts/guest.blade.php`, `resources/views/auth/login.blade.php` (redesigned auth screens)
- `resources/views/components/{text-input,input-label,primary-button}.blade.php` (restyled shared Breeze components used across all auth screens)
- `resources/views/welcome.blade.php` (replaced stock Laravel page with an SMS landing page)
- `app/Livewire/Dashboard.php` + `resources/views/livewire/dashboard.blade.php` (gradient banner, per-metric icons, role-relevant quick links)
- `resources/views/livewire/attendance/{mark,my-attendance}.blade.php` (icon-coded status chips, live tally, circular attendance-rate ring)

### Bugfix pass (this session)

Re-audited every screen built so far after a report that attendance marking
and the school logo/branding screens "weren't working". Since this sandbox
still cannot run `php`/`composer` (see environment note at top), this was a
static code audit, not a browser test — findings:

- **Real code bug, fixed**: `resources/views/livewire/attendance/mark.blade.php`
  set each student's status via `wire:click="$set('status.{{ $id }}', '{{ $value }}')"`
  — Livewire's `$set(...)` dot-path magic action for array properties relies
  on the framework's property-change interception, which **Livewire 4**
  (`composer.json` pins `^4.4`) reworked around PHP property hooks. This is
  the one place in the app using that pattern (every other CRUD screen calls
  plain component methods like `openEdit($id)`), so it's the most likely
  cause of "marking attendance doesn't work." Fixed by adding an explicit
  `Mark::setStatus(int $studentId, string $status)` method and calling
  `wire:click="setStatus({{ $student->id }}, '{{ $value }}')"` instead —
  version-agnostic and consistent with the rest of the codebase.
- **No code bug found** in the School Admin "School Profile" logo upload or
  the Super Admin "Schools" logo upload (`WithFileUploads`, validation, and
  `$model->update()` all check out against the working Students/Teachers CRUD
  pattern). The most likely cause of "logo update not working" is that
  **`php artisan storage:link` has never been run** in whatever environment
  this was tested in — without it, `Storage::disk('public')->url(...)` returns
  a `/storage/...` URL that 404s even though the upload itself succeeded and
  the DB `logo` column was updated. Also confirm `php artisan migrate` has
  actually been run so the `attendances` table and the new `schools.primary_color`
  /`secondary_color` columns exist — a missing table/column would surface as
  "feature doesn't work" too. Both are listed in "Next steps" below.

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
5. Attendance (marking + student/parent views) is now built — after
   migrating, assign a Teacher as `classTeacher` on a Section (via the
   Sections tab under Classes) so `teacher@demoschool.test` has a section to
   mark; then test `/school-admin/attendance`, `/teacher/attendance`,
   `/student/attendance`, `/parent/attendance` in the browser.
6. Run `php artisan storage:link` (needed for uploaded school logos to be
   web-accessible), then test theming: log in as `superadmin@example.com` →
   Schools → edit the demo school's logo/colors; log in as
   `admin@demoschool.test` → School Profile → change colors and confirm the
   sidebar/buttons/dashboard re-theme after reload.
7. Next: Fees & Finance or Examinations & Grades (either order). Attendance
   analytics/exports and absence notifications were deferred — revisit once
   Communication (Phase 4) exists for the notification channel.
