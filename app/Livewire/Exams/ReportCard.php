<?php

namespace App\Livewire\Exams;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class ReportCard extends Component
{
    public ?int $studentId = null;

    public ?int $examId = null;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user->hasRole('parent')) {
            $this->studentId = $user->guardianProfile?->students()->first()?->id;
        } elseif ($user->hasRole('student')) {
            $this->studentId = $user->student?->id;
        }
    }

    public function updatedStudentId(): void
    {
        $this->examId = null;
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

        $exams = collect();
        $results = collect();
        $totals = ['obtained' => 0.0, 'max' => 0.0, 'percentage' => null, 'grade' => null];

        if ($this->studentId) {
            $student = Student::findOrFail($this->studentId);

            $exams = Exam::where('school_class_id', $student->school_class_id)->orderByDesc('id')->get();

            if (! $exams->contains('id', $this->examId)) {
                $this->examId = $exams->first()?->id;
            }

            if ($this->examId) {
                $results = ExamResult::with('examSubject.subject')
                    ->where('student_id', $student->id)
                    ->whereHas('examSubject', fn ($q) => $q->where('exam_id', $this->examId))
                    ->get()
                    ->sortBy(fn ($result) => $result->examSubject->subject->name);

                $graded = $results->filter(fn ($result) => $result->marks_obtained !== null);
                $totals['obtained'] = (float) $graded->sum('marks_obtained');
                $totals['max'] = (float) $graded->sum(fn ($result) => $result->examSubject->max_marks);

                if ($totals['max'] > 0) {
                    $totals['percentage'] = round(($totals['obtained'] / $totals['max']) * 100, 2);
                    $totals['grade'] = match (true) {
                        $totals['percentage'] >= 90 => 'A+',
                        $totals['percentage'] >= 80 => 'A',
                        $totals['percentage'] >= 70 => 'B+',
                        $totals['percentage'] >= 60 => 'B',
                        $totals['percentage'] >= 50 => 'C',
                        $totals['percentage'] >= 40 => 'D',
                        default => 'F',
                    };
                }
            }
        }

        return view('livewire.exams.report-card', [
            'children' => $children,
            'exams' => $exams,
            'results' => $results,
            'totals' => $totals,
        ]);
    }
}
