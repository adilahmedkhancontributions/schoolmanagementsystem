<?php

namespace App\Support;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->writeAuditLog('created', null, $model->getAttributes()));

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $original = array_intersect_key($model->getOriginal(), $changes);
            $model->writeAuditLog('updated', $original, $changes);
        });

        static::deleted(fn ($model) => $model->writeAuditLog('deleted', $model->getAttributes(), null));
    }

    protected function writeAuditLog(string $action, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'school_id' => $this->auditSchoolId(),
            'user_id' => auth()->id(),
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()?->ip(),
        ]);
    }

    protected function auditSchoolId(): ?int
    {
        return $this->school_id ?? null;
    }
}
