<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class LicenseArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'week_number',
        'year',
        'files_count',
        'clients_summary',
        'storage_path',
        'origin'
    ];

    protected $casts = [
        'clients_summary' => 'array',
    ];

    /**
     * Obtiene la ruta completa al archivo ZIP.
     */
    public function getFullPathAttribute(): string
    {
        return Storage::disk('local')->path($this->storage_path);
    }
}
