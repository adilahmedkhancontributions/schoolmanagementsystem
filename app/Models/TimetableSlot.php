<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'start_time',
        'end_time',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }
}
