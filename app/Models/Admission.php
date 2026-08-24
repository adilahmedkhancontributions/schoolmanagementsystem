<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    use \App\Support\Auditable;

    public const STATUS_INQUIRY = 'inquiry';
    public const STATUS_INTERVIEW_SCHEDULED = 'interview_scheduled';
    public const STATUS_TEST_SCHEDULED = 'test_scheduled';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_ENROLLED = 'enrolled';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'school_id',
        'applicant_name',
        'father_name',
        'phone',
        'email',
        'gender',
        'date_of_birth',
        'address',
        'school_class_id',
        'source',
        'status',
        'interview_date',
        'interview_notes',
        'test_date',
        'test_score',
        'test_notes',
        'decision_notes',
        'enrolled_student_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'interview_date' => 'datetime',
            'test_date' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function enrolledStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'enrolled_student_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
