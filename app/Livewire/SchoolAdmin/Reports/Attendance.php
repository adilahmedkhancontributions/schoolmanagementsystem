<?php

namespace App\Livewire\SchoolAdmin\Reports;

use App\Models\Attendance as AttendanceModel;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.dashboard')]
class Attendance extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public ?int $classId = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;
        $rows = $this->buildRows($schoolId);

        $totalDays = $rows->sum('total');
        $totalPresent = $rows->sum('present');

        return view('livewire.school-admin.reports.attendance', [
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
            'rows' => $rows,
            'summary' => [
                'students' => $rows->count(),
                'averageRate' => $totalDays > 0 ? round($totalPresent / $totalDays * 100, 1) : null,
                'totalAbsent' => $rows->sum('absent'),
            ],
        ]);
    }

    private function buildRows(int $schoolId)
    {
        $records = AttendanceModel::with('student.user')
            ->where('school_id', $schoolId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->when($this->classId, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('school_class_id', $this->classId)))
            ->get();

        return $records->groupBy('student_id')->map(function ($studentRecords) {
            $counts = $studentRecords->countBy('status');
            $total = $studentRecords->count();
            $present = $counts->get('present', 0);

            return [
                'student' => $studentRecords->first()->student,
                'present' => $present,
                'absent' => $counts->get('absent', 0),
                'late' => $counts->get('late', 0),
                'half_day' => $counts->get('half_day', 0),
                'leave' => $counts->get('leave', 0),
                'total' => $total,
                'rate' => $total > 0 ? round($present / $total * 100, 1) : null,
            ];
        })->sortBy(fn ($row) => $row['student']->user->name)->values();
    }

    public function export(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $rows = $this->buildRows($schoolId);

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Admission No.', 'Present', 'Absent', 'Late', 'Half Day', 'Leave', 'Total Days', 'Attendance Rate (%)']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['student']->user->name,
                    $row['student']->admission_number,
                    $row['present'],
                    $row['absent'],
                    $row['late'],
                    $row['half_day'],
                    $row['leave'],
                    $row['total'],
                    $row['rate'],
                ]);
            }

            fclose($handle);
        }, 'attendance-report-'.$this->startDate.'-to-'.$this->endDate.'.csv');
    }
}
