<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(Model $subject, string $action, ?string $title = null, ?string $description = null, array $properties = []): void
    {
        $subject->activityLogs()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'title' => $title,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
