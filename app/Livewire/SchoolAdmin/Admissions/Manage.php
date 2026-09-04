<?php

namespace App\Livewire\SchoolAdmin\Admissions;

use App\Models\Admission;
use App\Models\Document;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AccountCreated;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    public ?string $filterStatus = null;

    public ?int $filterClassId = null;

    public bool $showModal = false;

    public bool $showStageModal = false;

    public bool $showDocumentsModal = false;

    public ?int $admissionId = null;

    public ?int $docAdmissionId = null;

    public $documents = [];

    public ?string $documentTitle = null;

    public $documentFile;

    public ?string $generatedPassword = null;

    // Application form
    public string $applicantName = '';

    public string $fatherName = '';

    public string $phone = '';

    public string $email = '';

    public string $gender = 'male';

    public string $dateOfBirth = '';

    public string $address = '';

    public ?int $schoolClassId = null;

    public string $source = '';

    // Stage form
    public string $interviewDate = '';

    public string $interviewNotes = '';

    public string $testDate = '';

    public string $testScore = '';

    public string $testNotes = '';

    public string $decisionNotes = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClassId(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $admissions = Admission::with(['schoolClass', 'enrolledStudent.user'])
            ->where('school_id', $schoolId)
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterClassId, fn ($q) => $q->where('school_class_id', $this->filterClassId))
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('applicant_name', 'like', "%{$search}%")
                        ->orWhere('father_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.school-admin.admissions.manage', [
            'admissions' => $admissions,
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
            'statuses' => [
                Admission::STATUS_INQUIRY => 'Inquiry',
                Admission::STATUS_INTERVIEW_SCHEDULED => 'Interview Scheduled',
                Admission::STATUS_TEST_SCHEDULED => 'Test Scheduled',
                Admission::STATUS_OFFERED => 'Offered',
                Admission::STATUS_ENROLLED => 'Enrolled',
                Admission::STATUS_REJECTED => 'Rejected',
                Admission::STATUS_WITHDRAWN => 'Withdrawn',
            ],
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $admission = Admission::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->admissionId = $admission->id;
        $this->applicantName = $admission->applicant_name;
        $this->fatherName = (string) $admission->father_name;
        $this->phone = (string) $admission->phone;
        $this->email = (string) $admission->email;
        $this->gender = (string) ($admission->gender ?? 'male');
        $this->dateOfBirth = optional($admission->date_of_birth)->format('Y-m-d') ?? '';
        $this->address = (string) $admission->address;
        $this->schoolClassId = $admission->school_class_id;
        $this->source = (string) $admission->source;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'applicantName' => 'required|string|max:255',
            'fatherName' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:male,female,other',
            'dateOfBirth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'schoolClassId' => 'nullable|exists:school_classes,id',
            'source' => 'nullable|string|max:100',
        ]);

        $schoolId = auth()->user()->school_id;

        $data = [
            'applicant_name' => $validated['applicantName'],
            'father_name' => $validated['fatherName'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'email' => $validated['email'] ?: null,
            'gender' => $validated['gender'] ?: null,
            'date_of_birth' => $validated['dateOfBirth'] ?: null,
            'address' => $validated['address'] ?: null,
            'school_class_id' => $validated['schoolClassId'] ?: null,
            'source' => $validated['source'] ?: null,
        ];

        if ($this->admissionId) {
            Admission::where('school_id', $schoolId)->findOrFail($this->admissionId)->update($data);
        } else {
            Admission::create($data + [
                'school_id' => $schoolId,
                'status' => Admission::STATUS_INQUIRY,
                'created_by' => auth()->id(),
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openStage(int $id): void
    {
        $admission = Admission::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->admissionId = $admission->id;
        $this->interviewDate = optional($admission->interview_date)->format('Y-m-d\TH:i') ?? '';
        $this->interviewNotes = (string) $admission->interview_notes;
        $this->testDate = optional($admission->test_date)->format('Y-m-d\TH:i') ?? '';
        $this->testScore = (string) $admission->test_score;
        $this->testNotes = (string) $admission->test_notes;
        $this->decisionNotes = (string) $admission->decision_notes;
        $this->showStageModal = true;
    }

    public function closeStageModal(): void
    {
        $this->showStageModal = false;
        $this->resetStageForm();
    }

    private function currentAdmission(): Admission
    {
        return Admission::where('school_id', auth()->user()->school_id)->findOrFail($this->admissionId);
    }

    public function scheduleInterview(): void
    {
        $this->validate([
            'interviewDate' => 'required|date',
            'interviewNotes' => 'nullable|string|max:1000',
        ]);

        $this->currentAdmission()->update([
            'interview_date' => $this->interviewDate,
            'interview_notes' => $this->interviewNotes ?: null,
            'status' => Admission::STATUS_INTERVIEW_SCHEDULED,
        ]);

        $this->closeStageModal();
    }

    public function scheduleTest(): void
    {
        $this->validate([
            'testDate' => 'required|date',
        ]);

        $this->currentAdmission()->update([
            'test_date' => $this->testDate,
            'status' => Admission::STATUS_TEST_SCHEDULED,
        ]);

        $this->closeStageModal();
    }

    public function recordTestResult(): void
    {
        $this->validate([
            'testScore' => 'nullable|numeric|min:0|max:100',
            'testNotes' => 'nullable|string|max:1000',
        ]);

        $this->currentAdmission()->update([
            'test_score' => $this->testScore !== '' ? $this->testScore : null,
            'test_notes' => $this->testNotes ?: null,
        ]);

        $this->closeStageModal();
    }

    public function makeOffer(): void
    {
        $this->currentAdmission()->update([
            'status' => Admission::STATUS_OFFERED,
            'decision_notes' => $this->decisionNotes ?: null,
        ]);

        $this->closeStageModal();
    }

    public function reject(): void
    {
        $this->validate([
            'decisionNotes' => 'required|string|max:1000',
        ]);

        $this->currentAdmission()->update([
            'status' => Admission::STATUS_REJECTED,
            'decision_notes' => $this->decisionNotes,
        ]);

        $this->closeStageModal();
    }

    public function withdraw(int $id): void
    {
        Admission::where('school_id', auth()->user()->school_id)->findOrFail($id)->update([
            'status' => Admission::STATUS_WITHDRAWN,
        ]);
    }

    public function enroll(int $id): void
    {
        $admission = Admission::where('school_id', auth()->user()->school_id)->findOrFail($id);

        if ($admission->status !== Admission::STATUS_OFFERED) {
            return;
        }

        if (! $admission->email) {
            $this->addError('enroll', 'An email address is required before enrolling this applicant.');

            return;
        }

        $schoolId = auth()->user()->school_id;
        $password = Str::password(12);

        $user = User::create([
            'school_id' => $schoolId,
            'name' => $admission->applicant_name,
            'email' => $admission->email,
            'phone' => $admission->phone,
            'password' => $password,
            'status' => 'active',
        ]);
        $user->assignRole('student');

        $student = Student::create([
            'user_id' => $user->id,
            'school_id' => $schoolId,
            'school_class_id' => $admission->school_class_id,
            'admission_number' => 'ADM-'.now()->format('y').'-'.str_pad((string) (Student::where('school_id', $schoolId)->count() + 1), 4, '0', STR_PAD_LEFT),
            'admission_date' => now(),
            'gender' => $admission->gender,
            'date_of_birth' => $admission->date_of_birth,
            'address' => $admission->address,
            'status' => 'active',
        ]);

        $admission->update([
            'status' => Admission::STATUS_ENROLLED,
            'enrolled_student_id' => $student->id,
        ]);

        Notification::send($user, new AccountCreated('Student', $admission->email, $password));

        $this->generatedPassword = $password;
    }

    public function dismissGeneratedPassword(): void
    {
        $this->generatedPassword = null;
    }

    public function openDocuments(int $id): void
    {
        Admission::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $this->docAdmissionId = $id;
        $this->loadDocuments();
        $this->showDocumentsModal = true;
    }

    public function loadDocuments(): void
    {
        $this->documents = Document::where('school_id', auth()->user()->school_id)
            ->where('documentable_type', Admission::class)
            ->where('documentable_id', $this->docAdmissionId)
            ->get();
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'documentTitle' => 'required|string|max:255',
            'documentFile' => 'required|file|max:10240',
        ]);

        Admission::where('school_id', auth()->user()->school_id)->findOrFail($this->docAdmissionId);

        $path = $this->documentFile->store('documents', 'public');

        Document::create([
            'school_id' => auth()->user()->school_id,
            'documentable_type' => Admission::class,
            'documentable_id' => $this->docAdmissionId,
            'title' => $this->documentTitle,
            'file_path' => $path,
            'file_type' => $this->documentFile->getClientOriginalExtension(),
        ]);

        $this->reset(['documentTitle', 'documentFile']);
        $this->loadDocuments();
    }

    public function deleteDocument(int $id): void
    {
        $doc = Document::where('school_id', auth()->user()->school_id)->findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        $this->loadDocuments();
    }

    public function closeDocumentsModal(): void
    {
        $this->showDocumentsModal = false;
        $this->reset(['docAdmissionId', 'documents', 'documentTitle', 'documentFile']);
    }

    private function resetForm(): void
    {
        $this->reset(['admissionId', 'applicantName', 'fatherName', 'phone', 'email', 'dateOfBirth', 'address', 'schoolClassId', 'source']);
        $this->gender = 'male';
        $this->resetErrorBag();
    }

    private function resetStageForm(): void
    {
        $this->reset(['admissionId', 'interviewDate', 'interviewNotes', 'testDate', 'testScore', 'testNotes', 'decisionNotes']);
        $this->resetErrorBag();
    }
}
