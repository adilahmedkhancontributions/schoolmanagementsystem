<?php

namespace App\Livewire\Timetable;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TimetableChangeRequest;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class AllTimetables extends Component
{
    public ?int $schoolClassId = null;

    public ?int $sectionId = null;

    public bool $showRequestModal = false;

    public ?int $requestEntryId = null;

    public string $requestedSubjectId = '';

    public string $requestedSectionId = '';

    public string $requestedSlotId = '';

    public string $requestedDay = '';

    public string $reason = '';

    public function mount(): void
    {
        $this->schoolClassId = $this->availableClasses()->orderBy('sort_order')->value('id');
        $this->sectionId = $this->schoolClassId
            ? $this->availableSections()->where('school_class_id', $this->schoolClassId)->orderBy('name')->value('id')
            : null;
    }

    public function updatedSchoolClassId(): void
    {
        $this->sectionId = $this->availableSections()->where('school_class_id', $this->schoolClassId)->orderBy('name')->value('id');
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;
        $teacher = auth()->user()->teacher;

        $section = $this->sectionId ? $this->availableSections()->find($this->sectionId) : null;
        $this->sectionId = $section?->id;

        $periods = TimetableSlot::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('start_time')->get();

        $grid = [];
        if ($section) {
            $entries = TimetableEntry::where('section_id', $section->id)->with(['subject', 'teacher.user'])->get();
            foreach ($entries as $entry) {
                $grid[$entry->timetable_slot_id][$entry->day_of_week] = $entry;
            }
        }

        $myEntries = collect();
        if ($teacher) {
            $myEntries = TimetableEntry::where('teacher_id', $teacher->id)
                ->with(['subject', 'section.schoolClass', 'slot'])
                ->get();
        }

        $myRequests = $teacher
            ? TimetableChangeRequest::where('teacher_id', $teacher->id)
                ->with(['currentSection.schoolClass', 'currentSubject', 'currentSlot', 'requestedSection.schoolClass', 'requestedSubject', 'requestedSlot'])
                ->latest()
                ->get()
            : collect();

        return view('livewire.timetable.all-timetables', [
            'classes' => $this->availableClasses()->orderBy('sort_order')->get(),
            'sections' => $this->schoolClassId
                ? $this->availableSections()->where('school_class_id', $this->schoolClassId)->orderBy('name')->get()
                : collect(),
            'periods' => $periods,
            'grid' => $grid,
            'days' => TimetableEntry::DAYS,
            'myEntries' => $myEntries,
            'myRequests' => $myRequests,
            'allSections' => $this->availableSections()->with('schoolClass')->orderBy('name')->get(),
            'allSubjects' => Subject::where('school_id', $schoolId)->orderBy('name')->get(),
        ]);
    }

    public function openRequestModal(int $entryId): void
    {
        $teacher = auth()->user()->teacher;
        $entry = TimetableEntry::where('teacher_id', $teacher?->id)->findOrFail($entryId);

        $this->requestEntryId = $entry->id;
        $this->requestedSubjectId = '';
        $this->requestedSectionId = '';
        $this->requestedSlotId = '';
        $this->requestedDay = '';
        $this->reason = '';
        $this->showRequestModal = true;
    }

    public function closeRequestModal(): void
    {
        $this->showRequestModal = false;
        $this->requestEntryId = null;
    }

    public function submitRequest(): void
    {
        $this->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $teacher = auth()->user()->teacher;
        $entry = TimetableEntry::where('teacher_id', $teacher?->id)->findOrFail($this->requestEntryId);

        if (
            $this->requestedSubjectId === '' &&
            $this->requestedSectionId === '' &&
            $this->requestedSlotId === '' &&
            $this->requestedDay === ''
        ) {
            $this->addError('reason', 'Please specify at least one change you are requesting.');

            return;
        }

        TimetableChangeRequest::create([
            'school_id' => $teacher->school_id,
            'teacher_id' => $teacher->id,
            'timetable_entry_id' => $entry->id,
            'current_section_id' => $entry->section_id,
            'current_subject_id' => $entry->subject_id,
            'current_timetable_slot_id' => $entry->timetable_slot_id,
            'current_day_of_week' => $entry->day_of_week,
            'requested_section_id' => $this->requestedSectionId ?: null,
            'requested_subject_id' => $this->requestedSubjectId ?: null,
            'requested_timetable_slot_id' => $this->requestedSlotId ?: null,
            'requested_day_of_week' => $this->requestedDay ?: null,
            'reason' => $this->reason,
            'status' => TimetableChangeRequest::STATUS_PENDING,
        ]);

        $this->closeRequestModal();
        session()->flash('message', 'Your timetable change request has been sent to the school admin.');
    }

    private function availableClasses()
    {
        return SchoolClass::where('school_id', auth()->user()->school_id);
    }

    private function availableSections()
    {
        return Section::whereHas('schoolClass', fn ($q) => $q->where('school_id', auth()->user()->school_id));
    }
}
