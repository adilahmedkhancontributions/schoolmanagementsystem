<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_invoice_id',
        'type',
        'is_percentage',
        'value',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_percentage' => 'boolean',
            'value' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function amountFor(string $invoiceAmount): string
    {
        return $this->is_percentage
            ? bcdiv(bcmul($invoiceAmount, (string) $this->value, 4), '100', 2)
            : number_format((float) $this->value, 2, '.', '');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'sibling' => 'Sibling Discount',
            'scholarship' => 'Scholarship',
            'staff_child' => 'Staff Child',
            'need_based' => 'Need-Based',
            default => 'Custom',
        };
    }
}
