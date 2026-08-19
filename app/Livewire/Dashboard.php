<?php

namespace App\Livewire;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Dashboard extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        return view('livewire.dashboard', [
            'role' => $role,
            'metrics' => match ($role) {
                'super_admin' => $this->superAdminMetrics(),
                'school_admin' => $this->schoolAdminMetrics($user),
                'teacher' => $this->teacherMetrics($user),
                'student' => $this->studentMetrics($user),
                'parent' => $this->parentMetrics($user),
                default => [],
            },
        ]);
    }

    private function superAdminMetrics(): array
    {
        return [
            'Schools' => School::count(),
            'Total Users' => User::count(),
            'School Admins' => User::role('school_admin')->count(),
            'Teachers' => User::role('teacher')->count(),
        ];
    }

    private function schoolAdminMetrics(User $user): array
    {
        return [
            'Students' => Student::where('school_id', $user->school_id)->count(),
            'Teachers' => Teacher::where('school_id', $user->school_id)->count(),
            'Classes' => SchoolClass::where('school_id', $user->school_id)->count(),
            'Active Staff' => User::where('school_id', $user->school_id)->where('status', 'active')->count(),
        ];
    }

    private function teacherMetrics(User $user): array
    {
        $teacher = $user->teacher;

        return [
            'My Sections' => $teacher?->sections()->count() ?? 0,
            'My Subjects' => $teacher?->subjects()->count() ?? 0,
        ];
    }

    private function studentMetrics(User $user): array
    {
        $student = $user->student;

        return [
            'Class' => $student?->schoolClass?->name ?? '—',
            'Section' => $student?->section?->name ?? '—',
            'Admission No.' => $student?->admission_number ?? '—',
        ];
    }

    private function parentMetrics(User $user): array
    {
        return [
            'My Children' => $user->guardianProfile?->students()->count() ?? 0,
        ];
    }
}
