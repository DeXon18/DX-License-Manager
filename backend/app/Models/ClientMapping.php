<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMapping extends Model
{
    protected $fillable = [
        'client_id',
        'sold_to',
        'vendor',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
