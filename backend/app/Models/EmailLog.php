<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'recipient',
        'subject',
        'mailable_class',
        'status',
        'error_message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
