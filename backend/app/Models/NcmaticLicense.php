<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NcmaticLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'serial_number',
        'license_type',
        'seats',
        'expiration_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
