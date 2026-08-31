<?php

namespace App\Livewire\SchoolAdmin\DataTools;

use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithFileUploads;

    public $importFile = null;

    public array $importRows = [];

    public int $importValidCount = 0;

    public int $importErrorCount = 0;

    public bool $importCommitted = false;

    public array $importedCredentials = [];

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        return view('livewire.school-admin.data-tools.manage', [
            'counts' => [
                'students' => Student::where('school_id', $schoolId)->count(),
                'teachers' => Teacher::where('school_id', $schoolId)->count(),
                'staff' => Staff::where('school_id', $schoolId)->count(),
                'invoices' => FeeInvoice::where('school_id', $schoolId)->count(),
            ],
        ]);
    }

    public function updatedImportFile(): void
    {
        $this->importCommitted = false;
        $this->importedCredentials = [];
        $this->parsePreview();
    }

    public function downloadStudentTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'phone', 'admission_number', 'class_name', 'section_name', 'gender', 'date_of_birth']);
            fputcsv($handle, ['Ali Raza', 'ali.raza@example.com', '03001234567', 'ADM-1001', 'Grade 5', 'A', 'male', '2014-05-12']);
            fclose($handle);
        }, 'student-import-template.csv');
    }

    private function parsePreview(): void
    {
        $this->importRows = [];
        $this->importValidCount = 0;
        $this->importErrorCount = 0;

        if (! $this->importFile) {
            return;
        }

        $schoolId = auth()->user()->school_id;
        $handle = fopen($this->importFile->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);
            return;
        }

        $header = array_map(fn ($h) => Str::snake(trim((string) $h)), $header);
        $seenEmails = [];
        $seenAdmissionNumbers = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, array_pad($row, count($header), null));
            $errors = [];

            $name = trim((string) ($data['name'] ?? ''));
            $email = trim((string) ($data['email'] ?? ''));
            $phone = trim((string) ($data['phone'] ?? ''));
            $admissionNumber = trim((string) ($data['admission_number'] ?? ''));
            $className = trim((string) ($data['class_name'] ?? ''));
            $sectionName = trim((string) ($data['section_name'] ?? ''));
            $gender = strtolower(trim((string) ($data['gender'] ?? '')));
            $dateOfBirth = trim((string) ($data['date_of_birth'] ?? ''));

            if ($name === '') {
                $errors[] = 'Name is required.';
            }

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            } elseif (in_array($email, $seenEmails, true)) {
                $errors[] = 'Duplicate email in file.';
            } elseif (User::where('email', $email)->exists()) {
                $errors[] = 'Email already in use.';
            }

            if ($admissionNumber === '') {
                $errors[] = 'Admission number is required.';
            } elseif (in_array($admissionNumber, $seenAdmissionNumbers, true)) {
                $errors[] = 'Duplicate admission number in file.';
            } elseif (Student::where('school_id', $schoolId)->where('admission_number', $admissionNumber)->exists()) {
                $errors[] = 'Admission number already in use.';
            }

            $schoolClassId = null;
            if ($className !== '') {
                $schoolClass = SchoolClass::where('school_id', $schoolId)->where('name', $className)->first();
                if (! $schoolClass) {
                    $errors[] = "Class \"{$className}\" not found.";
                } else {
                    $schoolClassId = $schoolClass->id;
                }
            }

            $sectionId = null;
            if ($sectionName !== '') {
                if (! $schoolClassId) {
                    $errors[] = 'Section given without a valid class.';
                } else {
                    $section = Section::where('school_class_id', $schoolClassId)->where('name', $sectionName)->first();
                    if (! $section) {
                        $errors[] = "Section \"{$sectionName}\" not found in class \"{$className}\".";
                    } else {
                        $sectionId = $section->id;
                    }
                }
            }

            if ($gender !== '' && ! in_array($gender, ['male', 'female', 'other'], true)) {
                $errors[] = 'Gender must be male, female or other.';
            }

            if ($dateOfBirth !== '' && ! strtotime($dateOfBirth)) {
                $errors[] = 'Date of birth is not a valid date.';
            }

            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $seenEmails[] = $email;
            }
            if ($admissionNumber !== '') {
                $seenAdmissionNumbers[] = $admissionNumber;
            }

            $this->importRows[] = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'admission_number' => $admissionNumber,
                'school_class_id' => $schoolClassId,
                'section_id' => $sectionId,
                'gender' => $gender !== '' ? $gender : null,
                'date_of_birth' => $dateOfBirth !== '' ? $dateOfBirth : null,
                'errors' => $errors,
            ];

            if (empty($errors)) {
                $this->importValidCount++;
            } else {
                $this->importErrorCount++;
            }
        }

        fclose($handle);
    }

    public function commitImport(): void
    {
        $schoolId = auth()->user()->school_id;
        $credentials = [];

        DB::transaction(function () use ($schoolId, &$credentials) {
            foreach ($this->importRows as $row) {
                if (! empty($row['errors'])) {
                    continue;
                }

                $password = Str::password(12);

                $user = User::create([
                    'school_id' => $schoolId,
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?: null,
                    'password' => $password,
                    'status' => 'active',
                ]);
                $user->assignRole('student');

                Student::create([
                    'user_id' => $user->id,
                    'school_id' => $schoolId,
                    'school_class_id' => $row['school_class_id'],
                    'section_id' => $row['section_id'],
                    'admission_number' => $row['admission_number'],
                    'admission_date' => now(),
                    'gender' => $row['gender'],
                    'date_of_birth' => $row['date_of_birth'],
                    'status' => 'active',
                ]);

                $credentials[] = ['name' => $row['name'], 'email' => $row['email'], 'password' => $password];
            }
        });

        $this->importedCredentials = $credentials;
        $this->importCommitted = true;
        $this->importRows = [];
        $this->importFile = null;
    }

    public function downloadCredentials(): StreamedResponse
    {
        $credentials = $this->importedCredentials;

        return response()->streamDownload(function () use ($credentials) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Temporary Password']);
            foreach ($credentials as $row) {
                fputcsv($handle, [$row['name'], $row['email'], $row['password']]);
            }
            fclose($handle);
        }, 'new-student-credentials.csv');
    }

    public function cancelImport(): void
    {
        $this->reset(['importFile', 'importRows', 'importValidCount', 'importErrorCount', 'importCommitted', 'importedCredentials']);
    }

    public function exportStudents(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $students = Student::with(['user', 'schoolClass', 'section'])->where('school_id', $schoolId)->orderBy('id')->get();

        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Admission Number', 'Class', 'Section', 'Gender', 'Date of Birth', 'Status']);
            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->user->name,
                    $student->user->email,
                    $student->user->phone,
                    $student->admission_number,
                    $student->schoolClass?->name,
                    $student->section?->name,
                    $student->gender,
                    optional($student->date_of_birth)->format('Y-m-d'),
                    $student->status,
                ]);
            }
            fclose($handle);
        }, 'students-export.csv');
    }

    public function exportTeachers(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $teachers = Teacher::with('user')->where('school_id', $schoolId)->orderBy('id')->get();

        return response()->streamDownload(function () use ($teachers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Employee ID', 'Qualification', 'Specialization', 'Employment Type', 'Joining Date']);
            foreach ($teachers as $teacher) {
                fputcsv($handle, [
                    $teacher->user->name,
                    $teacher->user->email,
                    $teacher->user->phone,
                    $teacher->employee_id,
                    $teacher->qualification,
                    $teacher->specialization,
                    $teacher->employment_type,
                    optional($teacher->joining_date)->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        }, 'teachers-export.csv');
    }

    public function exportStaff(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $staff = Staff::with('user')->where('school_id', $schoolId)->orderBy('id')->get();

        return response()->streamDownload(function () use ($staff) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Employee ID', 'Designation', 'Department', 'Employment Type', 'Joining Date']);
            foreach ($staff as $member) {
                fputcsv($handle, [
                    $member->user->name,
                    $member->user->email,
                    $member->user->phone,
                    $member->employee_id,
                    $member->designation,
                    $member->department,
                    $member->employment_type,
                    optional($member->joining_date)->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        }, 'staff-export.csv');
    }

    public function exportFeeInvoices(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $invoices = FeeInvoice::with('student.user', 'student.schoolClass')->where('school_id', $schoolId)->orderBy('id')->get();

        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Class', 'Title', 'Amount', 'Paid', 'Balance', 'Status', 'Due Date']);
            foreach ($invoices as $invoice) {
                fputcsv($handle, [
                    $invoice->student->user->name,
                    $invoice->student->schoolClass?->name,
                    $invoice->title,
                    $invoice->amount,
                    $invoice->paid_amount,
                    $invoice->balance(),
                    $invoice->status,
                    optional($invoice->due_date)->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        }, 'fee-invoices-export.csv');
    }

    public function exportAttendance(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $records = Attendance::with('student.user')
            ->whereHas('student', fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('date')
            ->get();

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Date', 'Status']);
            foreach ($records as $record) {
                fputcsv($handle, [$record->student->user->name, $record->date->format('Y-m-d'), $record->status]);
            }
            fclose($handle);
        }, 'attendance-export.csv');
    }

    public function exportExamResults(): StreamedResponse
    {
        $schoolId = auth()->user()->school_id;
        $results = ExamResult::with('student.user', 'examSubject.subject', 'examSubject.exam')
            ->whereHas('student', fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('id')
            ->get();

        return response()->streamDownload(function () use ($results) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Exam', 'Subject', 'Marks Obtained', 'Max Marks', 'Pass Marks', 'Remarks']);
            foreach ($results as $result) {
                fputcsv($handle, [
                    $result->student->user->name,
                    $result->examSubject->exam->name,
                    $result->examSubject->subject->name,
                    $result->marks_obtained,
                    $result->examSubject->max_marks,
                    $result->examSubject->pass_marks,
                    $result->remarks,
                ]);
            }
            fclose($handle);
        }, 'exam-results-export.csv');
    }
}
