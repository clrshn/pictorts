<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasCollaborationFeatures;

class FinancialRecord extends Model
{
    use HasCollaborationFeatures;

    protected $fillable = [
        'type',
        'description',
        'reference_code',
        'supplier',
        'pr_number',
        'pr_amount',
        'po_number',
        'po_amount',
        'obr_number',
        'voucher_number',
        'office_origin',
        'current_office',
        'current_holder',
        'created_by',
        'status',
        'progress',
        'remarks',
    ];

    protected $casts = [
        'pr_amount' => 'decimal:2',
        'po_amount' => 'decimal:2',
    ];

    public function originOffice()
    {
        return $this->belongsTo(Office::class, 'office_origin');
    }

    public function currentOffice()
    {
        return $this->belongsTo(Office::class, 'current_office');
    }

    public function holder()
    {
        return $this->belongsTo(User::class, 'current_holder');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function routes()
    {
        return $this->hasMany(FinancialRoute::class, 'financial_id');
    }

    public function attachments()
    {
        return $this->hasMany(FinancialAttachment::class, 'financial_id');
    }

    public function relatedSupplierRecords()
    {
        return static::query()
            ->whereNotNull('supplier')
            ->where('supplier', $this->supplier)
            ->whereKeyNot($this->getKey())
            ->latest()
            ->limit(8);
    }
}
