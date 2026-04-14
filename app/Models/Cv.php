<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'personal_name',
        'personal_email',
        'photo_path',
        'template_slug',
        'status',
        'public_uuid',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_slug', 'slug');
    }

    public function publishingErrors(): array
    {
        $errors = [];

        if (blank(trim((string) $this->personal_name))) {
            $errors['personal_name'] = 'Name is required before publishing.';
        }

        if (blank(trim((string) $this->personal_email))) {
            $errors['personal_email'] = 'Email is required before publishing.';
        }

        return $errors;
    }
}
