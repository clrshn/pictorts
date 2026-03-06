<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // added this line

class Office extends Model
{
    protected $fillable = ['code','name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope to order offices alphabetically A-Z
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('code', 'asc');
    }
}