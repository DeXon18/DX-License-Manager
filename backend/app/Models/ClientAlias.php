<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAlias extends Model
{
    protected $fillable = ['client_id', 'name'];

    /**
     * Get the client that owns the alias.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
