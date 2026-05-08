<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormalizationDecision extends Model
{
    protected $fillable = ['detected_name', 'decision'];
}
