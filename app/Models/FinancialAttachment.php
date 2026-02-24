<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAttachment extends Model
{
    protected $fillable = [
        'financial_id',
        'file_path',
        'file_name',
        'uploaded_by',
    ];

    public function financialRecord()
    {
        return $this->belongsTo(FinancialRecord::class, 'financial_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
