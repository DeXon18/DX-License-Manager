<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    protected $table = 'ai_models';

    protected $fillable = [
        'openrouter_id',
        'name',
        'is_free',
        'price_prompt',
        'price_completion',
        'is_active',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'price_prompt' => 'decimal:6',
        'price_completion' => 'decimal:6',
    ];

    public function primaryRoutes(): HasMany
    {
        return $this->hasMany(AiRoute::class, 'primary_model_id');
    }

    public function fallbackRoutes(): HasMany
    {
        return $this->hasMany(AiRoute::class, 'fallback_model_id');
    }
}
