<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ResourceLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'vendor',
        'category',
        'label',
        'url',
        'description',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Scope a query to only include specific vendor resources.
     */
    public function scopeForVendor($query, $vendor)
    {
        return $query->where('vendor', $vendor);
    }

    /**
     * Scope a query to only include specific category resources.
     */
    public function scopeForCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
