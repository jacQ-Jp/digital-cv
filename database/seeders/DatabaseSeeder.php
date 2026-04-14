<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $templateConfig = config('cv_templates.templates', []);
        $defaultSlug = collect($templateConfig)
            ->filter(fn ($template) => (bool) ($template['is_default'] ?? false))
            ->keys()
            ->first();

        foreach ($templateConfig as $slug => $template) {
            Template::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $template['name'] ?? ucfirst((string) $slug),
                    'description' => $template['description'] ?? null,
                    'thumbnail' => $template['thumbnail'] ?? null,
                    'is_active' => (bool) ($template['is_active'] ?? true),
                    'is_default' => (bool) ($template['is_default'] ?? false),
                ]
            );
        }

        if ($defaultSlug !== null) {
            Template::query()->where('slug', '!=', $defaultSlug)->update(['is_default' => false]);
        }

        $userRole = Role::query()->firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'User', 'slug' => 'user'],
        );

        $adminRole = Role::query()->firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id,
            ]
        );
    }
}
