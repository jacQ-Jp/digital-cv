<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
