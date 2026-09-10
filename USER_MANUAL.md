Temp dummy users:
| Role | Email |
|---|---|
| Super Admin | superadmin@example.com |
| School Admin | admin@demoschool.test |
| Teacher | teacher@demoschool.test |
| Student | student@demoschool.test |
| Parent | parent@demoschool.test |

password: password


# 📘 School Management System — Complete User Manual

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [System Requirements & Getting Started](#2-system-requirements--getting-started)
3. [Authentication](#3-authentication)
4. [Dashboard Overview](#4-dashboard-overview)
5. [Super Admin Portal](#5-super-admin-portal)
6. [School Admin Portal](#6-school-admin-portal)
7. [Teacher Portal](#7-teacher-portal)
8. [Student Portal](#8-student-portal)
9. [Parent Portal](#9-parent-portal)
10. [Staff Portal](#10-staff-portal)
11. [Notifications System](#11-notifications-system)
12. [User Profile & Settings](#12-user-profile--settings)
13. [Public School Website](#13-public-school-website)
14. [Troubleshooting & FAQ](#14-troubleshooting--faq)

---

## 1. Introduction

### What is the School Management System?

The **School Management System (SMS)** is a comprehensive, cloud-based platform designed to streamline the day-to-day operations of schools. It provides a centralized hub for managing students, teachers, staff, academics, attendance, examinations, fees, payroll, communication, and school content — all accessible from any modern web browser.

### Key Highlights

| Feature | Description |
|---------|-------------|
| **Multi-Role Access** | 6 distinct portals — Super Admin, School Admin, Teacher, Student, Parent, Staff |
| **Multi-School Support** | Manage multiple schools from a single platform |
| **Multi-Campus** | Each school can operate across multiple physical campuses |
| **Real-Time Notifications** | In-app and email notifications for all key events |
| **Messaging** | Direct teacher ↔ parent communication |
| **Content Management** | Built-in CMS for school websites, blogs, and photo galleries |
| **Data Import/Export** | Bulk student import via CSV, export any report as CSV |
| **PWA Support** | Installable as an app on mobile and desktop devices |
| **Responsive Design** | Works seamlessly on desktop, tablet, and mobile |
| **Audit Trail** | Tracks all important data changes automatically |

### Technology Stack

| Component | Technology |
|-----------|------------|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Livewire 4, Tailwind CSS 3, Alpine.js 3 |
| Authentication | Laravel Breeze |
| Permissions | Spatie Laravel Permission |
| Database | MySQL 8 / SQLite |
| Build Tool | Vite 6 |

---

## 2. System Requirements & Getting Started

### For Users (End Users)

- **Web Browser**: Chrome, Firefox, Safari, Edge (latest 2 versions)
- **Internet Connection**: Required for all operations
- **Screen Resolution**: Minimum 320px (mobile) — optimal at 1024px+ (desktop)
- **No software installation required** — access via your school's URL

### First-Time Login

1. You will receive your **login credentials** (email and temporary password) from your school administrator
2. Navigate to your school's URL (e.g., `https://school.example.com`)
3. Click **"Log in"** in the top navigation bar
4. Enter your email and temporary password
5. You may be prompted to **verify your email** — check your inbox for the verification link
6. You will be redirected to your **role-specific dashboard**
7. **Important**: Change your password immediately via Profile Settings

### PWA Installation (Optional)

For the best mobile experience, install the app on your device:

1. Open the school URL in your mobile browser
2. Look for the **"Install App"** button or the browser's install prompt
3. Follow the on-screen instructions
4. The app will appear on your home screen like a native app

---

## 3. Authentication

### Login

```
URL: /login
```

| Field | Description |
|-------|-------------|
| Email | Your registered email address |
| Password | Your account password |
| Remember Me | Stay logged in across browser sessions |

**Steps:**
1. Enter your **email** and **password**
2. (Optional) Check **"Remember Me"** to stay logged in
3. Click **"Log in"**

> ⚠️ After 5 failed login attempts, you will be temporarily locked out for security.

### Forgot Password

If you've forgotten your password:

1. Click **"Forgot your password?"** on the login page
2. Enter your registered email address
3. Click **"Send Password Reset Link"**
4. Check your email inbox for the reset link
5. Click the link and enter a **new password**
6. Confirm your new password and click **"Reset Password"**

### Email Verification

New accounts require email verification:

1. After first login, you'll see a verification prompt
2. Click **"Resend Verification Email"** if you didn't receive it
3. Open the email and click the **verification link**
4. You'll be redirected to your dashboard

### Profile Management

All users can manage their profile:

1. Click your **avatar** in the top-right corner
2. Select **"Profile"**
3. Update your **name** or **email**
4. Click **"Save"** to apply changes

### Change Password

1. Go to **Profile** (click avatar → Profile)
2. Scroll to the **Password** section
3. Enter your **current password**
4. Enter your **new password** (min 8 characters)
5. Confirm your new password
6. Click **"Update Password"**

### Logout

1. Click your **avatar** in the top-right corner
2. Select **"Log Out"**
3. You'll be redirected to the login page

---

## 4. Dashboard Overview

Your dashboard is the first screen you see after logging in. It provides a quick overview of your role's key metrics and quick access to all available features.

### Dashboard Layout

```
┌─────────────────────────────────────────────────────────┐
│  ☰ Menu    🔍 Search     🔔 Notifications   👤 Profile  │
├────────────┬────────────────────────────────────────────┤
│            │                                            │
│  SIDEBAR   │     STAT CARDS (Key Metrics)               │
│            │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐      │
│  📊 Home   │  │  42  │ │  12  │ │   8  │ │  23  │      │
│  📚 Module │  │Stat 1│ │Stat 2│ │Stat 3│ │Stat 4│      │
│  📋 Module │  └──────┘ └──────┘ └──────┘ └──────┘      │
│  ⚙️ Module │                                            │
│            │     QUICK LINKS                             │
│            │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐      │
│            │  │ Link │ │ Link │ │ Link │ │ Link │      │
│            │  └──────┘ └──────┘ └──────┘ └──────┘      │
│            │                                            │
├────────────┴────────────────────────────────────────────┤
│  [Dashboard] [Module 1] [Module 2] [Module 3] [Module] │  ← Mobile bottom bar
└─────────────────────────────────────────────────────────┘
```

### Dashboard Metrics by Role

| Role | Metrics Displayed |
|------|-------------------|
| **Super Admin** | Schools Count, Total Users, School Admins, Teachers |
| **School Admin** | Students, Teachers, Classes, Active Staff, Fees Due |
| **Teacher** | My Sections, My Subjects |
| **Student** | Class, Section, Admission No., Fees Due |
| **Parent** | My Children Count |
| **Staff** | *(minimal — access via sidebar)* |

### Quick Links

Below the stat cards, you'll find a grid of **Quick Links** — shortcuts to the most common actions for your role. Click any card to navigate directly to that feature.

### Notification Bell 🔔

Located in the top navigation bar:

- **Red badge** shows the number of unread notifications
- Click the bell icon to see your **latest 10 notifications**
- Click **"Mark all as read"** to clear the badge
- Click a specific notification to navigate to the relevant page

---

## 5. Super Admin Portal

> **Access Level**: Platform-wide — manages ALL schools in the system

The Super Admin has the highest level of access. They can create, edit, and delete schools, and have visibility into system-wide metrics.

### 5.1 Navigation Sidebar

```
OVERVIEW
  ├── 📊 Dashboard
  └── 🏫 Schools

PLATFORM (Coming Soon)
  ├── 👥 Admins
  ├── ⚙️ System Settings
  └── 📈 Global Analytics
```

### 5.2 Managing Schools

```
Route: /super-admin/schools
```

This is the primary function of the Super Admin — managing all schools on the platform.

#### Viewing Schools

1. Navigate to **Schools** from the sidebar
2. View all schools in a card grid layout
3. Each card shows:
   - **School Name** and status badge (Active/Inactive)
   - **Logo** (if uploaded)
   - **Email, Phone, Address**
   - **Student Count** and **Teacher Count**
   - **Primary/Secondary Theme Colors** (visual preview)
4. Use the **search bar** to find schools by name or code
5. **Paginated** — navigate between pages at the bottom

#### Creating a New School

1. Click the **"Add School"** button (top-right of the Schools page)
2. Fill in the required fields:

| Field | Required | Description |
|-------|----------|-------------|
| School Name | ✅ | Official name of the school |
| Slug | ✅ | URL-friendly identifier (auto-generated from name, must be unique) |
| Code | ✅ | Short unique code (e.g., "DEMO01") |
| Email | | School contact email |
| Phone | | School contact phone |
| Address | | Street address |
| City | | City name |
| Country | | Country name |
| Academic Year | | Current academic year (e.g., "2025-2026") |
| Status | ✅ | Active or Inactive |
| Primary Color | | Theme color (color picker + hex code) |
| Secondary Color | | Theme color (color picker + hex code) |
| Logo | | School logo image |

3. Click **"Save"** to create the school

#### Editing a School

1. Click the **Edit** (pencil) icon on the school card
2. Modify any fields
3. Click **"Update"** to save changes

#### Deleting a School

1. Click the **Delete** (trash) icon on the school card
2. Confirm the deletion in the dialog
3. **⚠️ Warning**: This action is permanent and will remove all associated data

#### Branding

Each school has its own **theme colors** that are applied throughout the UI:

- **Primary Color**: Main brand color (used for buttons, sidebar, accents)
- **Secondary Color**: Accent color (used for secondary elements)
- **Logo**: Displayed in the sidebar and on the public website

Changes to branding take effect **immediately** — no rebuild required.

### 5.3 Dashboard (Super Admin)

| Metric | Description |
|--------|-------------|
| **Schools** | Total number of registered schools |
| **Total Users** | All users across all schools |
| **School Admins** | Users with the School Admin role |
| **Teachers** | Total teachers across all schools |

---

## 6. School Admin Portal

> **Access Level**: Full control within their assigned school

The School Admin is the most feature-rich role. They manage all aspects of their school — academics, students, teachers, staff, attendance, exams, fees, payroll, announcements, content, and reports.

### 6.1 Navigation Sidebar

```
OVERVIEW
  └── 📊 Dashboard

ACADEMIC
  ├── 📋 Admissions
  ├── 🎓 Students
  ├── 👨‍🏫 Teachers
  ├── 👷 Staff
  ├── 🏫 Classes & Sections
  ├── 📚 Subjects
  └── 📅 Timetable

OPERATIONS
  ├── ✅ Attendance
  ├── ✅ Staff Attendance
  ├── 📝 Exams
  ├── 📖 Homework
  └── 🏖️ Leave Requests

FINANCE
  ├── 💰 Fees
  └── 💵 Payroll

PEOPLE & CONTENT
  ├── 📢 Announcements
  ├── 🌐 CMS
  └── 📊 Reports

SYSTEM
  ├── 📁 Data Import/Export
  ├── 🏢 Campuses
  └── ⚙️ School Profile
```

### 6.2 Admissions Management

```
Route: /school-admin/admissions
```

The admissions module manages the student enrollment pipeline — from initial inquiry to full enrollment.

#### The Admission Pipeline

```
  Inquiry → Interview Scheduled → Test Scheduled → Offered → ENROLLED ✅
                ↓                                       ↓
            Rejected                               Withdrawn
```

| Stage | Description |
|-------|-------------|
| **Inquiry** | New application received (default on creation) |
| **Interview Scheduled** | Interview date set |
| **Test Scheduled** | Admission test date set |
| **Offered** | Admission offer extended |
| **Enrolled** | ✅ Student account automatically created |
| **Rejected** | Application rejected |
| **Withdrawn** | Applicant withdrew their application |

#### Viewing Applications

1. Navigate to **Admissions** from the sidebar
2. View all applications in a table with columns:
   - Applicant Name, Father Name, Phone, Email
   - Applied Class, Source
   - Status (color-coded badge)
   - Date Applied
3. **Filter** by: Status, Class, Search (name/phone/email)
4. **Paginated** — 10 per page

#### Adding a New Admission

1. Click **"Add Admission"**
2. Fill in the application form:

| Field | Required | Description |
|-------|----------|-------------|
| Applicant Name | ✅ | Full name of the student |
| Father Name | | Father/guardian name |
| Phone | | Contact number |
| Email | | Contact email |
| Gender | | Male / Female / Other |
| Date of Birth | | Student's DOB |
| Address | | Home address |
| Applied Class | | The class they're applying for |
| Source | | How they found the school (referral, walk-in, website, etc.) |

3. Click **"Save"** — status defaults to **Inquiry**

#### Moving Through the Pipeline

1. Click **"View"** on an application
2. Update the **stage** using the stage selector
3. For each stage, fill in relevant details:

| Stage Fields | Description |
|-------------|-------------|
| Interview Date | When the interview is scheduled |
| Interview Notes | Notes from the interview |
| Test Date | When the admission test is scheduled |
| Test Score | Score out of 100 |
| Test Notes | Notes about the test |
| Decision Notes | Reason for offer/rejection |

4. Click **"Update"** to save

#### Enrolling a Student

When you move an applicant to the **"Enrolled"** stage:

1. The system **automatically creates**:
   - A **User account** (email + auto-generated password)
   - A **Student record** linked to the class
   - An **Admission Number** (format: `ADM-YY-NNNN`)
2. The student receives an **email notification** with their login credentials
3. The enrollment details are displayed for your reference

> 💡 **Tip**: Always verify the admission details are correct before enrolling.

#### Managing Documents

Each admission can have supporting documents attached:

1. In the admission detail view, scroll to **Documents**
2. Click **"Upload Document"**
3. Enter a title and select the file (max 10 MB)
4. Uploaded documents can be **viewed**, **downloaded**, or **deleted**

---

### 6.3 Student Management

```
Route: /school-admin/students
```

Manage all enrolled students — create profiles, update information, upload documents, and handle student records.

#### Viewing Students

1. Navigate to **Students** from the sidebar
2. View students in a responsive table (card layout on mobile):
   - Name, Admission Number, Class/Section, Campus, Email
   - Action buttons: Documents, Edit, Delete
3. **Filter** by: Campus, Class, Section, Search (name/email/admission number)
4. **Paginated** — 10 per page

#### Adding a New Student

1. Click **"Add Student"**
2. Fill in the form:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Full name |
| Email | ✅ | Unique email address (login credential) |
| Phone | | Contact number |
| Admission Number | ✅ | Unique identifier |
| Campus | | Assigned campus |
| Class | | Assigned class |
| Section | | Assigned section (depends on class selection) |
| Gender | | Male / Female / Other |
| Date of Birth | | DOB |
| Blood Group | | Blood group |
| Nationality | | Country of nationality |
| Religion | | Religion |
| Emergency Contact Name | | Emergency contact person |
| Emergency Contact Phone | | Emergency contact number |
| Emergency Contact Relation | | Relationship (Father, Mother, etc.) |
| Medical Notes | | Any medical conditions or allergies |
| Notes | | General notes |

3. Click **"Save"**
4. **⚠️ Save the generated password** — displayed once after creation
5. The student receives an **email notification** with their login credentials

#### Editing a Student

1. Click the **Edit** (pencil) icon
2. Modify any field
3. Click **"Update"** to save changes

#### Deleting a Student

1. Click the **Delete** (trash) icon
2. Confirm the deletion
3. The student record and associated user account are removed

#### Student Documents

1. Click the **Documents** icon on a student row
2. Upload documents with a title (max 10 MB per file)
3. View, download, or delete uploaded documents

---

### 6.4 Teacher Management

```
Route: /school-admin/teachers
```

#### Viewing Teachers

| Column | Description |
|--------|-------------|
| Name | Teacher's full name |
| Employee ID | Unique employee identifier |
| Email | Contact email |
| Qualification | Academic qualification |
| Assigned Subjects | Subjects they teach |

#### Adding a New Teacher

1. Click **"Add Teacher"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Full name |
| Email | ✅ | Unique email |
| Phone | | Contact number |
| Employee ID | ✅ | Unique employee ID |
| Qualification | | Academic qualification |
| Specialization | | Subject specialization |
| Employment Type | | Full-time / Part-time / Contract |
| Campus | | Assigned campus |

3. Click **"Save"**
4. **Save the generated password** — displayed once
5. Teacher receives an email with credentials

---

### 6.5 Staff Management (Non-Teaching)

```
Route: /school-admin/staff
```

Manage non-teaching staff — admin officers, librarians, lab assistants, cleaners, security, etc.

#### Adding New Staff

1. Click **"Add Staff"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Full name |
| Email | ✅ | Unique email |
| Phone | | Contact number |
| Employee ID | ✅ | Unique employee ID |
| Designation | ✅ | Job title (e.g., "Accountant", "Librarian") |
| Department | | Department name |
| Employment Type | | Full-time / Part-time / Contract |
| Campus | | Assigned campus |

3. Click **"Save"**
4. **Save the generated password** — displayed once

---

### 6.6 Classes & Sections

```
Route: /school-admin/classes
```

Organize your school into classes and sections.

#### Understanding the Structure

```
School
  ├── Grade 1
  │     ├── Section A (Class Teacher: Mr. Ahmed, Capacity: 40)
  │     └── Section B (Class Teacher: Mrs. Fatima, Capacity: 40)
  ├── Grade 2
  │     ├── Section A
  │     └── Section B
  └── Grade 3
        └── Section A
```

#### Managing Classes

1. Navigate to **Classes & Sections** from the sidebar
2. Classes are displayed as **cards** with the student count
3. Click **"Add Class"** to create a new class:
   - **Name** (required): e.g., "Grade 1", "Class 10"
   - **Sort Order**: Controls display order (0-255)
   - **Campus**: Assign to a campus

#### Managing Sections

1. Within a class card, click **"Add Section"** or view existing sections
2. Sections are displayed in a table within each class:

| Field | Description |
|-------|-------------|
| Section Name | e.g., "A", "B", "Morning" |
| Class Teacher | Assigned teacher (from the Teachers list) |
| Capacity | Max students (1-200, default 40) |
| Students | Current student count |

3. To add a section:
   - Click **"Add Section"**
   - Enter section **Name**, select **Class Teacher**, set **Capacity**
   - Click **"Save"**

> 💡 **Tip**: The Class Teacher is the primary teacher responsible for that section. They can mark attendance and manage homework for their section.

---

### 6.7 Subject Management

```
Route: /school-admin/subjects
```

Define subjects and assign them to classes and teachers.

#### Adding a Subject

1. Click **"Add Subject"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Subject name (e.g., "Mathematics") |
| Code | ✅ | Unique code within the class (e.g., "MATH101") |
| Class | | Assigned class |
| Teacher | | Assigned teacher |
| Is Elective | | Check if this is an elective subject |

3. Click **"Save"**

---

### 6.8 Timetable Management

```
Routes:
  /school-admin/timetable/manage  → Weekly timetable grid editor
  /school-admin/timetable/slots   → Manage time period slots
  /school-admin/timetable/requests → Teacher change requests
```

#### Managing Time Slots (Periods)

Before building a timetable, define your daily time periods:

1. Navigate to **Timetable** → **Slots**
2. Click **"Add Slot"**
3. Enter:
   - **Name**: e.g., "1st Period", "Morning Assembly"
   - **Start Time**: e.g., "08:00"
   - **End Time**: e.g., "08:45"
4. Slots are automatically ordered by start time
5. **Create, Edit, or Delete** slots as needed

#### Building the Timetable Grid

1. Navigate to **Timetable** from the sidebar
2. **Select a Class** → **Select a Section**
3. You'll see a **weekly grid** (Monday through Saturday):

```
              │ Mon      │ Tue      │ Wed      │ Thu      │ Fri      │ Sat
──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┼──────────
1st Period    │ Math     │ English  │ Science  │ Math     │ English  │ Science
              │ Mr. Ali  │ Ms. Sara │ Mr. Khan │ Mr. Ali  │ Ms. Sara │ Mr. Khan
──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┼──────────
2nd Period    │ English  │ Math     │ English  │ Science  │ Math     │ English
              │ Ms. Sara │ Mr. Ali  │ Ms. Sara │ Mr. Khan │ Mr. Ali  │ Ms. Sara
──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┼──────────
3rd Period    │ Science  │ Urdu     │ Math     │ English  │ Urdu     │ Math
              │ Mr. Khan │ Mr. Hamid│ Mr. Ali  │ Ms. Sara │ Mr. Hamid│ Mr. Ali
```

4. **To assign a subject**: Click any empty cell → select a Subject from the dropdown
5. **To remove a subject**: Click the assigned cell → select "Remove"
6. The **teacher is automatically assigned** from the subject's teacher
7. **Notifications** are sent automatically:
   - New teacher is notified when assigned
   - Old teacher is notified when removed

#### Managing Change Requests

Teachers can submit requests to change their timetable. As admin, you review them:

1. Navigate to **Timetable** → **Requests**
2. View pending requests with details:
   - **Current**: Current section, subject, slot, day
   - **Requested**: New section, subject, slot, day
   - **Reason**: Teacher's explanation
3. **Approve**: Updates the timetable and checks for conflicts
4. **Reject**: Add an admin note explaining why

---

### 6.9 Attendance Management

#### Student Attendance

```
Route: /school-admin/attendance
```

1. Navigate to **Attendance** from the sidebar
2. **Select a Section** and **Date**
3. The student list loads with the following columns:

| Column | Description |
|--------|-------------|
| Student Name | Name of the student |
| Status | Toggle buttons for each status |
| Remarks | Optional notes per student |

4. **Status options** (click to toggle):

| Status | Color | Description |
|--------|-------|-------------|
| ✅ Present | Green | Student is present |
| ❌ Absent | Red | Student is absent |
| ⏰ Late | Yellow | Student arrived late |
| 🟡 Half Day | Orange | Student left early |
| 📋 Leave | Blue | Approved leave of absence |

5. Use **"Mark All Present"** or **"Mark All Absent"** for quick marking
6. Add **individual remarks** if needed (e.g., "Left early for dentist appointment")
7. Click **"Save Attendance"** to record

> ⚠️ **Important**: When a student is marked absent, their **parents/guardians receive an automatic notification**.

#### Staff Attendance

```
Route: /school-admin/staff/attendance
```

Same process as student attendance, but for **teachers and non-teaching staff**:

1. Navigate to **Staff Attendance**
2. **Select a Date**
3. All teachers and staff are listed
4. Mark each person's status (Present, Absent, Late, Half Day, Leave)
5. Add remarks as needed
6. Click **"Save"**

---

### 6.10 Examination Management

```
Routes:
  /school-admin/exams        → Manage exams
  /school-admin/exams/grades → Enter grades
```

#### Creating an Exam

1. Navigate to **Exams** from the sidebar
2. Click **"Add Exam"**
3. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Exam Name | ✅ | e.g., "Mid-Term Exam", "Final Exam" |
| Class | ✅ | Which class this exam is for |
| Term | | e.g., "Fall 2025", "Spring 2026" |
| Start Date | | Exam start date |
| End Date | | Exam end date |

4. Click **"Save"**
5. After creating the exam, **configure exam subjects**:
   - Select which subjects to include
   - Set **Max Marks** (default: 100) per subject
   - Set **Pass Marks** (default: 40) per subject

#### Entering Grades

1. Navigate to **Exams** → **Grades**
2. Select the **Exam** from the dropdown
3. Select the **Subject**
4. Enter marks for each student:

| Student | Marks Obtained | Max Marks | Remarks |
|---------|---------------|-----------|---------|
| Ahmed Ali | 85 | 100 | Excellent |
| Sara Khan | 72 | 100 | Good |
| Hassan Mir | 35 | 100 | Needs improvement |

5. Marks are validated against the max marks
6. Click **"Save Grades"**

> 💡 **Tip**: Teachers can only enter grades for subjects they are assigned to teach.

#### Viewing Report Cards

Students and parents can view report cards with:

- Per-subject marks, max marks, and remarks
- Total marks obtained vs. maximum
- Percentage calculation
- Letter grade assignment:

| Percentage | Grade |
|------------|-------|
| 90% and above | A+ |
| 80% - 89% | A |
| 70% - 79% | B+ |
| 60% - 69% | B |
| 50% - 59% | C |
| 40% - 49% | D |
| Below 40% | F |

---

### 6.11 Homework Management

```
Route: /school-admin/homework
```

#### Creating Homework

1. Navigate to **Homework** from the sidebar
2. Click **"Add Homework"**
3. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Title | ✅ | Assignment title |
| Description | | Detailed instructions |
| Class | ✅ | Target class |
| Subject | ✅ | Target subject |
| Due Date | ✅ | When it's due |
| Max Marks | | Maximum marks for grading |
| Attachment | | Upload a file (max 10 MB) — worksheet, reference material, etc. |

4. Click **"Save"**
5. **All students in the class receive an automatic notification**

#### Viewing Submissions

1. In the homework list, click **"View Submissions"** on any assignment
2. See all students and their submission status:

| Student | Status | Submitted | Marks | Feedback |
|---------|--------|-----------|-------|----------|
| Ahmed Ali | ✅ Submitted | Sep 5, 2026 | 18/20 | Well done |
| Sara Khan | ⏳ Pending | — | — | — |
| Hassan Mir | ✅ Graded | Sep 4, 2026 | 15/20 | Good effort |

3. **To grade a submission**: Enter marks and feedback, then click "Save"

---

### 6.12 Leave Management

```
Route: /school-admin/leave
```

Review and manage leave requests from students (submitted by parents) and staff/teachers.

#### Viewing Requests

1. Navigate to **Leave Requests** from the sidebar
2. View all leave requests as cards with:
   - Requester name (student or staff/teacher)
   - Date range (from → to)
   - Number of days
   - Reason
   - Status badge (Pending / Approved / Rejected)
3. **Filter** by: Status (Pending/All), Type (All/Student/Staff)

#### Approving a Request

1. Review the request details
2. Click **"Approve"**
3. The requester receives a **notification** confirming approval

#### Rejecting a Request

1. Review the request details
2. Click **"Reject"**
3. Enter an **admin note** explaining the rejection reason
4. The requester receives a **notification** with the reason

---

### 6.13 Fees Management

```
Routes:
  /school-admin/fees/structures → Fee structures (templates)
  /school-admin/fees/invoices  → Invoices & payments
```

#### Fee Structures (Templates)

Fee structures are **reusable templates** for generating invoices.

1. Navigate to **Fees** → **Structures**
2. Click **"Add Structure"**
3. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | e.g., "Monthly Tuition", "Lab Fee" |
| Amount | ✅ | Fee amount |
| Class | | Apply to a specific class (or leave blank for all) |
| Frequency | | One-time / Monthly / Quarterly / Term / Annual |

4. Click **"Save"**

#### Generating Invoices

1. Navigate to **Fees** → **Invoices**
2. Click **"Generate Invoice"**
3. Configure:

| Field | Description |
|-------|-------------|
| Fee Structure | Select a template (auto-fills title and amount) |
| Class | Target class |
| Student | Select individual student or generate for ALL students in the class |
| Title | Invoice title (auto-filled from structure) |
| Amount | Invoice amount (auto-filled from structure) |
| Due Date | Payment deadline |

4. Click **"Generate"**
5. **Each student receives a notification** with invoice details

#### Recording Payments

1. On an unpaid or partially-paid invoice, click **"Record Payment"**
2. Enter:

| Field | Required | Description |
|-------|----------|-------------|
| Amount | ✅ | Payment amount |
| Method | ✅ | Cash / Bank Transfer / Cheque / Online / Other |
| Date | ✅ | Payment date |
| Notes | | Additional notes |

3. Click **"Save"**
4. The student receives a **payment confirmation notification** with remaining balance
5. Invoice status automatically updates:
   - **Paid**: Fully paid
   - **Partial**: Partially paid
   - **Unpaid**: No payments recorded

#### Applying Discounts

1. On an invoice, click **"Add Discount"**
2. Select the discount type:

| Type | Description |
|------|-------------|
| Sibling | Discount for siblings |
| Scholarship | Academic scholarship |
| Staff Child | Child of school employee |
| Need-Based | Financial need |
| Custom | Other |

3. Enter:
   - **Value**: Discount amount or percentage
   - **Is Percentage**: Check for % discount, uncheck for fixed amount
   - **Notes**: Reason for discount
4. Click **"Apply"**
5. The invoice balance is adjusted automatically

> 💡 **Tip**: Multiple discounts can be applied to a single invoice. The total discount is subtracted from the invoice amount.

---

### 6.14 Payroll Management

```
Routes:
  /school-admin/finance/salary-structures → Define salaries
  /school-admin/finance/payroll           → Generate payslips
```

#### Salary Structures

Define how each teacher/staff member is paid:

1. Navigate to **Payroll** → **Salary Structures**
2. Click **"Add Salary Structure"**
3. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Employee Type | ✅ | Teacher or Staff |
| Employee | ✅ | Select the employee |
| Basic Salary | ✅ | Base monthly salary |
| House Allowance | | Housing allowance |
| Transport Allowance | | Transportation allowance |
| Other Allowance | | Any other allowances |
| Deduction | | Standard monthly deduction |
| Effective From | | Start date for this salary |

4. Click **"Save"**
5. Only **one active structure** per employee is allowed — activating a new one deactivates the previous

#### Generating Payslips

1. Navigate to **Payroll** from the sidebar
2. Select a **Period** (YYYY-MM format)
3. Click **"Generate Payslips"** — creates payslips from all active salary structures
4. View the **Summary**:
   - Draft Total (total pending payslips)
   - Paid Total (total paid payslips)
   - Payslip Count

5. Per payslip, you can:
   - Add a **Bonus** adjustment
   - Add an **Extra Deduction**
   - View the **Gross** breakdown (Basic + House + Transport + Other)
   - View the **Net** salary calculation
6. **Actions per payslip**:

| Action | Description |
|--------|-------------|
| **Mark Paid** | Marks the payslip as paid (records payment date) |
| **Revert to Draft** | Undo payment marking (for corrections) |
| **Delete** | Remove draft payslips only |

---

### 6.15 Announcements

```
Route: /school-admin/announcements
```

Broadcast important messages to students, teachers, parents, or everyone.

#### Creating an Announcement

1. Navigate to **Announcements** from the sidebar
2. Click **"Add Announcement"**
3. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Title | ✅ | Announcement headline |
| Body | ✅ | Full announcement content (max 5000 characters) |
| Audience | ✅ | Who should see it: Everyone / Teachers / Students / Parents |
| Class | | Optional: Target a specific class (only that class's students, parents, and teachers see it) |
| Publish Now | | Check to publish immediately |
| Publish Date | | Schedule for future publishing |

4. Click **"Publish"**
5. **All targeted users receive a notification** with the announcement

#### Announcement Visibility by Role

| Role | Sees Announcements For |
|------|----------------------|
| Super Admin / School Admin | Everyone, Teachers, Students, Parents |
| Teacher | Everyone, Teachers |
| Student | Everyone, Students |
| Parent | Everyone, Parents |

---

### 6.16 Content Management System (CMS)

```
Routes:
  /school-admin/cms/pages    → Static pages
  /school-admin/cms/posts    → Blog posts
  /school-admin/cms/gallery  → Photo gallery
  /school-admin/cms/messages → Contact form submissions
```

#### CMS Pages

Create and manage static pages for your school's public website:

1. Click **"Add Page"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Title | ✅ | Page title |
| Slug | | URL-friendly slug (auto-generated) |
| Content | | Page content (rich text editor with bold, italic, headings, lists, links) |
| Meta Title | | SEO title |
| Meta Description | | SEO description |
| Status | | Draft / Published |

3. Click **"Save"**

#### Blog Posts

Create blog posts for your school website:

1. Click **"Add Post"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Title | ✅ | Post title |
| Slug | | Auto-generated |
| Excerpt | | Short summary |
| Content | | Full post content (rich text editor) |
| Featured Image | | Upload an image (max 2 MB) |
| Status | | Draft / Published |
| Publish Date | | Schedule for future |

3. Click **"Save"**

#### Photo Gallery

Manage photos for your school website:

1. **Upload Images**: Click **"Upload Images"**, select multiple images (max 2 MB each)
2. **Add Captions**: Enter a caption for each image
3. **Reorder**: Use the ↑ (move up) and ↓ (move down) arrows to reorder
4. **Delete**: Click the trash icon to remove an image

#### Contact Form Messages

View messages submitted through your school website's contact form:

1. View all submissions in a list
2. **Unread messages** are highlighted
3. Click to view the full message
4. **Mark as read** or **Delete** as needed

---

### 6.17 Reports

```
Routes:
  /school-admin/reports/attendance
  /school-admin/reports/exams
  /school-admin/reports/fees
```

#### Attendance Report

1. Navigate to **Reports** → **Attendance**
2. **Filter** by: Date Range (default: month to date), Class
3. View per-student summary:

| Student | Present | Absent | Late | Half Day | Leave | Total | Rate |
|---------|---------|--------|------|----------|-------|-------|------|
| Ahmed Ali | 18 | 1 | 1 | 0 | 0 | 20 | 90% |
| Sara Khan | 20 | 0 | 0 | 0 | 0 | 20 | 100% |

4. **Overall Summary**: Student count, Average attendance rate, Total absences
5. **Export CSV** — download the report as a spreadsheet

#### Exam Report

1. Navigate to **Reports** → **Exams**
2. Select an **Exam** from the dropdown
3. View two summaries:
   - **Student Summary**: Per-student total marks, percentage, pass/fail
   - **Subject Summary**: Per-subject average, highest, lowest marks
4. **Overall**: Student count, Class average %, Pass rate %, Top scorer
5. **Export CSV** — download the report

#### Fees Report

1. Navigate to **Reports** → **Fees**
2. View:
   - **Overall Summary**: Total Billed, Total Paid, Total Due, Collection Rate %
   - **Per-Class Breakdown**: Billed, Paid, Due per class
   - **Overdue List**: Unpaid invoices past due date
3. **Export CSV** — download the overdue list

---

### 6.18 Data Import/Export

```
Route: /school-admin/data-tools
```

#### Importing Students via CSV

1. Navigate to **Data Tools** from the sidebar
2. Click **"Download Template"** to get the CSV template
3. Fill in the template with student data:

| Column | Required | Description |
|--------|----------|-------------|
| name | ✅ | Student name |
| email | ✅ | Unique email |
| phone | | Phone number |
| admission_number | ✅ | Unique admission number |
| class_name | ✅ | Must match an existing class |
| section_name | ✅ | Must match an existing section |
| gender | | male / female / other |
| date_of_birth | | Format: YYYY-MM-DD |

4. Click **"Upload CSV"** and select your file
5. **Preview** the import:
   - Each row is validated
   - ✅ Valid rows are highlighted in green
   - ❌ Invalid rows show error messages
6. Review the **Valid/Invalid counts**
7. Click **"Commit Import"** to process (creates User + Student accounts)
8. **Download Credentials CSV** with generated passwords for distribution

> ⚠️ **Important**: Passwords are only shown once. Download the credentials file and distribute it securely.

#### Exporting Data

Available CSV exports:

| Export Type | Data Included |
|------------|---------------|
| **Students** | Name, email, phone, admission number, class, section, gender, DOB, status |
| **Teachers** | Name, email, phone, employee ID, qualification, specialization, type |
| **Staff** | Name, email, phone, employee ID, designation, department, type |
| **Fee Invoices** | Student, class, title, amount, paid, balance, status, due date |
| **Attendance** | Student, date, status |
| **Exam Results** | Student, exam, subject, marks, max marks, pass marks, remarks |

1. Click the respective **"Export CSV"** button
2. The file downloads automatically to your device

---

### 6.19 Campus Management

```
Route: /school-admin/campuses
```

Manage multiple physical campuses for your school.

#### Adding a Campus

1. Click **"Add Campus"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Campus name (e.g., "Main Campus", "North Branch") |
| Code | | Short campus code |
| Address | | Campus address |
| City | | City |
| Phone | | Contact number |
| Is Default | | Set as the default campus |
| Status | | Active / Inactive |

3. Click **"Save"**

> ⚠️ **Note**: Only one campus can be the default. You cannot delete the default campus — change the default first.

---

### 6.20 School Profile & Settings

```
Route: /school-admin/settings
```

Configure your school's profile and branding.

#### School Information

| Field | Description |
|-------|-------------|
| School Name | Official school name |
| Email | Contact email |
| Phone | Contact phone |
| Address | Street address |
| City | City |
| Country | Country |
| Academic Year | Current academic year |

#### Branding

| Field | Description |
|-------|-------------|
| Primary Color | Main brand color (color picker + hex input) |
| Secondary Color | Accent color |
| Logo | School logo image |

These colors are applied **throughout the application** — sidebar, buttons, and accent elements.

#### Public Website Content

| Field | Description |
|-------|-------------|
| Hero Headline | Main headline on the public homepage |
| Hero Subheadline | Supporting text |
| Hero Image | Large banner image for the homepage |

---

## 7. Teacher Portal

> **Access Level**: Scoped to their assigned sections, subjects, and students

Teachers have focused access for their teaching responsibilities — marking attendance, entering grades, managing homework, communicating with parents, and viewing their personal records.

### 7.1 Navigation Sidebar

```
OVERVIEW
  └── 📊 Dashboard

TEACHING
  ├── 📅 Timetable
  ├── ✅ Attendance
  ├── 📝 Grades
  └── 📖 Homework

PERSONAL
  ├── ✅ My Attendance
  ├── 🏖️ Leave
  └── 💵 My Pay

COMMUNICATION
  ├── 📢 Announcements
  └── 💬 Messages
```

### 7.2 Dashboard

| Metric | Description |
|--------|-------------|
| **My Sections** | Number of sections where you are the class teacher |
| **My Subjects** | Number of subjects assigned to you |

### 7.3 Viewing Timetable

```
Route: /teacher/timetable
```

1. Navigate to **Timetable** from the sidebar
2. View your complete weekly schedule in a grid format
3. **Desktop**: Full weekly grid view
4. **Mobile**: Per-day card view with swipe navigation
5. Each cell shows:
   - **Subject name**
   - **Section** (e.g., "Grade 1 - Section A")
   - **Time slot** (period timing)
6. **Request Changes**: If you need to modify your schedule, click "Request Change" and submit:
   - The change you want (new section, subject, slot, day)
   - A reason for the request
   - The school admin will review and approve/reject

### 7.4 Marking Student Attendance

```
Route: /teacher/attendance
```

1. Navigate to **Attendance** from the sidebar
2. **Select a Section** (only your assigned sections are available)
3. **Select a Date**
4. Mark each student's status:

| Action | How |
|--------|-----|
| Mark as Present | Click the green ✅ button |
| Mark as Absent | Click the red ❌ button |
| Mark as Late | Click the yellow ⏰ button |
| Mark as Half Day | Click the orange 🟡 button |
| Mark as Leave | Click the blue 📋 button |
| Quick Mark All | Use "Mark All Present" or "Mark All Absent" buttons |

5. Add **remarks** for individual students if needed
6. Click **"Save Attendance"**

> 📬 Absent students' guardians are automatically notified.

### 7.5 Entering Grades

```
Route: /teacher/exams/grades
```

1. Navigate to **Grades** from the sidebar
2. Select the **Exam**
3. Select the **Subject** (only subjects you teach)
4. Enter marks for each student:

| Student | Marks (out of max) | Remarks |
|---------|-------------------|---------|
| Ahmed Ali | 85 | Excellent performance |
| Sara Khan | 72 | Good work |
| Hassan Mir | 35 | Needs extra support |

5. Click **"Save"**

### 7.6 Managing Homework

```
Route: /teacher/homework
```

#### Creating Homework

1. Click **"Add Homework"**
2. Fill in the form (Title, Description, Class, Subject, Due Date, Max Marks, Attachment)
3. Click **"Save"**
4. **All students in the class are automatically notified**

#### Grading Submissions

1. Click **"View Submissions"** on a homework assignment
2. See all students' submissions with their files and text
3. Enter **marks** and **feedback** for each submission
4. Click **"Save"**

### 7.7 My Attendance

```
Route: /teacher/staff-attendance
```

View your own attendance history as a staff member:

1. Select a **Month** using the month picker
2. View your daily attendance records:
   - Date, Status, Remarks
3. See a **summary** of Present, Absent, Late, Half Day, Leave counts
4. View your **attendance rate** as a visual ring chart

### 7.8 Leave Requests

```
Route: /teacher/leave
```

#### Submitting a Leave Request

1. Click **"Submit Leave Request"**
2. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| From Date | ✅ | Leave start date |
| To Date | ✅ | Leave end date (must be on or after From Date) |
| Reason | ✅ | Reason for leave (max 1000 characters) |

3. Click **"Submit"**
4. The **school admin receives a notification** to review
5. View your request history and status

#### Tracking Status

- **Pending**: Waiting for admin review
- **Approved**: Leave granted ✅
- **Rejected**: Leave denied ❌ (with admin note)

### 7.9 My Payslips

```
Route: /teacher/payroll
```

View your salary information:

1. See a list of all your payslips, most recent first
2. Each payslip shows:

| Field | Description |
|-------|-------------|
| Period | Month/Year |
| Basic Salary | Base pay |
| House Allowance | Housing allowance |
| Transport Allowance | Transportation allowance |
| Other Allowance | Additional allowances |
| Bonus | Any bonus adjustments |
| Deduction | Deductions |
| Net Salary | Final take-home pay |
| Status | Draft / Paid |

### 7.10 Announcements Feed

```
Route: /teacher/announcements
```

View announcements published for teachers:

1. See all announcements targeted at **"Everyone"** and **"Teachers"**
2. Each announcement shows:
   - **Title**
   - **Published Date**
   - **Audience** badge
   - **Content preview**
3. Paginated — 10 per page
4. Ordered by newest first

### 7.11 Messaging (Teacher ↔ Parent)

```
Route: /teacher/messages
```

Direct messaging with parents of your students:

#### Starting a Conversation

1. Click **"New Message"**
2. Select a **Contact** from the dropdown:
   - Parents of students in your sections
   - Parents of students in your subjects
3. The conversation is linked to the specific student
4. Type your message (max 2000 characters)
5. Click **"Send"**

#### Viewing Messages

1. See all your conversations in the left panel
2. **Unread count** badges on each conversation
3. Click a conversation to view the message thread
4. Messages auto-mark as **read** when opened
5. **Polls every 5 seconds** for new messages (real-time feel)

#### Mobile View

On mobile devices:
- **List View**: See all conversations
- **Chat View**: Tap a conversation to open the full thread
- **Back Button**: Return to the conversation list

---

## 8. Student Portal

> **Access Level**: Strictly self-scoped — view only their own data

Students can view their academic information, check attendance, see grades, view homework, check fees, and read announcements.

### 8.1 Navigation Sidebar

```
OVERVIEW
  └── 📊 Dashboard

ACADEMIC
  ├── 📅 Timetable
  ├── ✅ Attendance
  ├── 📝 Grades
  └── 📖 Homework

FINANCIAL
  └── 💰 Fees

COMMUNICATION
  └── 📢 Announcements
```

### 8.2 Dashboard

| Metric | Description |
|--------|-------------|
| **Class** | Your current class |
| **Section** | Your current section |
| **Admission Number** | Your unique admission number |
| **Fees Due** | Outstanding fees amount |

### 8.3 My Timetable

```
Route: /student/timetable
```

1. View your section's weekly timetable
2. **Desktop**: Full grid view
3. **Mobile**: Per-day cards
4. Each cell shows the subject, teacher, and time

### 8.4 My Attendance

```
Route: /student/attendance
```

1. Select a **Month** using the month picker
2. View your daily attendance records
3. **Summary** at the top:

| Status | Count |
|--------|-------|
| ✅ Present | 18 |
| ❌ Absent | 1 |
| ⏰ Late | 1 |
| 🟡 Half Day | 0 |
| 📋 Leave | 0 |

4. **Attendance Rate**: Displayed as a visual progress ring
5. **History Table**: Date, Status, Remarks for each day
6. Paginated — 15 records per page

### 8.5 My Grades (Report Card)

```
Route: /student/exams
```

1. Select an **Exam** from the dropdown
2. View your **Report Card**:

```
┌─────────────────────────────────────────────┐
│           REPORT CARD                       │
│  Exam: Mid-Term Exam 2025                  │
│  Student: Ahmed Ali                         │
│  Class: Grade 5 - Section A                 │
├─────────────┬───────┬───────┬──────┬───────┤
│ Subject     │ Marks │ Max   │ %    │ Grade │
├─────────────┼───────┼───────┼──────┼───────┤
│ Mathematics │   85  │  100  │  85% │   A   │
│ English     │   72  │  100  │  72% │  B+   │
│ Science     │   90  │  100  │  90% │   A+  │
│ Urdu        │   65  │  100  │  65% │   B   │
│ Social St.  │   78  │  100  │  78% │  B+   │
├─────────────┼───────┼───────┼──────┼───────┤
│ TOTAL       │  390  │  500  │  78% │  B+   │
└─────────────┴───────┴───────┴──────┴───────┘
```

3. View total marks, percentage, and overall grade
4. Pass/Fail indication

### 8.6 Homework

```
Route: /student/homework
```

1. View all homework assigned to your class
2. Each assignment shows:

| Field | Description |
|-------|-------------|
| Title | Assignment name |
| Subject | Related subject |
| Due Date | Deadline |
| Status | Pending / Overdue / Submitted / Graded |
| Max Marks | Maximum marks |
| Attachment | Downloadable file (if attached) |

3. **View Details**: Click to see full description and instructions

#### Submitting Homework

1. Click **"Submit"** on an assignment
2. Enter your **submission text** (max 2000 characters)
3. Optionally **upload a file** (max 10 MB)
4. Click **"Submit"**
5. Your submission status changes to **"Submitted"**

#### After Grading

- When the teacher grades your submission, you'll see:
  - **Marks Obtained** (e.g., 18/20)
  - **Feedback** from the teacher
  - Status changes to **"Graded"**

### 8.7 My Fees

```
Route: /student/fees
```

1. View all your fee invoices
2. See a **summary**:

| Metric | Amount |
|--------|--------|
| Total Billed | $1,200.00 |
| Total Paid | $800.00 |
| Total Due | $400.00 |

3. **Invoice List**:

| Title | Amount | Paid | Balance | Due Date | Status |
|-------|--------|------|---------|----------|--------|
| Monthly Tuition - Sep | $100 | $100 | $0 | Sep 30 | ✅ Paid |
| Monthly Tuition - Oct | $100 | $50 | $50 | Oct 31 | ⚠️ Partial |
| Lab Fee - Fall | $50 | $0 | $50 | Nov 15 | ❌ Unpaid |

4. View **payment history** for each invoice

---

## 9. Parent Portal

> **Access Level**: Scoped to their linked children via the guardian_student relationship

Parents can monitor their children's academic progress, view attendance, check fees, communicate with teachers, submit homework on behalf of their children, and request leave.

### 9.1 Navigation Sidebar

```
OVERVIEW
  ├── 📊 Dashboard
  └── 👨‍👩‍👧 My Children (Coming Soon)

ACADEMIC
  ├── ✅ Attendance
  ├── 📊 Performance
  └── 📖 Homework

FINANCIAL
  └── 💰 Fees

COMMUNICATION
  ├── 🏖️ Leave
  ├── 📢 Announcements
  └── 💬 Messages
```

### 9.2 Dashboard

| Metric | Description |
|--------|-------------|
| **My Children** | Number of children linked to your account |

### 9.3 Child Selector

Most parent pages include a **child selector** — a dropdown at the top to switch between your children.

```
┌──────────────────────────────────────────┐
│  👧 Select Child: [ ▼ Ahmed Ali       ]  │
├──────────────────────────────────────────┤
│                                          │
│  (Content for selected child)            │
│                                          │
└──────────────────────────────────────────┘
```

All data displayed is **filtered to the selected child only**.

### 9.4 Viewing Attendance

```
Route: /parent/attendance
```

1. **Select a child** from the dropdown
2. **Select a Month**
3. View daily attendance records
4. See summary (Present, Absent, Late, etc.)
5. View attendance rate percentage

### 9.5 Viewing Performance (Grades)

```
Route: /parent/exams
```

1. **Select a child** from the dropdown
2. **Select an Exam**
3. View the **Report Card** with per-subject marks, grades, and totals
4. Compare across exams

### 9.6 Homework

```
Route: /parent/homework
```

#### Viewing Homework

1. **Select a child** — view homework for their class
2. See all assignments with status, due dates, and marks

#### Submitting on Behalf of Child

1. Click **"Submit"** on an assignment
2. Enter the **submission text** (parent can write for their child)
3. Optionally **upload a file**
4. Click **"Submit"**

> 💡 **Tip**: This is useful for younger students who may need help with homework submission.

### 9.7 Viewing Fees

```
Route: /parent/fees
```

1. **Select a child** from the dropdown
2. View all fee invoices for that child
3. See payment status, amounts, due dates

### 9.8 Leave Requests

```
Route: /parent/leave
```

#### Submitting Leave for a Child

1. Click **"Submit Leave Request"**
2. **Select the child** for whom you're requesting leave
3. Fill in:

| Field | Required | Description |
|-------|----------|-------------|
| From Date | ✅ | Leave start date |
| To Date | ✅ | Leave end date |
| Reason | ✅ | Reason for leave |

4. Click **"Submit"**
5. The **school admin receives a notification**

#### Tracking Requests

- View all submitted requests with status (Pending / Approved / Rejected)
- See admin notes on rejected requests

### 9.9 Announcements

```
Route: /parent/announcements
```

View announcements targeted at **"Everyone"** and **"Parents"**.

### 10.0 Messaging (Parent ↔ Teacher)

```
Route: /parent/messages
```

Direct communication with your children's teachers:

#### Starting a Conversation

1. Click **"New Message"**
2. Select a **Teacher** from the dropdown:
   - Class teacher of your child
   - Subject teachers of your child
3. The conversation is linked to the specific student
4. Type your message
5. Click **"Send"**

#### Features

- **Conversation list** with unread badges
- **Real-time polling** (every 5 seconds)
- **Auto-mark read** when conversation is opened
- **Mobile responsive**: List view → Chat view

---

## 10. Staff Portal

> **Access Level**: Self-scoped — minimal access for non-teaching staff

Non-teaching staff (accountants, librarians, admin officers, etc.) have limited access — mainly viewing their own attendance and payslips.

### 10.1 Navigation Sidebar

```
OVERVIEW
  ├── 📊 Dashboard
  ├── ✅ My Attendance
  └── 💵 My Pay
```

### 10.2 My Attendance

```
Route: /staff-portal/attendance
```

1. Select a **Month**
2. View your daily attendance records
3. See summary and attendance rate

### 10.3 My Payslips

```
Route: /staff-portal/payroll
```

1. View all your payslips (most recent first)
2. See detailed breakdown of:
   - Basic Salary
   - Allowances (House, Transport, Other)
   - Bonus
   - Deductions
   - Net Salary
3. Status: Draft / Paid

---

## 11. Notifications System

### Overview

The system sends automatic notifications for key events. Notifications are delivered via:

1. **🔔 In-App Bell**: Top-right corner of the dashboard (polls every 30 seconds)
2. **📧 Email**: Sent to your registered email address

### Notification Types

| Notification | Trigger | Recipients |
|-------------|---------|------------|
| **Account Created** | Admin creates a new user | The new user |
| **Announcement Published** | Admin publishes an announcement | Targeted audience |
| **Attendance Alert** | Student marked absent/late | Student's guardians |
| **Fee Invoice Generated** | Invoice created for a student | The student |
| **Fee Payment Received** | Payment recorded on an invoice | The student |
| **Homework Assigned** | Teacher creates homework | All students in the class |
| **Leave Request Submitted** | Teacher/parent submits leave request | School admins |
| **Leave Request Updated** | Admin approves/rejects leave | The requester |
| **Timetable Changed** | Admin modifies the timetable | Affected teachers |

### Using the Notification Bell

```
  🔔 3 ← Unread count badge
  │
  ├── 📢 New Announcement: School Holiday
  ├── ⚠️ Attendance Alert: Ahmed Ali absent today
  ├── 💰 Invoice Generated: Monthly Tuition - Sep
  └── ... (up to 10 shown)
```

1. **Click the bell icon** to open the notification dropdown
2. See your **latest 10 notifications** with:
   - Notification icon
   - Title and preview text
   - Time since received
3. **Click a notification** to navigate to the relevant page
4. Click **"Mark all as read"** to clear all unread notifications
5. Individual notifications are **auto-marked as read** when you click them

---

## 12. User Profile & Settings

### Accessing Your Profile

1. Click your **avatar/name** in the top-right corner
2. Select **"Profile"** from the dropdown

### Profile Settings

| Section | Fields | Actions |
|---------|--------|---------|
| **Profile Info** | Name, Email | Edit and save |
| **Password** | Current Password, New Password, Confirm Password | Change password |
| **Danger Zone** | Delete Account | Permanently delete your account |

### Edit Name/Email

1. Enter your updated name or email
2. Click **"Save"**
3. If you changed your email, you'll need to **verify** the new address

### Change Password

1. Enter your **current password**
2. Enter your **new password** (min 8 characters)
3. **Confirm** your new password
4. Click **"Update Password"**

### Delete Account

> ⚠️ **Warning**: This action is **irreversible**. All your data will be permanently deleted.

1. Scroll to the **Danger Zone**
2. Click **"Delete Account"**
3. Confirm in the dialog
4. You'll be logged out immediately

---

## 13. Public School Website

Each school has a **public-facing website** accessible at:

```
/s/{school-slug}/
```

### Available Pages

| Page | URL | Description |
|------|-----|-------------|
| **Home** | `/s/{slug}/` | Hero section, featured pages, recent blog posts, gallery preview, public announcements |
| **CMS Pages** | `/s/{slug}/pages/{page-slug}` | Static content pages (About, Contact Info, etc.) |
| **Blog** | `/s/{slug}/blog` | Paginated blog posts (9 per page) |
| **Blog Post** | `/s/{slug}/blog/{post-slug}` | Individual blog post |
| **Gallery** | `/s/{slug}/gallery` | Full photo gallery |
| **Contact** | Contact form on any page | Submit messages to the school |

### Contact Form

Visitors can contact the school through the contact form:

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Visitor's name |
| Email | ✅ | Visitor's email |
| Phone | | Phone number |
| Message | ✅ | Their message |

- **Honeypot protection** against spam
- **Rate limiting**: 1 request per minute per IP
- Messages appear in the **CMS Messages** inbox for the school admin

---

## 14. Troubleshooting & FAQ

### Common Issues

#### Q: I can't log in
**A**: 
1. Verify your email and password are correct
2. Check if your email is verified (check inbox for verification link)
3. Try the "Forgot Password" option to reset
4. Contact your school admin to verify your account is active

#### Q: I don't see any sidebar menu items
**A**: 
- Your account may not have a role assigned yet
- Contact your school administrator to assign you the correct role

#### Q: I can't find a feature/module
**A**: 
- Features vary by role — check the sidebar for your role's available modules
- Some features are marked "Coming Soon" in the Super Admin portal

#### Q: I'm not receiving notifications
**A**: 
1. Check your spam/junk email folder
2. Verify your email is correct in Profile settings
3. Check that the notification bell shows unread notifications
4. Ensure your browser allows the site to show notifications

#### Q: My uploaded file failed
**A**: 
- **Images**: Max 2 MB
- **Documents/Homework**: Max 10 MB
- Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF, CSV

#### Q: How do I bulk import students?
**A**: 
1. Go to **Data Tools** (School Admin only)
2. Download the CSV template
3. Fill in student data
4. Upload and preview
5. Commit the import
6. Download the credentials file to share with students

#### Q: Can I undo a deletion?
**A**: 
- **Admissions**: Soft-delete (withdrawn status) — can be recovered
- **Students, Teachers, Staff**: Hard delete — **not recoverable**
- Always ensure you have backups before deleting

#### Q: How do I change my school's theme colors?
**A**: 
1. Go to **School Profile** (School Admin only)
2. Update the Primary and Secondary colors
3. Click **"Save"**
4. Changes take effect **immediately**

#### Q: The timetable grid isn't showing all days
**A**: 
- The system supports **Monday through Saturday**
- Ensure time slots are created under **Timetable > Slots** before building the grid

#### Q: How does the parent-child linking work?
**A**: 
- When a student is enrolled, their parent/guardian is linked via the **guardian_student** relationship
- Parents can only see data for their linked children
- The link is established during student creation or admission enrollment

---

## Appendix A: Quick Reference — URLs by Role

### Super Admin

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard` |
| Schools | `/super-admin/schools` |

### School Admin

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard` |
| Admissions | `/school-admin/admissions` |
| Students | `/school-admin/students` |
| Teachers | `/school-admin/teachers` |
| Staff | `/school-admin/staff` |
| Classes | `/school-admin/classes` |
| Subjects | `/school-admin/subjects` |
| Timetable | `/school-admin/timetable` |
| Timetable Slots | `/school-admin/timetable/slots` |
| Timetable Requests | `/school-admin/timetable/requests` |
| Attendance | `/school-admin/attendance` |
| Staff Attendance | `/school-admin/staff/attendance` |
| Exams | `/school-admin/exams` |
| Grades | `/school-admin/exams/grades` |
| Homework | `/school-admin/homework` |
| Leave Requests | `/school-admin/leave` |
| Fee Structures | `/school-admin/fees/structures` |
| Fee Invoices | `/school-admin/fees/invoices` |
| Salary Structures | `/school-admin/finance/salary-structures` |
| Payroll | `/school-admin/finance/payroll` |
| Announcements | `/school-admin/announcements` |
| CMS Pages | `/school-admin/cms/pages` |
| Blog Posts | `/school-admin/cms/posts` |
| Gallery | `/school-admin/cms/gallery` |
| Contact Messages | `/school-admin/cms/messages` |
| Attendance Report | `/school-admin/reports/attendance` |
| Exam Report | `/school-admin/reports/exams` |
| Fees Report | `/school-admin/reports/fees` |
| Data Import/Export | `/school-admin/data-tools` |
| Campuses | `/school-admin/campuses` |
| School Profile | `/school-admin/settings` |

### Teacher

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard` |
| Timetable | `/teacher/timetable` |
| Attendance | `/teacher/attendance` |
| Grades | `/teacher/exams/grades` |
| Homework | `/teacher/homework` |
| My Attendance | `/teacher/staff-attendance` |
| Leave | `/teacher/leave` |
| My Pay | `/teacher/payroll` |
| Announcements | `/teacher/announcements` |
| Messages | `/teacher/messages` |

### Student

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard` |
| Timetable | `/student/timetable` |
| Attendance | `/student/attendance` |
| Grades | `/student/exams` |
| Homework | `/student/homework` |
| Fees | `/student/fees` |
| Announcements | `/student/announcements` |

### Parent

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard` |
| Attendance | `/parent/attendance` |
| Performance | `/parent/exams` |
| Homework | `/parent/homework` |
| Fees | `/parent/fees` |
| Leave | `/parent/leave` |
| Announcements | `/parent/announcements` |
| Messages | `/parent/messages` |

### Staff

| Feature | URL |
|---------|-----|
| Dashboard | `/dashboard` |
| My Attendance | `/staff-portal/attendance` |
| My Pay | `/staff-portal/payroll` |

---

## Appendix B: Notification Email Previews

### Account Created
```
Subject: Your School Account Has Been Created

Hello {name},

Your account for {school_name} has been created.

Email: {email}
Password: {password}

Please log in and change your password.
```

### Fee Invoice Generated
```
Subject: Fee Invoice: {title}

Hello {student_name},

A new fee invoice has been generated:

Title: {title}
Amount: {amount}
Due Date: {due_date}

Log in to view details and make a payment.
```

### Homework Assigned
```
Subject: New Homework: {title}

Hello {student_name},

New homework has been assigned:

Subject: {subject}
Class: {class}
Due Date: {due_date}

Log in to view details and submit your work.
```

---

## Appendix C: Grade Scale Reference

| Percentage Range | Letter Grade | Description |
|-----------------|-------------|-------------|
| 90% and above | **A+** | Outstanding |
| 80% – 89% | **A** | Excellent |
| 70% – 79% | **B+** | Very Good |
| 60% – 69% | **B** | Good |
| 50% – 59% | **C** | Average |
| 40% – 49% | **D** | Below Average |
| Below 40% | **F** | Failing |

---

## Appendix D: Fee Discount Types

| Type | Description | Example |
|------|-------------|---------|
| **Sibling** | Discount for siblings attending the same school | 10% off for 2nd child |
| **Scholarship** | Academic merit-based discount | 50% off for top scorer |
| **Staff Child** | Child of a school employee | 100% fee waiver |
| **Need-Based** | Financial assistance | 30% off for low-income families |
| **Custom** | Any other arrangement | One-time special discount |

---

## Appendix E: Acceptance Payment Methods

| Method | Description |
|--------|-------------|
| **Cash** | Cash payment at the school office |
| **Bank Transfer** | Direct bank transfer |
| **Cheque** | Payment by cheque |
| **Online** | Online payment gateway |
| **Other** | Any other payment method |

---

*Document Version: 1.0*
*Last Updated: September 10, 2026*
*System Version: School Management System v1.0 (Laravel 11 + Livewire 4)*
