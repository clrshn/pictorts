<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'todo_notifications',
        'document_notifications',
        'financial_notifications',
        'reminder_notifications',
        'approval_notifications',
    ];

    protected $casts = [
        'todo_notifications' => 'boolean',
        'document_notifications' => 'boolean',
        'financial_notifications' => 'boolean',
        'reminder_notifications' => 'boolean',
        'approval_notifications' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
