<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'threshold_alerta',
        'threshold_aviso',
        'threshold_recordatorio',
        'internal_copy_emails',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get internal emails as array
     */
    public function getInternalEmailsAttribute(): array
    {
        if (empty($this->internal_copy_emails)) {
            return [];
        }

        return array_map('trim', explode(',', $this->internal_copy_emails));
    }
}
