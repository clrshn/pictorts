<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Todo extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
            'high' => '#e74c3c',
            'medium' => '#f39c12',
            'low' => '#3498db',
            default => '#95a5a6'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => '#27ae60',
            'in_progress' => '#e67e22',
            'pending' => '#95a5a6',
            default => '#95a5a6'
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match($this->priority) {
            'high' => 'HIGH',
            'medium' => 'MEDIUM',
            'low' => 'LOW',
            default => 'MEDIUM'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'completed' => 'COMPLETED',
            'in_progress' => 'IN PROGRESS',
            'pending' => 'PENDING',
            default => 'PENDING'
        };
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }
}
