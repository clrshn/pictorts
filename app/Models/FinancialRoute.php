<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRoute extends Model
{
    protected $fillable = [
        'financial_id',
        'from_office',
        'to_office',
        'released_by',
        'received_by',
        'datetime_released',
        'datetime_received',
        'remarks',
    ];

    protected $casts = [
        'datetime_released' => 'datetime',
        'datetime_received' => 'datetime',
    ];

    public function financialRecord()
    {
        return $this->belongsTo(FinancialRecord::class, 'financial_id');
    }

    public function fromOffice()
    {
        return $this->belongsTo(Office::class, 'from_office');
    }

    public function toOffice()
    {
        return $this->belongsTo(Office::class, 'to_office');
    }

    public function releasedByUser()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
