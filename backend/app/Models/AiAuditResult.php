<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAuditResult extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'client_id',
        'sold_to',
        'customer_name',
        'vendor',
        'results',
        'warnings',
        'status',
    ];

    protected $casts = [
        'results' => 'array',
        'warnings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
