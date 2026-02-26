<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['code','name'];

    /**
     * Scope to order offices with BAC second to last and Others last
     */
    public function scopeOrdered($query)
    {
        return $query->orderByRaw("
            CASE 
                WHEN UPPER(code) = 'BAC' THEN 2
                WHEN UPPER(code) = 'OTHERS' THEN 3
                ELSE 1
            END,
            code ASC
        ");
    }
}