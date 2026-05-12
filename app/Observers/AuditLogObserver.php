<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditLogObserver
{
    public function created(Model $model): void
    {
        $this->logger()->logModel($model, AuditLog::EVENT_CREATED, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        if ($model instanceof User && array_keys($changes) === ['password']) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        $this->logger()->logModel($model, AuditLog::EVENT_UPDATED, $oldValues, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->logger()->logModel($model, AuditLog::EVENT_DELETED, $model->getOriginal(), null);
    }

    private function logger(): AuditLogger
    {
        return app(AuditLogger::class);
    }
}
