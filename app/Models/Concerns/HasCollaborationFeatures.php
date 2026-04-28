<?php

namespace App\Models\Concerns;

trait HasCollaborationFeatures
{
    // These polymorphic relationships let Documents, Financial Records, and Todos
    // share one collaboration model instead of duplicating separate tables per module.
    public function pins()
    {
        return $this->morphMany(\App\Models\Pin::class, 'pinnable');
    }

    public function comments()
    {
        return $this->morphMany(\App\Models\Comment::class, 'commentable')
            ->whereNull('parent_id')
            ->latest();
    }

    public function allComments()
    {
        return $this->morphMany(\App\Models\Comment::class, 'commentable')->latest();
    }

    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject')->latest();
    }

    public function approval()
    {
        return $this->morphOne(\App\Models\Approval::class, 'approvable');
    }

    public function pinnedByCurrentUser(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->pins->contains('user_id', auth()->id());
    }
}
