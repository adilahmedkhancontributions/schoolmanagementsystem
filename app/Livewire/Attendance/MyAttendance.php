<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class MyAttendance extends Component
{
    use WithPagination;

    public ?int $studentId = null;

    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');

        $user = auth()->user();
        if ($user->hasRole('parent')) {
            $this->studentId = $user->guardianProfile?->students()->first()?->id;
        } elseif ($user->hasRole('student')) {
            $this->studentId = $user->student?->id;
        }
    }

    public function render(): View
    {
        $user = auth()->user();

        $children = $user->hasRole('parent')
            ? $user->guardianProfile?->students()->with('user')->get() ?? collect()
            : collect();

        $allowedIds = $user->hasRole('parent')
            ? $children->pluck('id')
            : collect([$user->student?->id]);

        if (! $allowedIds->contains($this->studentId)) {
            $this->studentId = null;
        }

        $start = Carbon::parse($this->month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = collect();
        $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'half_day' => 0, 'leave' => 0];

        if ($this->studentId) {
            $student = Student::findOrFail($this->studentId);

            $records = $student->attendances()
                ->whereBetween('date', [$start, $end])
                ->orderByDesc('date')
                ->paginate(15);

            $counts = $student->attendances()
                ->whereBetween('date', [$start, $end])
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            foreach ($counts as $status => $total) {
                $summary[$status] = $total;
            }
        }

        return view('livewire.attendance.my-attendance', [
            'children' => $children,
            'records' => $records,
            'summary' => $summary,
        ]);
    }
}
