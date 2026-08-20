<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableChangeRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'school_id',
        'teacher_id',
        'timetable_entry_id',
        'current_section_id',
        'current_subject_id',
        'current_timetable_slot_id',
        'current_day_of_week',
        'requested_section_id',
        'requested_subject_id',
        'requested_timetable_slot_id',
        'requested_day_of_week',
        'reason',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function timetableEntry(): BelongsTo
    {
        return $this->belongsTo(TimetableEntry::class);
    }

    public function currentSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'current_section_id');
    }

    public function currentSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'current_subject_id');
    }

    public function currentSlot(): BelongsTo
    {
        return $this->belongsTo(TimetableSlot::class, 'current_timetable_slot_id');
    }

    public function requestedSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'requested_section_id');
    }

    public function requestedSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'requested_subject_id');
    }

    public function requestedSlot(): BelongsTo
    {
        return $this->belongsTo(TimetableSlot::class, 'requested_timetable_slot_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function dayName(?int $day): ?string
    {
        return $day ? TimetableEntry::DAYS[$day] ?? null : null;
    }
}
