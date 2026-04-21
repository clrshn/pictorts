<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Concerns\HasCollaborationFeatures;

class Todo extends Model
{
    use HasCollaborationFeatures;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'user_id',
        'assigned_to',
        'remarks',
        'date_added',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_interval',
        'recurrence_end_date',
        'recurring_parent_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_added' => 'date',
        'is_recurring' => 'boolean',
        'recurrence_end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subtasks()
    {
        return $this->hasMany(TodoSubtask::class)->orderBy('position');
    }

    public function recurringParent()
    {
        return $this->belongsTo(self::class, 'recurring_parent_id');
    }

    public function recurringChildren()
    {
        return $this->hasMany(self::class, 'recurring_parent_id');
    }

    public function scopeForUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeByStatus($query, $status = null)
    {
        if ($status && $status !== 'ALL') {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByPriority($query, $priority = null)
    {
        if ($priority && $priority !== 'ALL') {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'top' => '#8b0000',
            'high' => '#e74c3c',
            'medium' => '#f39c12',
            'low' => '#3498db',
            default => '#95a5a6'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'done' => '#27ae60',
            'on-going' => '#e67e22',
            'pending' => '#95a5a6',
            'cancelled' => '#7f8c8d',
            default => '#95a5a6'
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match($this->priority) {
            'top' => 'TOP',
            'high' => 'HIGH',
            'medium' => 'MEDIUM',
            'low' => 'LOW',
            default => 'MEDIUM'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'done' => 'DONE',
            'on-going' => 'ON GOING',
            'pending' => 'PENDING',
            'cancelled' => 'CANCELLED',
            default => 'PENDING'
        };
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['done', 'cancelled'], true);
    }

    public function getCompletionPercentAttribute(): int
    {
        $total = $this->subtasks->count();

        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->subtasks->where('is_completed', true)->count() / $total) * 100);
    }
}
