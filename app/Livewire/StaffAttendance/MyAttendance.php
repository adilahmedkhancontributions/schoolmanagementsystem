<?php

namespace App\Livewire\StaffAttendance;

use App\Models\StaffAttendance;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class MyAttendance extends Component
{
    use WithPagination;

    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function render(): View
    {
        $userId = auth()->id();

        $start = Carbon::parse($this->month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = StaffAttendance::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->orderByDesc('date')
            ->paginate(15);

        $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'half_day' => 0, 'leave' => 0];

        $counts = StaffAttendance::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        foreach ($counts as $status => $total) {
            $summary[$status] = $total;
        }

        return view('livewire.staff-attendance.my-attendance', [
            'records' => $records,
            'summary' => $summary,
        ]);
    }
}
