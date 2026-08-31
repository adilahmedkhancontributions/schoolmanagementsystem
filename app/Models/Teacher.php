<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;
    use \App\Support\Auditable;

    protected $fillable = [
        'user_id',
        'school_id',
        'campus_id',
        'employee_id',
        'qualification',
        'specialization',
        'joining_date',
        'employment_type',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function salaryStructure()
    {
        return $this->morphOne(SalaryStructure::class, 'payable');
    }

    public function payslips()
    {
        return $this->morphMany(Payslip::class, 'payable');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function timetableChangeRequests(): HasMany
    {
        return $this->hasMany(TimetableChangeRequest::class);
    }

    public function homeworks(): HasMany
    {
        return $this->hasMany(Homework::class);
    }
}
