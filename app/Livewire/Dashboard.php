<?php

namespace App\Livewire;

use App\Models\FeeInvoice;
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

        $quickLinks = collect(\App\Support\Navigation::forRole($role))
            ->filter(fn ($item) => $item['route'] && $item['route'] !== 'dashboard' && \Illuminate\Support\Facades\Route::has($item['route']))
            ->values();

        return view('livewire.dashboard', [
            'role' => $role,
            'quickLinks' => $quickLinks,
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
        $due = FeeInvoice::where('school_id', $user->school_id)
            ->selectRaw('SUM(amount - paid_amount) as due')
            ->value('due') ?? 0;

        return [
            'Students' => Student::where('school_id', $user->school_id)->count(),
            'Teachers' => Teacher::where('school_id', $user->school_id)->count(),
            'Classes' => SchoolClass::where('school_id', $user->school_id)->count(),
            'Active Staff' => User::where('school_id', $user->school_id)->where('status', 'active')->count(),
            'Fees Due' => $user->school->currency.' '.number_format($due, 2),
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

        $due = $student
            ? $student->feeInvoices()->selectRaw('SUM(amount - paid_amount) as due')->value('due') ?? 0
            : 0;

        return [
            'Class' => $student?->schoolClass?->name ?? '—',
            'Section' => $student?->section?->name ?? '—',
            'Admission No.' => $student?->admission_number ?? '—',
            'Fees Due' => $user->school->currency.' '.number_format($due, 2),
        ];
    }

    private function parentMetrics(User $user): array
    {
        return [
            'My Children' => $user->guardianProfile?->students()->count() ?? 0,
        ];
    }
}
