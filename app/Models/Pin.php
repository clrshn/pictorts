<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $fillable = [
        'user_id',
        'pinnable_id',
        'pinnable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pinnable()
    {
        return $this->morphTo();
    }
}
