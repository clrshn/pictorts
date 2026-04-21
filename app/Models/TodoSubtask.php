<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoSubtask extends Model
{
    protected $fillable = [
        'todo_id',
        'title',
        'is_completed',
        'completed_at',
        'position',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }
}
