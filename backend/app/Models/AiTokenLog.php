<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiTokenLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'model',
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

    public function getEstimatedCostAttribute()
    {
        $pricingConfig = config('ai.pricing', []);
        
        $model = $this->model ?? 'default';
        $modelKey = 'default';
        
        if (isset($pricingConfig[$model])) {
            $modelKey = $model;
        } elseif (str_contains(strtolower($model), 'free')) {
            $modelKey = 'default';
        } elseif (str_contains(strtolower($model), 'gemini')) {
            $modelKey = 'gemini-1.5-flash';
        } elseif (str_contains(strtolower($model), 'deepseek') || strtolower($this->provider) === 'n8n') {
            $modelKey = 'deepseek-chat';
        }

        $promptPrice = $pricingConfig[$modelKey]['prompt'] ?? 0;
        $completionPrice = $pricingConfig[$modelKey]['completion'] ?? 0;

        return ($this->prompt_tokens / 1000000 * $promptPrice) + ($this->completion_tokens / 1000000 * $completionPrice);
    }
}
