<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LicenseInventoryDaemon extends Model
{
    protected $fillable = [
        'uuid',
        'client_id',
        'sold_to',
        'daemon',
        'hostname',
        'composite',
        'hardware_id',
        'version',
        'type',
    ];

    protected static function booted()
    {
        static::creating(function ($daemon) {
            if (empty($daemon->uuid)) {
                $daemon->uuid = (string) Str::uuid();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(LicenseInventoryProduct::class, 'daemon_id');
    }

    /**
     * Identifica el vendor basado en el nombre del daemon.
     */
    public function getVendorAttribute(): string
    {
        $daemon = strtolower($this->daemon);
        if (str_contains($daemon, 'moldex')) {
            return 'moldex';
        }
        return 'siemens';
    }
}
