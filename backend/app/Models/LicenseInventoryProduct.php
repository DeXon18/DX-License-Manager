<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseInventoryProduct extends Model
{
    protected $fillable = [
        'daemon_id',
        'product_code',
        'description',
        'quantity',
        'start_date',
        'expiration_date',
        'node_locked_host_id',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'expiration_date' => 'datetime',
    ];

    public function daemon(): BelongsTo
    {
        return $this->belongsTo(LicenseInventoryDaemon::class, 'daemon_id');
    }

    /**
     * Scope to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
