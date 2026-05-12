<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'week_number',
        'year',
        'files_count',
        'clients_summary',
        'storage_path'
    ];

    protected $casts = [
        'clients_summary' => 'array',
    ];

    /**
     * Obtiene la ruta completa al archivo ZIP.
     */
    public function getFullPathAttribute(): string
    {
        return storage_path('app/private/' . $this->storage_path);
    }
}
