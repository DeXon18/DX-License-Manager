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
        'expiration_date',
        'node_locked_host_id',
        'status',
    ];

    protected $casts = [
        'expiration_date' => 'date',
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
