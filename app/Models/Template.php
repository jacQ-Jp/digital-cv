<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'is_active',
        'is_default',
    ];

    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class, 'template_slug', 'slug');
    }

    public function thumbnailPreviewUrl(): ?string
    {
        $storedPath = trim((string) $this->thumbnail);

        if ($storedPath !== '' && Storage::disk('public')->exists($storedPath)) {
            return asset('storage/'.$storedPath);
        }

        $slug = Str::slug((string) $this->slug);
        $name = Str::slug((string) $this->name);

        $candidates = array_filter([
            $slug !== '' ? "images/templates/generated/{$slug}.png" : null,
            $name !== '' ? "images/templates/generated/{$name}.png" : null,
            $slug !== '' ? "images/templates/{$slug}.png" : null,
            $name !== '' ? "images/templates/{$name}.png" : null,
        ]);

        foreach ($candidates as $relativePath) {
            if (file_exists(public_path($relativePath))) {
                return asset($relativePath);
            }
        }

        return null;
    }
}
