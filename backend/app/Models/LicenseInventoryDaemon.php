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
        'additional_sold_tos',
        'daemon',
        'hostname',
        'composite',
        'hardware_id',
        'version',
        'type',
        'status',
    ];

    protected $casts = [
        'additional_sold_tos' => 'array',
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

    /**
     * Scope to only include active daemons.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to only include dropped daemons.
     */
    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }
}
