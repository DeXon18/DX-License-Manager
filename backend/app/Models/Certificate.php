<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'file_name',
        'upload_date',
        'user_id',
    ];

    protected $casts = [
        'upload_date' => 'datetime',
    ];

    /**
     * Get the client that owns the certificate.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who uploaded the certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
