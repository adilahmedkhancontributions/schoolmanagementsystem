<?php

namespace App\Livewire\SchoolAdmin\Timetable;

use App\Models\Section;
use App\Models\Subject;
use App\Models\TimetableChangeRequest;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use App\Support\TimetableNotifier;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Requests extends Component
{
    public string $statusFilter = 'pending';

    public ?int $reviewingId = null;

    public string $adminNote = '';

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $query = TimetableChangeRequest::where('school_id', $schoolId)
            ->with([
                'teacher.user',
                'currentSection.schoolClass',
                'currentSubject',
                'currentSlot',
                'requestedSection.schoolClass',
                'requestedSubject',
                'requestedSlot',
            ])
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.school-admin.timetable.requests', [
            'requests' => $query->get(),
        ]);
    }

    public function approve(int $requestId): void
    {
        $schoolId = auth()->user()->school_id;
        $request = TimetableChangeRequest::where('school_id', $schoolId)->findOrFail($requestId);

        if ($request->status !== TimetableChangeRequest::STATUS_PENDING) {
            return;
        }

        $entry = $request->timetable_entry_id
            ? TimetableEntry::with(['section.schoolClass', 'subject', 'slot', 'teacher.user'])->find($request->timetable_entry_id)
            : null;

        if (! $entry) {
            $this->addError('reviewingId', 'The original timetable entry no longer exists.');

            return;
        }

        $newSectionId = $request->requested_section_id ?? $entry->section_id;
        $newSubjectId = $request->requested_subject_id ?? $entry->subject_id;
        $newSlotId = $request->requested_timetable_slot_id ?? $entry->timetable_slot_id;
        $newDay = $request->requested_day_of_week ?? $entry->day_of_week;

        $conflict = TimetableEntry::where('section_id', $newSectionId)
            ->where('timetable_slot_id', $newSlotId)
            ->where('day_of_week', $newDay)
            ->where('id', '!=', $entry->id)
            ->exists();

        if ($conflict) {
            session()->flash('error', 'Cannot approve: another subject is already scheduled in that class, period and day.');

            return;
        }

        $oldSection = $entry->section;
        $oldSubject = $entry->subject;
        $oldScheduleLabel = TimetableNotifier::describe($entry->slot, $entry->day_of_week);
        $teacher = $entry->teacher;

        $entry->update([
            'section_id' => $newSectionId,
            'subject_id' => $newSubjectId,
            'timetable_slot_id' => $newSlotId,
            'day_of_week' => $newDay,
        ]);

        $request->update([
            'status' => TimetableChangeRequest::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $newSection = Section::with('schoolClass')->find($newSectionId);
        $newSubject = Subject::find($newSubjectId);
        $newSlot = TimetableSlot::find($newSlotId);
        $newScheduleLabel = TimetableNotifier::describe($newSlot, $newDay);
        $changedBy = $teacher?->user?->name.' (approved change request)';

        TimetableNotifier::notify(
            $oldSection,
            $teacher,
            'Timetable updated',
            TimetableNotifier::sectionLabel($oldSection).' — '.$oldSubject->name.' schedule updated. Previously: '.$oldScheduleLabel.'. Now: '.$newSubject->name.' on '.$newScheduleLabel.' ('.TimetableNotifier::sectionLabel($newSection).').',
            $changedBy
        );

        if ($newSection->id !== $oldSection->id) {
            TimetableNotifier::notify(
                $newSection,
                $teacher,
                'New period added to timetable',
                TimetableNotifier::sectionLabel($newSection).' — '.$newSubject->name.' scheduled on '.$newScheduleLabel.'.',
                $changedBy
            );
        }

        session()->flash('message', 'Request approved and timetable updated.');
    }

    public function startReject(int $requestId): void
    {
        $this->reviewingId = $requestId;
        $this->adminNote = '';
    }

    public function cancelReject(): void
    {
        $this->reviewingId = null;
        $this->adminNote = '';
    }

    public function reject(): void
    {
        $schoolId = auth()->user()->school_id;
        $request = TimetableChangeRequest::where('school_id', $schoolId)->with('teacher.user')->findOrFail($this->reviewingId);

        if ($request->status !== TimetableChangeRequest::STATUS_PENDING) {
            $this->cancelReject();

            return;
        }

        $request->update([
            'status' => TimetableChangeRequest::STATUS_REJECTED,
            'admin_note' => $this->adminNote ?: null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        TimetableNotifier::notifyUser(
            $request->teacher?->user,
            'Timetable change request rejected',
            'Your timetable change request has been rejected.'.($this->adminNote ? ' Reason: '.$this->adminNote : ''),
            auth()->user()->name.' (School Admin)'
        );

        $this->cancelReject();
        session()->flash('message', 'Request rejected.');
    }
}
