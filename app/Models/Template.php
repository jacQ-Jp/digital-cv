<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class, 'template_slug', 'slug');
    }
}
