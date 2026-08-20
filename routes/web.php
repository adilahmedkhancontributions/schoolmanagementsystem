<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Livewire\Announcements\Feed as AnnouncementsFeed;
use App\Livewire\Attendance\Mark as MarkAttendance;
use App\Livewire\Attendance\MyAttendance;
use App\Livewire\Dashboard;
use App\Livewire\Exams\GradeEntry;
use App\Livewire\Exams\ReportCard;
use App\Livewire\Fees\MyFees;
use App\Livewire\SchoolAdmin\Announcements\Manage as ManageAnnouncements;
use App\Livewire\SchoolAdmin\Classes\Manage as ManageClasses;
use App\Livewire\SchoolAdmin\Cms\Gallery as ManageGallery;
use App\Livewire\SchoolAdmin\Cms\Messages as ManageMessages;
use App\Livewire\SchoolAdmin\Cms\Pages as ManagePages;
use App\Livewire\SchoolAdmin\Cms\Posts as ManagePosts;
use App\Livewire\SchoolAdmin\Exams\Manage as ManageExams;
use App\Livewire\SchoolAdmin\Fees\Invoices as ManageFeeInvoices;
use App\Livewire\SchoolAdmin\Fees\Structures as ManageFeeStructures;
use App\Livewire\SchoolAdmin\Reports\Attendance as AttendanceReport;
use App\Livewire\SchoolAdmin\Reports\Exams as ExamReport;
use App\Livewire\SchoolAdmin\Reports\Fees as FeesReport;
use App\Livewire\SchoolAdmin\Settings\Profile as SchoolProfile;
use App\Livewire\SchoolAdmin\Students\Manage as ManageStudents;
use App\Livewire\SchoolAdmin\Subjects\Manage as ManageSubjects;
use App\Livewire\SchoolAdmin\Teachers\Manage as ManageTeachers;
use App\Livewire\SuperAdmin\Schools\Manage as ManageSchools;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:school_admin'])
    ->prefix('school-admin')
    ->name('school-admin.')
    ->group(function () {
        Route::get('/classes', ManageClasses::class)->name('classes');
        Route::get('/subjects', ManageSubjects::class)->name('subjects');
        Route::get('/teachers', ManageTeachers::class)->name('teachers');
        Route::get('/students', ManageStudents::class)->name('students');
        Route::get('/attendance', MarkAttendance::class)->name('attendance');
        Route::get('/settings', SchoolProfile::class)->name('settings');
        Route::get('/fees/structures', ManageFeeStructures::class)->name('fees.structures');
        Route::get('/fees/invoices', ManageFeeInvoices::class)->name('fees.invoices');
        Route::get('/exams', ManageExams::class)->name('exams');
        Route::get('/exams/grades', GradeEntry::class)->name('exams.grades');
        Route::get('/reports/attendance', AttendanceReport::class)->name('reports.attendance');
        Route::get('/reports/exams', ExamReport::class)->name('reports.exams');
        Route::get('/reports/fees', FeesReport::class)->name('reports.fees');
        Route::get('/announcements', ManageAnnouncements::class)->name('announcements');
        Route::get('/cms/pages', ManagePages::class)->name('cms.pages');
        Route::get('/cms/posts', ManagePosts::class)->name('cms.posts');
        Route::get('/cms/gallery', ManageGallery::class)->name('cms.gallery');
        Route::get('/cms/messages', ManageMessages::class)->name('cms.messages');
    });

Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/schools', ManageSchools::class)->name('schools');
    });

Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/attendance', MarkAttendance::class)->name('attendance');
        Route::get('/exams/grades', GradeEntry::class)->name('exams.grades');
        Route::get('/announcements', AnnouncementsFeed::class)->name('announcements');
    });

Route::middleware(['auth', 'verified', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/attendance', MyAttendance::class)->name('attendance');
        Route::get('/fees', MyFees::class)->name('fees');
        Route::get('/exams', ReportCard::class)->name('exams');
        Route::get('/announcements', AnnouncementsFeed::class)->name('announcements');
    });

Route::middleware(['auth', 'verified', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/attendance', MyAttendance::class)->name('attendance');
        Route::get('/fees', MyFees::class)->name('fees');
        Route::get('/exams', ReportCard::class)->name('exams');
        Route::get('/announcements', AnnouncementsFeed::class)->name('announcements');
    });

Route::prefix('s/{school:slug}')
    ->name('public.site.')
    ->group(function () {
        Route::get('/', [PublicSiteController::class, 'home'])->name('home');
        Route::get('/pages/{slug}', [PublicSiteController::class, 'page'])->name('page');
        Route::get('/blog', [PublicSiteController::class, 'blogIndex'])->name('blog');
        Route::get('/blog/{slug}', [PublicSiteController::class, 'blogShow'])->name('blog.show');
        Route::get('/gallery', [PublicSiteController::class, 'gallery'])->name('gallery');
        Route::post('/contact', [PublicSiteController::class, 'contact'])->middleware('throttle:5,1')->name('contact');
    });

require __DIR__.'/auth.php';
