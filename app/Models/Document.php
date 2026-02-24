<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Office;
use App\Models\User;
use App\Models\DocumentRoute;
use App\Models\DocumentFile;

class Document extends Model
{
    protected $fillable = [
        'dts_number',
        'ictu_number',
        'doc_number',
        'document_type',
        'direction',
        'originating_office',
        'to_office',
        'current_office',
        'current_holder',
        'subject',
        'action_required',
        'endorsed_to',
        'date_received',
        'status',
        'remarks',
        'shared_drive_link',
        'received_via_online',
        'encoded_by',
    ];

    protected $casts = [
        'date_received' => 'date',
        'received_via_online' => 'boolean',
    ];

    public function originatingOffice()
    {
        return $this->belongsTo(Office::class, 'originating_office');
    }

    public function destinationOffice()
    {
        return $this->belongsTo(Office::class, 'to_office');
    }

    public function currentOffice()
    {
        return $this->belongsTo(Office::class, 'current_office');
    }

    public function holder()
    {
        return $this->belongsTo(User::class, 'current_holder');
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function routes()
    {
        return $this->hasMany(DocumentRoute::class);
    }

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }
}