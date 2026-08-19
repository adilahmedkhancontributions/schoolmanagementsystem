<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'school_class_id',
        'created_by',
        'title',
        'body',
        'audience',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lessThanOrEqualTo(now());
    }

    public function scopeVisibleTo($query, string $role, array|int|null $schoolClassIds = null)
    {
        $audiences = match ($role) {
            'teacher' => ['everyone', 'teachers'],
            'student' => ['everyone', 'students'],
            'parent' => ['everyone', 'parents'],
            default => ['everyone', 'teachers', 'students', 'parents'],
        };

        $classIds = array_filter((array) $schoolClassIds);

        return $query->whereIn('audience', $audiences)
            ->when($classIds, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('school_class_id')->orWhereIn('school_class_id', $classIds)))
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
