<?php

namespace App\Livewire\SchoolAdmin\Reports;

use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.dashboard')]
class Exams extends Component
{
    public ?int $examId = null;

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $exams = Exam::where('school_id', $schoolId)->orderByDesc('id')->get();

        if (! $this->examId && $exams->isNotEmpty()) {
            $this->examId = $exams->first()->id;
        }

        $studentRows = collect();
        $subjectRows = collect();
        $summary = ['students' => 0, 'classAverage' => null, 'passRate' => null, 'topScorer' => null];

        if ($this->examId) {
            $results = ExamResult::with(['student.user', 'examSubject.subject'])
                ->whereHas('examSubject', fn ($q) => $q->where('exam_id', $this->examId))
                ->get();

            $graded = $results->filter(fn ($result) => $result->marks_obtained !== null);

            $studentRows = $graded->groupBy('student_id')->map(function ($studentResults) {
                $obtained = (float) $studentResults->sum('marks_obtained');
                $max = (float) $studentResults->sum(fn ($result) => $result->examSubject->max_marks);
                $percentage = $max > 0 ? round($obtained / $max * 100, 2) : null;
                $passed = $studentResults->every(fn ($result) => $result->isPass());

                return [
                    'student' => $studentResults->first()->student,
                    'obtained' => $obtained,
                    'max' => $max,
                    'percentage' => $percentage,
                    'passed' => $passed,
                ];
            })->sortByDesc('percentage')->values();

            $subjectRows = $graded->groupBy('examSubject.subject_id')->map(function ($subjectResults) {
                return [
                    'subject' => $subjectResults->first()->examSubject->subject,
                    'average' => round($subjectResults->avg('marks_obtained'), 2),
                    'highest' => $subjectResults->max('marks_obtained'),
                    'lowest' => $subjectResults->min('marks_obtained'),
                    'max' => $subjectResults->first()->examSubject->max_marks,
                ];
            })->sortBy(fn ($row) => $row['subject']->name)->values();

            $summary['students'] = $studentRows->count();
            $summary['classAverage'] = $studentRows->isNotEmpty() ? round($studentRows->avg('percentage'), 1) : null;
            $summary['passRate'] = $studentRows->isNotEmpty() ? round($studentRows->where('passed', true)->count() / $studentRows->count() * 100, 1) : null;
            $summary['topScorer'] = $studentRows->first();
        }

        return view('livewire.school-admin.reports.exams', [
            'exams' => $exams,
            'studentRows' => $studentRows,
            'subjectRows' => $subjectRows,
            'summary' => $summary,
        ]);
    }

    public function export(): StreamedResponse
    {
        $results = ExamResult::with(['student.user', 'examSubject.subject'])
            ->whereHas('examSubject', fn ($q) => $q->where('exam_id', $this->examId))
            ->get();

        $studentRows = $results->filter(fn ($result) => $result->marks_obtained !== null)
            ->groupBy('student_id')
            ->map(function ($studentResults) {
                $obtained = (float) $studentResults->sum('marks_obtained');
                $max = (float) $studentResults->sum(fn ($result) => $result->examSubject->max_marks);

                return [
                    'student' => $studentResults->first()->student,
                    'obtained' => $obtained,
                    'max' => $max,
                    'percentage' => $max > 0 ? round($obtained / $max * 100, 2) : null,
                ];
            })->sortByDesc('percentage')->values();

        $examName = Exam::find($this->examId)?->name ?? 'exam';

        return response()->streamDownload(function () use ($studentRows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Admission No.', 'Marks Obtained', 'Max Marks', 'Percentage']);

            foreach ($studentRows as $row) {
                fputcsv($handle, [
                    $row['student']->user->name,
                    $row['student']->admission_number,
                    $row['obtained'],
                    $row['max'],
                    $row['percentage'],
                ]);
            }

            fclose($handle);
        }, \Illuminate\Support\Str::slug($examName).'-report.csv');
    }
}
