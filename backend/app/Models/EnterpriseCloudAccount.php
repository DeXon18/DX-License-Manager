<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnterpriseCloudAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sold_to',
        'account_id',
        'admin_email',
    ];

    /**
     * Get the client that owns the account.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
