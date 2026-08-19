<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\SchoolAdmin\Classes\Manage as ManageClasses;
use App\Livewire\SchoolAdmin\Students\Manage as ManageStudents;
use App\Livewire\SchoolAdmin\Subjects\Manage as ManageSubjects;
use App\Livewire\SchoolAdmin\Teachers\Manage as ManageTeachers;
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
    });

require __DIR__.'/auth.php';
