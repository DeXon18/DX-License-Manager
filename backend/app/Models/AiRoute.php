<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRoute extends Model
{
    protected $table = 'ai_routes';
    protected $primaryKey = 'task_name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'task_name',
        'primary_model_id',
        'fallback_model_id',
        'description',
    ];

    public function primaryModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'primary_model_id');
    }

    public function fallbackModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'fallback_model_id');
    }
}
