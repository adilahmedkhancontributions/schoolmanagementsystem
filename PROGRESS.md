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

## SRS v1.0 Realignment (this session)

The project manager supplied `/SoftwareRequirementsSpecification(SRS).txt` — a
full 89-section product spec for a multi-school SaaS platform ("Product
Master Specification", targeting private schools in Attock/Punjab,
Pakistan). It is **much broader** than everything built so far: it adds
Admissions, Staff/HR/Payroll, Leave Management, Fee Discounts/Concessions/
Defaulters, Homework/LMS/Quizzes, Library, Transport, Inventory, Visitor/
Complaint/Discipline/Health records, Certificates, ID cards, Audit Logs,
Data Import/Export, WhatsApp/SMS, subscriptions/billing, and AI features —
on top of everything already in Phases 1–4 below. **This document is now
tracked against the SRS's own section numbers** so it's always clear which
of the 89 sections are covered, partial, or not started.

The SRS's own §77 "MVP Definition" lists 36 mandatory modules and explicitly
excludes Payroll, full HR, Library, Transport, Inventory, WhatsApp/SMS,
online payment gateways, AI, mobile apps, offline sync, and biometrics/GPS
from MVP (deferred to its Phase 2/Phase 3, §78–79). That phasing is adopted
here instead of inventing a separate one.

### Coverage vs. SRS sections

| # | SRS Section | Status |
|---|---|---|
| 1–5 | Purpose/Vision/Objectives/Users/Stakeholders | 📄 Docs only (product framing, no direct code) |
| 6 | User Role and Permission System | ✅ Done (Spatie roles: super_admin/school_admin/teacher/student/parent) — ⚠️ SRS wants finer-grained configurable permissions (view/create/edit/delete/approve/export/print/manage/configure) per role beyond Spatie's default permission list; not built |
| 7 | Multi-School Architecture | ✅ Done (single DB, `school_id` scoping) |
| 8 | Multi-Campus Support | ❌ Not started — no `campuses` table; every school is currently single-campus |
| 9 | School Configuration | 🟡 Partial — name/logo/colors/contact done; academic calendar, configurable grading system, configurable fee/attendance rules not done |
| 10 | School Dashboard | 🟡 Partial — student/attendance/finance/academic metric cards exist; "Alerts" (low attendance, missing marks, pending approvals) not built |
| 11 | Admissions Management | ❌ Not started — students are created directly by School Admin, no inquiry→interview→test→enrol pipeline or document uploads |
| 12 | Student Management | 🟡 Partial — core profile + CRUD done; no father/mother name split, B-form/CNIC, medical info, or status lifecycle (Applicant/Active/Transferred/Withdrawn/Graduated/Suspended/Expelled) beyond implicit active |
| 13 | Parent and Guardian Management | ✅ Done (guardian↔student pivot, child switcher) |
| 14 | Academic Session Management | ❌ Not started — no `academic_sessions`/year-rollover/promotion workflow; classes/sections are not session-scoped |
| 15 | Classes, Sections and Subjects | ✅ Done |
| 16 | Teacher Management | ✅ Done |
| 17 | Staff Management (non-teaching) | ✅ Done |
| 18 | Timetable Management | 🟡 Partial — class/teacher timetable + change requests + conflict-safe approval done; no room timetable/room-conflict detection |
| 19 | Student Attendance | ✅ Done |
| 20 | Staff Attendance | ✅ Done |
| 21 | Leave Management | ✅ Done this session (student + staff/teacher leave requests, approval workflow) |
| 22 | Fee Management | ✅ Done (categories via fee structures, monthly/custom schedules) |
| 23 | Discounts and Concessions | ✅ Done this session (sibling/scholarship/staff-child/need-based/custom, % or fixed) |
| 24 | Fee Collection | 🟡 Partial — cash/bank/cheque/online/other manual recording + partial payments done; no challans, JazzCash/Easypaisa, refunds, or receipt numbering |
| 25 | Fee Defaulters | ✅ Done this session (class-wise outstanding list, CSV export) |
| 26 | Examination Management | ✅ Done |
| 27 | Marks Management | 🟡 Partial — single marks-obtained field per subject; no MCQ/short/long/practical/viva component breakdown |
| 28 | Result Management | 🟡 Partial — per-exam report card done; no transcripts/GPA/configurable grade scale |
| 29 | Result Analytics | ✅ Done (folded into Reports → Exams) |
| 30 | Report Card Builder | ❌ Not started — report card layout is fixed, not a per-school configurable template |
| 31 | Homework Management | ❌ Not started |
| 32 | Learning Management System | ❌ Not started |
| 33 | Quiz System | ❌ Not started |
| 34 | Parent Portal | ✅ Done |
| 35 | Student Portal | ✅ Done |
| 36 | Teacher Portal | ✅ Done |
| 37 | Communication Management | 🟡 Partial — announcements + teacher↔parent messaging done; email/SMS/WhatsApp delivery not done |
| 38 | WhatsApp Integration | ❌ Not started (SRS: Phase 2) |
| 40 | Events and Calendar | ❌ Not started |
| 41 | Library Management | ❌ Not started (SRS: Phase 2) |
| 43 | HR Management | ❌ Not started (SRS: Phase 2) |
| 44 | Payroll Management | ❌ Not started (SRS: Phase 2) |
| 45 | Inventory Management | ❌ Not started (SRS: Phase 2) |
| 46 | School Store | ❌ Not started (SRS: Phase 2) |
| 47 | Document Management System | ✅ Done (Students, Teachers, Staff) |
| 49 | Visitor Management | ❌ Not started (SRS: Phase 2) |
| 50 | Complaint Management | ❌ Not started (SRS: Phase 2) |
| 51 | Discipline Management | ❌ Not started (SRS: Phase 2) |
| 52 | Student Health Records | ❌ Not started (SRS: Phase 2) |
| 53 | Certificates | ❌ Not started (SRS: Phase 2) |
| 54 | ID Card Generation | ❌ Not started (SRS: Phase 2) |
| 55 | Reporting System | 🟡 Partial — attendance/exam/fee reports with CSV export done; no PDF export |
| 56 | Owner Analytics | ❌ Not started — no cross-school Super Admin analytics dashboard |
| 57 | AI Features | ❌ Not started (SRS: Phase 3) |
| 58 | Notifications | 🟡 Partial — in-app + email (timetable changes) done; SMS/WhatsApp/push not done |
| 59 | Search | ❌ Not started — no global cross-module search |
| 60 | Audit Logs | ✅ Done this session (generic audit trail on key models + School Admin viewer) |
| 61 | Security Requirements | 🟡 Partial — Laravel/Breeze auth, CSRF, validation, role middleware in place; no explicit rate-limiting review, account lockout, or field-level encryption yet |
| 62 | Data Isolation | ✅ Done (manual `school_id` scoping in every query — see architecture note) |
| 63 | Backup and Recovery | ❌ Not started — no backup tooling configured |
| 64 | Data Export | 🟡 Partial — CSV export exists for Reports only, not Students/Fees/Attendance/Results in general |
| 65 | Data Import | ❌ Not started — no bulk CSV/Excel import for students/staff/results |
| 66 | API Architecture | ❌ Not started — server-rendered Livewire only, no public API layer |
| 67 | Mobile Strategy | ✅ Responsive web done; native apps out of scope (SRS: Phase 3) |
| 68 | Offline Capability | ❌ Not started (SRS: explicitly deferred) |
| 69 | Localization | ❌ Not started — English only, no Urdu |
| 70 | Pakistani School Support | ❌ Not started — no B-form/board/Islamiat-specific fields, no PKR currency formatting, no JazzCash/Easypaisa |
| 71–74 | Non-functional (performance/scalability/availability/maintainability) | 🟡 Ongoing — Eloquent scoping + pagination in place; no caching/queue/index audit done yet |
| 75 | Customization Architecture | 🟡 Partial — theming/branding configurable; grading/fee/result templates are not |
| 76 | Subscription Architecture | ❌ Not started — no plans/billing/limits for the SaaS platform itself |
| 77 | MVP Definition | See per-item mapping above — most of the 36 MVP items exist except Admissions, Staff, Staff Attendance, Homework/basic LMS, Data Import/Export |
| 78–79 | Phase 2 / Phase 3 | Correctly still not started — out of scope until MVP gaps above are closed |
| 80–89 | Architecture/strategy/process guidance | 📄 Docs only — informs future work, no direct deliverable |

### Notes / design choices (this session's additions)

- **Audit Logs**: one generic `audit_logs` table (`user_id`, `school_id`,
  `auditable_type`/`auditable_id`, `action`, `old_values`/`new_values` JSON,
  `ip_address`) populated by an `App\Support\Auditable` trait's
  `created`/`updated`/`deleted` model event hooks — added to `Student`,
  `Teacher`, `FeeInvoice`, `FeePayment`, `ExamResult`, and `Attendance` (the
  models the SRS's own example — "Accountant changed a fee balance" — and
  general MVP intent most directly call out) rather than every model in the
  app, to keep the log signal-focused. School Admin gets a read-only,
  paginated, filterable (`by model type`, `by user`) log viewer.
- **Fee Discounts/Concessions**: a `fee_discounts` table (type: sibling/
  scholarship/staff_child/need_based/custom, `is_percentage` + `value`,
  optional `notes` for justification) linked to a `fee_invoice_id`;
  `FeeInvoice::refreshStatus()` now nets discounts off the billed amount
  before computing `paid`/`partial`/`unpaid`. Applying a discount requires
  School Admin (same `role:school_admin` gate as everything else in Fees —
  the SRS's "appropriate authorization" requirement is met by there being no
  Teacher/Student/Parent write access to this screen, consistent with every
  other Fees screen).
- **Fee Defaulters**: a fourth tab alongside the existing Reports (Attendance/
  Exams/Fees) — lists every student with `status != 'paid'`, days overdue
  (`now() - due_date`), and outstanding balance (post-discount), grouped by
  class, with the same CSV export pattern as the other three reports.
- **Leave Management**: one `leave_requests` table (`user_id` = requester,
  `student_id` nullable for a parent-submitted student leave vs. null for a
  staff/teacher's own leave, `from_date`/`to_date`/`reason`/`status`
  pending/approved/rejected, `reviewed_by`/`reviewed_at`/`admin_note` —
  same shape as `TimetableChangeRequest`, reused deliberately since it's
  already proven to work for a "request → review → approve/reject" flow).
  Parent submits on behalf of a child (re-validated against their own
  children, same guard pattern as `MyAttendance`); Teacher submits for
  themselves. School Admin approves/rejects both from one screen with a
  type filter (Student/Staff).

### Files added/changed this session (SRS Realignment)

(See per-module "Files added/changed" lists further down, filed under their
matching Phase headings, added at the point in this doc where each module's
peers already live — Audit Logs and Leave Management are new so they get
their own headings before "Phase 5 — Polish & Deployment"; Discounts/
Defaulters extend the existing Phase 3 Fees/Reports sections in place.)

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
| Fees & Finance: fee structures CRUD (School Admin) | ✅ Done |
| Fees & Finance: invoice generation (single student / whole class / whole school, from a structure template or custom) | ✅ Done |
| Fees & Finance: manual payment recording (cash/bank/cheque/online/other) with running balance + payment history, partial payments supported | ✅ Done |
| Fees & Finance: Student/Parent "My Fees" view (invoices, totals billed/paid/due, parent child switcher) | ✅ Done |
| Fees & Finance: payment gateway integration (Stripe/Razorpay/PayPal online pay) | ❌ Not started — deferred until real credentials are available (per architecture note at top of this doc); payments are recorded manually by School Admin for now |
| Examinations & Grades: exam setup (School Admin creates exams per class, picks subjects + max/pass marks) | ✅ Done |
| Examinations & Grades: grade entry (Teacher — own subjects only; School Admin — any subject) | ✅ Done |
| Examinations & Grades: Student/Parent report card (per-exam subject breakdown, total %, letter grade, pass/fail) | ✅ Done |
| Examinations & Grades: analytics (class averages, top scorers, per-subject breakdown, CSV export) | ✅ Done (folded into Reports, see below) |
| Reporting: Attendance report (per-student present/absent/late/half-day/leave counts + rate, date range + class filter, CSV export) | ✅ Done |
| Reporting: Exam report (student rankings, class average, pass rate, top scorer, per-subject average/high/low, CSV export) | ✅ Done |
| Reporting: Fee collection report (billed/collected/outstanding/collection rate, per-class breakdown, overdue invoices list + CSV export) | ✅ Done |
| Reporting: trend charts (attendance/fees over time) | ❌ Not started — no JS charting library installed yet; current reports use stat cards + tables only |
| Reporting: Super Admin cross-school / global analytics | ❌ Not started |

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

### Notes / design choices (Fees & Finance)

- Three tables: `fee_structures` (reusable templates — name, amount, optional
  class scope, frequency label for display only, no auto-recurring billing
  job yet), `fee_invoices` (one row billed to one student, `amount` +
  `paid_amount` + derived `status`), `fee_payments` (append-only ledger of
  each payment against an invoice, so partial payments and payment history
  are both supported without any destructive updates to past payments).
- `FeeInvoice::refreshStatus()` recomputes `unpaid`/`partial`/`paid` from
  `paid_amount` vs `amount` using `bcadd`/`bccomp` (avoids float rounding on
  money); called after every payment is recorded.
- "Generate Invoices" is a single form that can target one student, a whole
  class, or (leaving both blank) every student in the school — it can start
  from a fee structure template (prefills title/amount/class) or be fully
  custom. There's no recurring/auto-generation job yet — admin re-runs it
  each billing cycle, consistent with "no cron/queue infra assumed yet".
- No payment gateway wired (Stripe/Razorpay/PayPal) — matches the top-level
  architecture decision to defer third-party integrations until real
  credentials exist. School Admin records payments manually (cash/bank
  transfer/cheque/online/other) after collecting them out-of-band.
- Student/Parent "My Fees" reuses the same studentId-guarded pattern as
  `MyAttendance` (own record only for Student, per-child switcher for
  Parent) — no payment action for Student/Parent yet, view-only.
- Added `Fees Due` (outstanding balance) to the School Admin and Student
  dashboard metric cards.

### Files added/changed this session (Phase 3 — Fees & Finance)

- `database/migrations/2024_01_06_00000{0,1,2}_create_fee_{structures,invoices,payments}_table.php`
- `app/Models/{FeeStructure,FeeInvoice,FeePayment}.php`; added `feeInvoices()` to `app/Models/Student.php`, `feeStructures()` to `app/Models/SchoolClass.php`
- `app/Livewire/SchoolAdmin/Fees/{Structures,Invoices}.php` + matching `resources/views/livewire/school-admin/fees/{structures,invoices}.blade.php`
- `app/Livewire/Fees/MyFees.php` + `resources/views/livewire/fees/my-fees.blade.php` (shared by Student and Parent)
- `routes/web.php` (`school-admin.fees.structures`, `school-admin.fees.invoices`, `student.fees`, `parent.fees`)
- `app/Support/Navigation.php` (Fees nav wired for school_admin/student/parent)
- `app/Livewire/Dashboard.php`, `resources/views/livewire/dashboard.blade.php` (`Fees Due` metric)
- `database/seeders/DemoDataSeeder.php` (demo `FeeStructure` + one unpaid `FeeInvoice` for the demo student)

### Bugfix / audit pass (this session)

Re-read every previously built Livewire component (Classes/Sections,
Subjects, Teachers, Students, Attendance Mark/MyAttendance, Super Admin
Schools, School Admin Settings/Profile) end-to-end while building Fees to
check for issues similar to the earlier `$set(...)` attendance bug. No new
bugs found — all CRUD components consistently scope queries by
`school_id`, use plain component methods for `wire:click` (no Livewire
magic actions), and validate uniqueness with `Rule::unique(...)->ignore(...)`.
No changes were needed to these files this session.

### Notes / design choices (Examinations & Grades)

- Three tables: `exams` (name/term/dates, scoped to one `school_class_id` —
  an exam is always for a specific class, unlike fee structures which can be
  class-agnostic), `exam_subjects` (join of exam + subject with a per-subject
  `max_marks`/`pass_marks`, since different subjects in the same exam can
  have different totals), `exam_results` (one row per student per
  exam-subject — `marks_obtained` nullable so "not yet graded" is
  distinguishable from "scored zero", unique on `(exam_subject_id,
  student_id)`).
- No separate configurable grading-scale table — percentage-to-letter-grade
  (A+/A/B+/B/C/D/F) is a fixed threshold table on `ExamResult::grade()`,
  matching the "don't build for hypothetical requirements" guidance; revisit
  if a school needs custom grade bands.
- Exam setup is School-Admin-only (`school-admin.exams`): create the exam
  against a class, then a second "Subjects" modal lists every subject that
  belongs to that class so the admin can toggle which ones are examined and
  set marks — unticking a subject deletes its `exam_subjects` row (cascades
  to any entered results for that subject).
- Grade entry (`App\Livewire\Exams\GradeEntry`) is shared by Teacher and
  School Admin, same pattern as `Attendance\Mark`: a Teacher only sees exams/
  subjects where `subjects.teacher_id` is their own `teachers.id`; School
  Admin sees every exam/subject in the school. Picking an exam+subject loads
  every student in that exam's class with a marks + remarks input per row,
  saved in one submit via `updateOrCreate` per student (matches the
  Attendance `Mark::save()` pattern, not the Fees `updateOrCreate`-with-
  possible-cast-mismatch pattern — see the Attendance date-cast bugfix
  earlier in this doc, which is why `ExamResult`/`ExamSubject` don't use
  Eloquent date/decimal round-tripping tricks for their lookup keys).
- Student/Parent "Grades" (`App\Livewire\Exams\ReportCard`) reuses the same
  studentId-guarded pattern as `MyAttendance`/`MyFees` (own record only for
  Student, per-child switcher for Parent): pick an exam (scoped to the
  student's current class), see a subject-by-subject table plus totals
  (marks obtained/max, percentage, overall letter grade) and a pass/fail
  chip per subject computed from that subject's own `pass_marks`.
- No analytics yet (class average, top scorers, grade distribution charts)
  and no printable/exportable report card PDF — both deferred, tracked in
  the Phase 3 table above.

### Files added/changed this session (Phase 3 — Examinations & Grades)

- `database/migrations/2024_01_07_00000{0,1,2}_create_exam{s,_subjects,_results}_table.php`
- `app/Models/{Exam,ExamSubject,ExamResult}.php`; added `exams()` to `app/Models/SchoolClass.php`, `examSubjects()` to `app/Models/Subject.php`, `examResults()` to `app/Models/Student.php`
- `app/Livewire/SchoolAdmin/Exams/Manage.php` + `resources/views/livewire/school-admin/exams/manage.blade.php` (exam CRUD + per-exam subject/marks setup modal)
- `app/Livewire/Exams/GradeEntry.php` + `resources/views/livewire/exams/grade-entry.blade.php` (shared Teacher/School Admin marks entry)
- `app/Livewire/Exams/ReportCard.php` + `resources/views/livewire/exams/report-card.blade.php` (shared Student/Parent report card)
- `routes/web.php` (`school-admin.exams`, `school-admin.exams.grades`, `teacher.exams.grades`, `student.exams`, `parent.exams`)
- `app/Support/Navigation.php` (Exams/Grades/Performance nav items now point to real routes for school_admin/teacher/student/parent)
- `database/seeders/DemoDataSeeder.php` (demo `Exam` "Mid Term Exam" + one `ExamSubject` for Mathematics on the demo class)

### Notes / design choices (Reports)

- Three School-Admin-only report screens sharing one tab bar
  (`livewire/school-admin/reports/_tabs.blade.php`) rather than one combined
  component — keeps each component's `render()` query set focused, mirrors
  how Fees already splits Structures/Invoices into separate components with
  a cross-link button.
- No charting library is installed (`package.json` has no chart dependency
  and the architecture note at the top of this doc prioritizes shared-hosting/
  minimal-resource-usage), so all three reports use stat cards + plain
  tables instead of graphs. Revisit if/when a lightweight charting approach
  is chosen.
- CSV export is implemented as a plain Livewire component method returning
  `response()->streamDownload(...)` (`wire:click="export"` triggers a normal
  browser download) — no extra controller/route or export package needed.
- Attendance report groups `Attendance` rows by `student_id` over a date
  range (default: month-to-date) with an optional class filter, computing
  present/absent/late/half_day/leave counts and a simple attendance rate
  (`present / total * 100`).
- Exam report picks one exam (defaults to the most recent) and computes,
  per student, summed marks across all graded `exam_subjects` for that exam
  plus overall pass/fail (a student "passes" only if every graded subject
  individually clears its own `pass_marks`); the subject-breakdown table
  reuses the same `ExamResult` collection grouped by subject instead of a
  second query.
- Fees report aggregates all of a school's `fee_invoices` by
  `student->schoolClass->name` for the billed/paid/due-by-class table, and
  separately lists invoices where `status != 'paid'` and `due_date` is in
  the past as "Overdue" (its own CSV export, since that's the actionable
  list an admin would chase up).

### Files added/changed this session (Phase 3 — Reports)

- `app/Livewire/SchoolAdmin/Reports/{Attendance,Exams,Fees}.php` + matching `resources/views/livewire/school-admin/reports/{attendance,exams,fees}.blade.php`
- `resources/views/livewire/school-admin/reports/_tabs.blade.php` (shared tab bar)
- `routes/web.php` (`school-admin.reports.attendance`, `school-admin.reports.exams`, `school-admin.reports.fees`)
- `app/Support/Navigation.php` (new "Reports" nav item for school_admin)

## Phase 4 — Enhancement (NOT started)

| Item | Status |
|---|---|
| Communication: Announcements (School Admin CRUD — title/body/audience/optional class scope/immediate or scheduled publish; read-only feed for Teacher/Student/Parent, audience + class filtered) | ✅ Done |
| Communication: email/SMS/push delivery of announcements | ❌ Not started — in-app feed only for now, no mail/SMS provider configured |
| Communication: real-time chat / direct messaging (teacher↔parent) | ❌ Not started |
| Public CMS front page (hero/about/announcements/admissions/blog/gallery/contact) | ❌ Not started |
| Admin CMS (WYSIWYG, media library, SEO, scheduled publishing) | ❌ Not started |
| PWA polish (manifest, service worker, offline shell) | ❌ Not started — package installed, not yet configured |
| Analytics integrations (Google Analytics/Mixpanel) | ❌ Not started |

### Notes / design choices (Announcements)

- One `announcements` table: `audience` enum (`everyone`/`teachers`/`students`/`parents`)
  plus an optional `school_class_id` to narrow further (e.g. "students" +
  Grade 5 = only Grade 5 students/parents-of-Grade-5 see it, since the
  visibility scope also matches `parents` against a class). `published_at`
  is nullable and can be future-dated for scheduling, or left null for a
  draft that never appears in any feed.
- `Announcement::scopeVisibleTo($role, $classIds)` is the single source of
  truth for "can this role see this announcement" — used by the shared
  `App\Livewire\Announcements\Feed` component (Teacher/Student/Parent) so
  the filtering logic isn't duplicated per role. Teachers are matched against
  the classes of the sections they're `classTeacher` on; Students against
  their own `school_class_id`; Parents against the union of all their
  children's `school_class_id`s.
- School Admin's management screen (`school-admin.announcements`) is the
  only write surface — Teacher/Student/Parent get a read-only feed, matching
  the request that CMS/communication authoring stays admin-side for now.
- No email/SMS/push delivery yet (matches the top-level architecture
  decision to defer third-party integrations) — an announcement only
  appears in the in-app feed once its `published_at` has passed.

### Files added/changed this session (Phase 4 — Announcements)

- `database/migrations/2024_01_08_000000_create_announcements_table.php`
- `app/Models/Announcement.php`; added `announcements()` to `app/Models/SchoolClass.php`
- `app/Livewire/SchoolAdmin/Announcements/Manage.php` + `resources/views/livewire/school-admin/announcements/manage.blade.php`
- `app/Livewire/Announcements/Feed.php` + `resources/views/livewire/announcements/feed.blade.php` (shared Teacher/Student/Parent)
- `routes/web.php` (`school-admin.announcements`, `teacher.announcements`, `student.announcements`, `parent.announcements`)
- `app/Support/Navigation.php` (Announcements nav items now point to real routes for school_admin/teacher/student/parent)
- `database/seeders/DemoDataSeeder.php` (demo "Welcome to the new term" announcement, audience `everyone`)

## Mobile Responsiveness & PWA Pass (this session)

| Item | Status |
|---|---|
| Fixed mobile "Save" bar hidden behind bottom nav on Attendance Mark / Grade Entry | ✅ Done |
| Mobile card layouts for admin data tables (Students, Teachers, Subjects, Fee Structures, Fee Invoices, Exams) | ✅ Done |
| Mobile card layout for Student/Parent "My Fees" and "Grades" (report card) | ✅ Done |
| Stacked (not side-by-side) form fields on narrow modals (Exam dates, Announcement audience/class) | ✅ Done |
| Prevented iOS Safari auto-zoom on input focus (forced 16px font-size on form fields under 640px) | ✅ Done |
| PWA manifest (`config/pwa.php`, `public/manifest.json`) re-branded from placeholder "Laravel PWA" to real app name/colors, `display` changed `fullscreen` → `standalone` | ✅ Done |
| `@PwaHead` directive + apple-mobile-web-app meta tags wired into `layouts/dashboard.blade.php`, `layouts/guest.blade.php`, `welcome.blade.php` | ✅ Done |

### Notes / design choices (Mobile & PWA)

- **Real bug found and fixed**: `attendance/mark.blade.php` and `exams/grade-entry.blade.php` each had a
  mobile-only "Save" button bar pinned `fixed bottom-0`, but the dashboard shell's bottom navigation
  (`layouts/dashboard.blade.php`) is *also* `fixed bottom-0` and renders later in the DOM, so it painted on
  top and made the Save button unreachable on every phone. Fixed by moving the save bar to `bottom-16`
  (clears the nav) and bumping the form's bottom padding from `pb-24` to `pb-32` so the last row of content
  isn't covered by the two stacked bars.
- Every admin CRUD table (Students, Teachers, Subjects, Fee Structures, Fee Invoices, Exams,
  Announcements) now renders a `sm:hidden` stacked-card list on phones and keeps the original `hidden
  sm:block` table unchanged above the `sm` breakpoint — no data or actions were removed, just re-laid-out.
  Screens that were already card-based (Classes/Sections, Super Admin Schools, Announcements feed) or
  already used `overflow-x-auto` (Reports) were left as-is.
- Student/Parent "My Fees" and "Grades" (report card) got the same card/table split since these are the
  screens ordinary students and parents hit most on a phone.
- Global fix in `resources/css/app.css`: all `input`/`select`/`textarea` get `font-size: 16px` under a
  `max-width: 640px` media query — anything smaller than 16px makes iOS Safari auto-zoom the whole page on
  focus, which was happening on every form field (`text-sm` = 14px) across the app.
- PWA: `config/pwa.php` and `public/manifest.json` were still the package's placeholder values ("Laravel
  PWA" / "LPT" / purple `#6777ef` / `display: fullscreen`). Re-branded both to the app name/description and
  the default brand indigo (`#4f46e5`), and switched `display` to `standalone` (fullscreen hides the status
  bar entirely, which is unexpected for a business app). Added the package's `@PwaHead` Blade directive plus
  `apple-mobile-web-app-capable`/`apple-touch-icon` meta tags to all three top-level layouts (dashboard,
  guest/auth, public welcome page) so "Add to Home Screen" works with correct icon/name on iOS and Android.
  `public/sw.js`/`public/offline.html` (install-time cache + offline fallback page) were already functional
  and unchanged. Since `composer install`/`php artisan erag:update-manifest` can't run in this sandbox,
  `public/manifest.json` was hand-edited to match the new `config/pwa.php` values directly.
- Did not touch: `public/offline.html` (generic red Laravel-branded offline page, cosmetic only, low
  priority), Reports screens (already used `overflow-x-auto` and fit acceptably on mobile since they're
  admin-only analytics, not something "every user" hits on a phone).

### Files added/changed this session (Mobile & PWA)

- `resources/views/livewire/attendance/mark.blade.php`, `resources/views/livewire/exams/grade-entry.blade.php` (fixed bottom-nav overlap bug)
- `resources/views/livewire/school-admin/{students,teachers,subjects}/manage.blade.php`, `resources/views/livewire/school-admin/fees/{structures,invoices}.blade.php`, `resources/views/livewire/school-admin/exams/manage.blade.php`, `resources/views/livewire/school-admin/announcements/manage.blade.php` (mobile card layouts)
- `resources/views/livewire/fees/my-fees.blade.php`, `resources/views/livewire/exams/report-card.blade.php` (mobile card layouts)
- `resources/css/app.css` (16px mobile input font-size fix)
- `config/pwa.php`, `public/manifest.json` (re-branded, `standalone` display)
- `resources/views/layouts/dashboard.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/welcome.blade.php` (`@PwaHead`, apple meta tags, `viewport-fit=cover`)

## Phase 4 — CMS Module (this session)

| Item | Status |
|---|---|
| Public front page & CMS: Hero, About/Admissions/custom pages, Announcements, Blog, Gallery, Contact form | ✅ Done |
| Admin CMS: Pages CRUD with lightweight WYSIWYG editor + SEO fields + draft/published | ✅ Done |
| Admin CMS: Blog Posts CRUD with featured image upload + scheduled publishing | ✅ Done |
| Admin CMS: Gallery CRUD (multi-upload, reorder, captions) | ✅ Done |
| Admin CMS: Contact message inbox (mark read / delete) | ✅ Done |
| Admin CMS: Homepage Hero (headline/subheadline/image) folded into School Profile settings | ✅ Done |
| Full drag-and-drop page builder / dedicated media library browser | ❌ Not started — see notes below |

### Notes / design choices (CMS)

- **Public site is per-school**, served at `/s/{school:slug}` (`PublicSiteController` + plain Blade, no
  auth) — home (hero, announcements, about/admissions page teasers, gallery preview, latest posts, contact
  form), `/pages/{slug}` for any published `CmsPage`, `/blog` + `/blog/{slug}`, `/gallery`. This is separate
  from the platform's own `welcome.blade.php` (which markets the SaaS product itself, not a specific
  school) — a school's public site is themed with that school's `primary_color`/`secondary_color` via the
  same CSS-variable mechanism the dashboard shell already uses.
- **No new WYSIWYG/editor npm package** — `x-rich-text-editor` (`resources/views/components/rich-text-editor.blade.php`)
  is a `contenteditable` div with an Alpine-driven toolbar calling `document.execCommand` (bold/italic/
  underline/headings/lists/link/clear-formatting), synced to the Livewire property on blur/input via
  `$wire.set(...)`. Matches the existing architecture decision to keep the app light for shared hosting
  and avoid new dependencies; sufficient for the "hero/about/admissions/blog" copy this CMS targets. A real
  block-based drag-and-drop builder (as the original spec's "WYSIWYG, drag-and-drop" wording implies) was
  judged out of scope for the value it adds here and is called out as the one deliberately deferred piece.
- **Stored-XSS mitigation**: CMS page/post `content` is raw HTML from the editor and later rendered
  unescaped (`{!! !!}`) on the public site to anonymous visitors, so a compromised/malicious School Admin
  account could otherwise plant a stored XSS payload. Added `App\Support\HtmlSanitizer::clean()` — a
  dependency-free allow-list tag strip (`strip_tags` with a fixed allow-list) plus regex removal of
  `on*="..."` event-handler attributes and `javascript:` URIs — applied in `Pages::save()` and
  `Posts::save()` before persisting. Not a full HTML sanitizer (no `mews/purifier`/`HTMLPurifier` dependency
  added, consistent with the shared-hosting/minimal-footprint decision) but closes the realistic attack
  surface for this editor.
- **No separate "media library"** — matches the existing precedent (school logo upload is inline on its own
  form, not picked from a shared library): blog post featured images and gallery images are uploaded
  per-item via `WithFileUploads`, stored on the `public` disk (`cms-posts/`, `cms-gallery/`, `cms-hero/`),
  same as `school-logos/`. Revisit if a school accumulates enough images that a searchable/reusable library
  becomes worth the complexity.
- Gallery reordering is simple up/down swap buttons (`sort_order` swap between adjacent rows), not
  drag-and-drop — no JS sortable library installed and this keeps the same "no new dependency" stance.
- Contact form has a hidden honeypot field (`website`, `prohibited` validation rule) plus `throttle:5,1` on
  the POST route as basic spam/abuse mitigation — no CAPTCHA service configured (would need third-party
  credentials, same as the deferred payment/SMS integrations).
- `CmsPage`/`CmsPost` slugs are unique **per school** (`unique(['school_id','slug'])`), not globally, so two
  schools can each have their own `/about` page; validated with `Rule::unique(...)->where('school_id', ...)`.
- Homepage hero fields (`hero_headline`, `hero_subheadline`, `hero_image`) live directly on the `schools`
  table (next to the existing theme color columns) and are edited from the existing School Admin → School
  Profile screen rather than a new page, since it's the same "branding" concern as logo/colors.
- CMS nav item in `app/Support/Navigation.php` (previously a disabled "Coming soon" placeholder) now links
  to `school-admin.cms.pages`; a shared tab bar (`livewire/school-admin/cms/_tabs.blade.php`, same pattern
  as Reports) switches between Pages/Blog/Gallery/Messages and links out to "Homepage Hero" (Settings) and
  "View Public Site" (opens the live `/s/{slug}` page in a new tab).

### Files added/changed this session (CMS)

- `database/migrations/2024_01_09_00000{0,1,2,3,4}_*.php` (hero fields on `schools`, `cms_pages`, `cms_posts`, `cms_gallery_images`, `contact_messages`)
- `app/Models/{CmsPage,CmsPost,GalleryImage,ContactMessage}.php`; `app/Models/School.php` (hero fields, `heroImageUrl()`, CMS relations)
- `app/Support/HtmlSanitizer.php`
- `app/Livewire/SchoolAdmin/Cms/{Pages,Posts,Gallery,Messages}.php` + matching `resources/views/livewire/school-admin/cms/{pages,posts,gallery,messages}.blade.php` + `_tabs.blade.php`
- `resources/views/components/rich-text-editor.blade.php`; `resources/css/app.css` (`.editor-btn`, `.cms-content` typography)
- `app/Http/Controllers/PublicSiteController.php`; `resources/views/components/public-site-layout.blade.php`; `resources/views/cms/public/{home,page,blog-index,blog-show,gallery}.blade.php`
- `app/Livewire/SchoolAdmin/Settings/Profile.php` + view (Homepage Hero fields)
- `routes/web.php` (`school-admin.cms.*`, public `public.site.*` routes at `/s/{school:slug}`)
- `app/Support/Navigation.php` (CMS nav item wired to `school-admin.cms.pages`)
- `database/seeders/DemoDataSeeder.php` (demo About/Admissions pages, one blog post, one contact message, hero copy on the demo school)

## Phase 4 — Teacher ↔ Parent Messaging (this session)

| Item | Status |
|---|---|
| Direct messaging between Teacher and Parent, scoped per-child | ✅ Done |
| Mobile-first inbox (list ↔ thread toggle on phones, split-pane on desktop) | ✅ Done |
| Contact list derived from real class-teacher/subject-teacher relationships (no free-for-all messaging) | ✅ Done |
| Unread badges + mark-as-read on open | ✅ Done |
| Near-real-time updates via polling (no websockets/broadcasting infra) | ✅ Done |

### Notes / design choices (Messaging)

- Two tables: `conversations` (`teacher_id` + `guardian_id` + `student_id`, unique together — a thread is
  always "this teacher and this parent, about this specific child", not a generic DM) and `messages`
  (`conversation_id`, `sender_id` as a `users.id` so either party's message row looks the same, `body`,
  `read_at`). Scoping every thread to a student is deliberate: a parent with two children in different
  classes gets separate threads per teacher-per-child, so context is never ambiguous.
- **Who can message whom is derived, not free-form**: a Teacher's contact list is every guardian of a
  student where that teacher is the student's section `classTeacher` OR teaches a subject in the student's
  class; a Parent's contact list is the class teacher and every subject teacher for each of their own
  children. Both directions are computed by the same `App\Livewire\Messaging\Inbox::contactOptions()` and
  re-validated server-side in `startConversation()` (the dropdown `value` a user submits is checked against
  their own freshly-computed contact list before a conversation row is created) — a parent can't start a
  thread with an arbitrary teacher outside their child's class, and vice versa.
- **No websockets/Laravel Echo/Reverb** — matches the existing shared-hosting/minimal-footprint architecture
  decision. The open thread panel uses `wire:poll.5s` to re-fetch messages, which is a plain HTTP
  request Livewire already knows how to diff/morph; adequate for a school's message volume without adding
  a persistent connection requirement shared hosting often can't support.
- `Inbox` is one shared Livewire component for both roles (same pattern as `Attendance\Mark` and
  `Fees\MyFees`) — `auth()->user()->hasRole('teacher')` switches labels/contact-list direction; every
  query is scoped through `conversationsQuery()` (`where('teacher_id', $user->teacher?->id ?? 0)` /
  `where('guardian_id', ...)`), including `send()`'s ownership check, so a tampered `conversationId` can't
  be used to read or post into someone else's thread.
- Mobile UX: a `$showList` boolean (independent of `$conversationId`) toggles between the conversation list
  and the open thread on narrow screens (`hidden`/`flex` swap at the `lg` breakpoint) with a back button in
  the thread header — kept separate from `$conversationId` specifically so tapping "back" doesn't fight
  with the "auto-select the first conversation" logic in `render()`.

### Files added/changed this session (Messaging)

- `database/migrations/2024_01_10_00000{0,1}_create_{conversations,messages}_table.php`
- `app/Models/{Conversation,Message}.php`; added `conversations()` to `app/Models/Teacher.php` and `app/Models/Guardian.php`
- `app/Livewire/Messaging/Inbox.php` + `resources/views/livewire/messaging/inbox.blade.php` (shared Teacher/Parent)
- `routes/web.php` (`teacher.messages`, `parent.messages`)
- `app/Support/Navigation.php` (Messages nav items wired for teacher/parent, previously "coming soon")
- `database/seeders/DemoDataSeeder.php` (demo conversation + two messages between the demo teacher and parent about the demo student)

## Phase 2/3 — Timetable Module (this session)

| Item | Status |
|---|---|
| Time slots (periods) CRUD, School Admin | ✅ Done |
| Weekly timetable grid builder per class/section, School Admin | ✅ Done |
| Teacher "My Timetable" (own periods across every class/section they teach) | ✅ Done |
| Student "My Timetable" (own section's weekly schedule) | ✅ Done |
| Nav placeholders (previously "coming soon" for Teacher/Student, missing entirely for School Admin) wired up | ✅ Done |

### Notes / design choices (Timetable)

- Two tables: `timetable_slots` (school-wide reusable periods — name + start/end time + sort order, e.g.
  "Period 1, 08:00–08:45", set up once per school) and `timetable_entries` (one row per
  section + slot + day-of-week, holding `subject_id` + `teacher_id`; unique on
  `(section_id, timetable_slot_id, day_of_week)` so a section can't have two subjects in the same period on
  the same day). `day_of_week` is `1`–`6` (Monday–Saturday) — matches the typical school week already
  implied elsewhere in the app (no Sunday classes assumed); revisit if a school needs a different week
  shape.
- **Grid builder UX**: School Admin picks a class → section, then sees a slots-×-days grid where each cell
  is a plain `<select>` of that class's subjects, saved instantly on change via
  `wire:change="assign(slotId, day, $event.target.value)"` (no separate "Save" button/batch submit — matches
  the "avoid Livewire magic actions" lesson learned earlier in this project, using an explicit method call
  like `Attendance\Mark::setStatus()` rather than `$set(...)`). Selecting a subject auto-fills that entry's
  `teacher_id` from `Subject::teacher_id`; clearing a cell (back to "—") deletes the entry.
- **Tenant isolation**: every query in `SchoolAdmin\Timetable\Manage` goes through `availableClasses()`/
  `availableSections()` helpers scoped by `where('school_id', ...)` /
  `whereHas('schoolClass', fn ($q) => $q->where('school_id', ...))` — the same defensive pattern already
  used in `Attendance\Mark::availableSections()` — so a tampered `sectionId`/`schoolClassId` Livewire
  property can't be used to read or write another school's timetable.
- `App\Livewire\Timetable\MyTimetable` is one shared read-only component for Teacher and Student (same
  "detect role via `hasRole()`" pattern as `Attendance\Mark`/`Exams\GradeEntry`): Teacher sees every entry
  where `teacher_id` matches their own `teachers.id` (across all classes/sections they teach), Student sees
  every entry for their own `section_id`. Mobile renders one card per day; desktop renders the same
  slots-×-days grid as the builder (read-only, no selects).
- No admin UI for reordering days/handling half-day schedules/exceptions (holidays, substitutions) — out of
  scope for a first pass; the existing Announcements module already covers one-off notices like "no school
  Friday".

### Files added/changed this session (Timetable)

- `database/migrations/2024_01_11_00000{0,1}_create_timetable_{slots,entries}_table.php`
- `app/Models/{TimetableSlot,TimetableEntry}.php`; added `timetableSlots()` to `app/Models/School.php`, `timetableEntries()` to `app/Models/Section.php` and `app/Models/Teacher.php`
- `app/Livewire/SchoolAdmin/Timetable/{Slots,Manage}.php` + matching `resources/views/livewire/school-admin/timetable/{slots,manage}.blade.php` + `_tabs.blade.php`
- `app/Livewire/Timetable/MyTimetable.php` + `resources/views/livewire/timetable/my-timetable.blade.php` (shared Teacher/Student)
- `routes/web.php` (`school-admin.timetable.manage`, `school-admin.timetable.slots`, `teacher.timetable`, `student.timetable`)
- `app/Support/Navigation.php` (Timetable nav item added for school_admin; wired up for teacher/student, previously "coming soon")
- `database/seeders/DemoDataSeeder.php` (two demo time slots + two demo timetable entries for the demo section)

## Phase 2/3 — Timetable Change Requests (this session)

| Item | Status |
|---|---|
| Teacher "All Timetables" view — browse every class/section's schedule, not just their own | ✅ Done |
| Teacher's own periods list with "Request Change" action | ✅ Done |
| Teacher-submitted request form (new subject/class-section/period/day, all optional + required reason) | ✅ Done |
| Teacher's own request history with status (pending/approved/rejected) + admin note | ✅ Done |
| School Admin "Change Requests" tab — filter by pending/approved/rejected/all | ✅ Done |
| Approve action — applies requested field(s) directly onto the underlying `TimetableEntry`, with a conflict check (same section+slot+day already taken by another entry blocks the approval) | ✅ Done |
| Reject action with optional admin note | ✅ Done |

### Notes / design choices (Timetable Change Requests)

- New table `timetable_change_requests`: snapshots the entry's current
  `section_id`/`subject_id`/`timetable_slot_id`/`day_of_week` at request time (`current_*` columns) plus the
  teacher's desired `requested_*` columns (all nullable — a null column means "no change requested for that
  field"), a required `reason` text field, `status` (`pending`/`approved`/`rejected`), and `admin_note` +
  `reviewed_by`/`reviewed_at` for the admin's decision.
- `App\Livewire\Timetable\AllTimetables` replaces `MyTimetable` on the **teacher** route only
  (`teacher.timetable`); the Student route still points at the original read-only `MyTimetable` component
  since students don't need to browse other classes or request changes. The class/section browser reuses the
  same `availableClasses()`/`availableSections()` tenant-scoping pattern as `SchoolAdmin\Timetable\Manage`.
- Request submission validates that at least one `requested_*` field is set (can't submit an empty request)
  and that the entry being modified actually belongs to the requesting teacher
  (`TimetableEntry::where('teacher_id', $teacher->id)->findOrFail(...)`).
- Approval logic (`SchoolAdmin\Timetable\Requests::approve()`) merges each requested field over the entry's
  current value (`$request->requested_section_id ?? $entry->section_id`, etc.), then re-checks the
  `(section_id, timetable_slot_id, day_of_week)` uniqueness constraint against every other entry before
  saving — if another subject is already scheduled in that slot, the approval is blocked with a flashed error
  and the request stays pending so the admin can reject it or ask the teacher to adjust.
- `teacher_id` on the entry is intentionally left untouched on approval (it stays the requesting teacher) even
  if the requested subject's own `Subject::teacher_id` differs — the assumption is the requesting teacher is
  the one who will actually teach that slot going forward.
- Added a "Change Requests" tab alongside the existing "Timetable"/"Time Slots" tabs in
  `resources/views/livewire/school-admin/timetable/_tabs.blade.php`.

### Files added/changed this session (Timetable Change Requests)

- `database/migrations/2024_01_12_000000_create_timetable_change_requests_table.php`
- `app/Models/TimetableChangeRequest.php`; added `timetableChangeRequests()` to `app/Models/Teacher.php`
- `app/Livewire/Timetable/AllTimetables.php` + `resources/views/livewire/timetable/all-timetables.blade.php`
- `app/Livewire/SchoolAdmin/Timetable/Requests.php` + `resources/views/livewire/school-admin/timetable/requests.blade.php`
- `resources/views/livewire/school-admin/timetable/_tabs.blade.php` (added "Change Requests" tab)
- `routes/web.php` (`school-admin.timetable.requests`; `teacher.timetable` now points at `AllTimetables` instead of `MyTimetable`)

## Phase 2/3 — Timetable Change Notifications (this session)

| Item | Status |
|---|---|
| Teacher request modal now pre-fills every field with the period's current subject/class-section/period/day so the teacher edits from the current state instead of blank "no change" dropdowns | ✅ Done |
| Submitting a request now diffs the selected values against the current entry (only actually-changed fields are stored as `requested_*`; unchanged fields stay null) | ✅ Done |
| In-app + email notification sent to the affected students, their guardians, and the teacher whenever a timetable entry is rescheduled or removed — whether done directly by School Admin in the grid, or via an approved teacher change request | ✅ Done |
| Teacher is also notified by email/in-app if their change request is rejected (with the admin's note, if any) | ✅ Done |
| Notification bell in the dashboard topbar (all roles) showing unread count, a dropdown of the latest 10 notifications, mark-one-read and mark-all-read | ✅ Done |

### Notes / design choices (Timetable Change Notifications)

- Added Laravel's standard `notifications` table migration (none existed before) and used the built-in
  `Notifiable` trait already present on `App\Models\User` — no new dependency required.
- `App\Notifications\TimetableChanged` is intentionally generic (`title`, `message`, `changedBy` — no
  timetable-specific fields) so both call sites (direct grid edits and approved change requests, which have
  different "what changed" shapes) can compose their own clear wording rather than forcing one rigid
  before/after schema. Implements `via() = ['mail', 'database']`; `toArray()` feeds the dashboard bell,
  `toMail()` feeds the email (currently logged to `storage/logs/laravel.log` since `MAIL_MAILER=log` by
  default — set real SMTP credentials in `.env` to actually deliver email).
- `App\Support\TimetableNotifier` centralizes recipient resolution: `recipientsFor(Section, ?Teacher)` collects
  the teacher's user + every student in that section + all of those students' guardians (via
  `Student::guardians()`), de-duplicated by user id, and dispatches through `Notification::send()`. A
  `notifyUser()` helper sends to a single user directly (used for "you're no longer teaching this" /
  "your request was rejected" messages that shouldn't go to the whole class).
- **Hook points**:
  - `SchoolAdmin\Timetable\Manage::assign()` — loads the existing entry (if any) for that section+slot+day
    before saving; on removal (subject set back to "—") notifies before deleting; on reassignment (subject
    swapped in the same cell) notifies the section + new subject's teacher with "changed from X to Y", and
    additionally notifies the old teacher individually if the teacher also changed.
  - `SchoolAdmin\Timetable\Requests::approve()` — snapshots the old section/subject/schedule before applying
    the merged requested fields, then notifies the old section's recipients with a before/after summary; if
    the requested section differs from the original, the new section's students/guardians also get a
    separate "new period added" notice. `reject()` notifies just the requesting teacher.
- No queue is configured, so notifications (including the mail send) happen synchronously in the request
  cycle — acceptable at this project's scale (matches the "no WebSockets/queue infra, shared-hosting
  friendly" decision already made for messaging polling); revisit with `ShouldQueue` if a school's roster
  grows large enough that sending to a full class becomes slow.

### Files added/changed this session (Timetable Change Notifications)

- `database/migrations/2024_01_13_000000_create_notifications_table.php`
- `app/Notifications/TimetableChanged.php`
- `app/Support/TimetableNotifier.php`
- `app/Livewire/Timetable/AllTimetables.php` (prefill + diff-based request submission)
- `resources/views/livewire/timetable/all-timetables.blade.php` (current-schedule summary block in the modal, dropdowns pre-selected to current values)
- `app/Livewire/SchoolAdmin/Timetable/Manage.php` (`assign()` now notifies on reassignment/removal)
- `app/Livewire/SchoolAdmin/Timetable/Requests.php` (`approve()`/`reject()` now notify)
- `app/Livewire/NotificationBell.php` + `resources/views/livewire/notification-bell.blade.php`
- `resources/views/layouts/dashboard.blade.php` (bell embedded in the topbar for every role)

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
7. Fees & Finance (structures, invoice generation, manual payment recording,
   Student/Parent "My Fees") is now built — after migrating/seeding, test as
   `admin@demoschool.test`: `/school-admin/fees/structures` (edit/add a fee
   structure) and `/school-admin/fees/invoices` (Generate Invoices, then
   click the $ icon on a row to record a payment and watch the status chip
   move unpaid → partial → paid). Then check `/student/fees` as
   `student@demoschool.test` and `/parent/fees` as `parent@demoschool.test`
   (demo seed already includes one unpaid Monthly Tuition Fee invoice).
8. Examinations & Grades (exam setup, grade entry, report cards) is now
   built — after migrating/seeding, test as `admin@demoschool.test`:
   `/school-admin/exams` (edit the demo "Mid Term Exam", click the list-check
   icon to confirm Mathematics is ticked with max/pass marks, or add another
   subject). Then `/school-admin/exams/grades` (and `/teacher/exams/grades`
   as `teacher@demoschool.test`) to enter a mark for the demo student, and
   confirm it shows up at `/student/exams` (`student@demoschool.test`) and
   `/parent/exams` (`parent@demoschool.test`) with the correct percentage/
   grade/pass-fail chip.
   Absence notifications were deferred — revisit once Communication
   (Phase 4) exists for the notification channel. A recurring/auto-generated
   invoice job (e.g. monthly tuition auto-billed) was also deferred —
   currently a School Admin re-runs "Generate Invoices" each cycle.
9. Reports (Attendance/Exams/Fees, all with CSV export) is now built as
   `admin@demoschool.test` — test `/school-admin/reports/attendance` (date
   range + class filter, then Export CSV), `/school-admin/reports/exams`
   (pick the demo exam, check student ranking + subject breakdown), and
   `/school-admin/reports/fees` (billed/collected/outstanding cards,
   per-class table, overdue invoices list). No charting library is wired up
   yet, so these are stat-card/table only — see Phase 3 table above.
10. Announcements (Phase 4) is now built — after migrating/seeding, test as
    `admin@demoschool.test`: `/school-admin/announcements` (edit the demo
    "Welcome to the new term" notice, try creating one scoped to Grade 5 +
    "students" audience, and one scheduled for a future date/time to confirm
    it shows as "Scheduled" and doesn't appear in feeds yet). Then check
    `/teacher/announcements`, `/student/announcements`, and
    `/parent/announcements` all show the "everyone" one.
11. Next: public CMS front page + admin CMS (WYSIWYG, media library, SEO,
    scheduled publishing) is the largest remaining functional gap. Smaller
    alternatives: real-time messaging (teacher↔parent), email/SMS delivery
    of announcements, Super Admin cross-school analytics, or a charting
    library for trend visualizations in Reports.
