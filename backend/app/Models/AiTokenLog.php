<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiTokenLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'action',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
