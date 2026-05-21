<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenewalLogFile extends Model
{
    protected $fillable = [
        'renewal_log_id',
        'file_path',
        'file_name',
    ];

    public function renewalLog(): BelongsTo
    {
        return $this->belongsTo(RenewalLog::class);
    }
}
