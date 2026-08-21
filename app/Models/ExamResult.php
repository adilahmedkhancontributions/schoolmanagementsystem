<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;
    use \App\Support\Auditable;

    protected $fillable = [
        'exam_subject_id',
        'student_id',
        'marks_obtained',
        'remarks',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
        ];
    }

    public function examSubject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function percentage(): ?float
    {
        if ($this->marks_obtained === null || (float) $this->examSubject->max_marks <= 0) {
            return null;
        }

        return round(((float) $this->marks_obtained / (float) $this->examSubject->max_marks) * 100, 2);
    }

    public function isPass(): ?bool
    {
        if ($this->marks_obtained === null) {
            return null;
        }

        return (float) $this->marks_obtained >= (float) $this->examSubject->pass_marks;
    }

    public function grade(): ?string
    {
        $percentage = $this->percentage();

        if ($percentage === null) {
            return null;
        }

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            default => 'F',
        };
    }
}
