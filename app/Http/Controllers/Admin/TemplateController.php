<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::query()
            ->withCount('cvs')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('admin.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:templates,slug'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;
        $isDefault = $request->has('is_default') ? $request->boolean('is_default') : false;

        // Default template must be active
        if ($isDefault && ! $isActive) {
            $this->throwRuleValidation('Default template harus tetap aktif.', 'is_active');
        }

        $this->ensureTemplateBladeExists($data['slug']);

        $thumbnailPath = $request->file('thumbnail')->store('templates', 'public');

        try {
            DB::transaction(function () use ($data, $thumbnailPath, $isActive, $isDefault) {
                $normalizedIsActive = $isDefault ? true : $isActive;

                // If marking as default, unmark all others
                if ($isDefault) {
                    Template::query()->update(['is_default' => false]);
                }

                Template::create([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? null,
                    'thumbnail' => $thumbnailPath,
                    'is_active' => $normalizedIsActive,
                    'is_default' => $isDefault,
                ]);

                $this->assertSingleDefaultTemplate();
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($thumbnailPath);

            throw $exception;
        }

        return redirect()->route('admin.templates.index')->with('status', 'Template berhasil ditambahkan.');
    }

    public function edit(Template $template): View
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('templates', 'slug')->ignore($template->id)],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $this->ensureTemplateBladeExists($data['slug']);

        $isUsed = $template->cvs()->exists();
        $oldThumbnail = $template->thumbnail;
        $uploadedThumbnailPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('templates', 'public')
            : null;

        $isActive = $request->has('is_active')
            ? $request->boolean('is_active')
            : (bool) $template->is_active;
        $isDefault = $request->has('is_default')
            ? $request->boolean('is_default')
            : (bool) $template->is_default;

        try {
            DB::transaction(function () use ($template, $data, $isUsed, $uploadedThumbnailPath, $oldThumbnail, $isActive, $isDefault) {
                // Default template must be active.
                $normalizedIsActive = $isDefault ? true : $isActive;

                // If this template is currently default, it must remain active
                if ($template->is_default && ! $normalizedIsActive) {
                    $this->throwRuleValidation(
                        'Default template cannot be inactivated.',
                        'is_active'
                    );
                }

                if (! $normalizedIsActive && $isUsed) {
                    $this->throwRuleValidation(
                        'Template cannot be inactivated because it is already used by users.',
                        'is_active'
                    );
                }

                // If marking this template as default, unmark all others
                if ($isDefault && ! $template->is_default) {
                    Template::query()
                        ->where('id', '!=', $template->id)
                        ->update(['is_default' => false]);
                }

                $template->update([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? null,
                    'thumbnail' => $uploadedThumbnailPath ?? $oldThumbnail,
                    'is_active' => $normalizedIsActive,
                    'is_default' => $isDefault,
                ]);

                $this->assertSingleDefaultTemplate();
            });
        } catch (\Throwable $exception) {
            if ($uploadedThumbnailPath) {
                Storage::disk('public')->delete($uploadedThumbnailPath);
            }

            throw $exception;
        }

        if ($uploadedThumbnailPath && $oldThumbnail && $oldThumbnail !== $uploadedThumbnailPath) {
            Storage::disk('public')->delete($oldThumbnail);
        }

        return redirect()->route('admin.templates.index')->with('status', 'Template updated.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        if ($template->is_default) {
            $this->throwRuleValidation('Default template cannot be deleted.');
        }

        if ($template->cvs()->exists()) {
            $this->throwRuleValidation('Template cannot be deleted because it is already used by users.');
        }

        $thumbnailPath = $template->thumbnail;

        $template->delete();

        if ($thumbnailPath) {
            Storage::disk('public')->delete($thumbnailPath);
        }

        return redirect()->route('admin.templates.index')->with('status', 'Template deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template_ids' => ['required', 'array', 'min:1'],
            'template_ids.*' => ['required', 'integer', 'distinct', Rule::exists('templates', 'id')],
        ]);

        $templates = Template::query()
            ->whereIn('id', $data['template_ids'])
            ->withCount('cvs')
            ->get();

        $deletable = $templates->filter(function (Template $template): bool {
            return ! $template->is_default && (int) $template->cvs_count === 0;
        });

        if ($deletable->isEmpty()) {
            $this->throwRuleValidation('Template yang dipilih tidak bisa dihapus (default / sudah dipakai).', 'template_ids');
        }

        $deletedCount = 0;

        DB::transaction(function () use ($deletable, &$deletedCount) {
            foreach ($deletable as $template) {
                $thumbnailPath = $template->thumbnail;
                $template->delete();
                if ($thumbnailPath) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                $deletedCount++;
            }
        });

        $blockedCount = $templates->count() - $deletedCount;
        $status = $deletedCount.' template berhasil dihapus.';
        if ($blockedCount > 0) {
            $status .= ' '.$blockedCount.' template dilewati karena default / sedang dipakai.';
        }

        return redirect()->route('admin.templates.index')->with('status', $status);
    }

    public function destroyAll(): RedirectResponse
    {
        $templates = Template::query()
            ->withCount('cvs')
            ->get();

        $deletable = $templates->filter(function (Template $template): bool {
            return ! $template->is_default && (int) $template->cvs_count === 0;
        });

        if ($deletable->isEmpty()) {
            $this->throwRuleValidation('Tidak ada template yang bisa dihapus (default / sedang dipakai).', 'template_ids');
        }

        $deletedCount = 0;

        DB::transaction(function () use ($deletable, &$deletedCount) {
            foreach ($deletable as $template) {
                $thumbnailPath = $template->thumbnail;
                $template->delete();
                if ($thumbnailPath) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                $deletedCount++;
            }
        });

        $blockedCount = $templates->count() - $deletedCount;
        $status = 'Berhasil menghapus '.$deletedCount.' template.';
        if ($blockedCount > 0) {
            $status .= ' '.$blockedCount.' template dilewati karena default / sedang dipakai.';
        }

        return redirect()->route('admin.templates.index')->with('status', $status);
    }

    public function toggleActive(Template $template): RedirectResponse
    {
        if ($template->is_default) {
            $this->throwRuleValidation('Default template cannot be inactivated.');
        }

        if ($template->is_active && $template->cvs()->exists()) {
            $this->throwRuleValidation('Template cannot be inactivated because it is already used by users.');
        }

        $template->update(['is_active' => ! $template->is_active]);

        $this->assertSingleDefaultTemplate();

        return redirect()->route('admin.templates.index')->with('status', 'Template status updated.');
    }

    public function setDefault(Template $template): RedirectResponse
    {
        if ($template->is_default) {
            return redirect()->route('admin.templates.index')->with('status', 'Template ini sudah menjadi default.');
        }

        DB::transaction(function () use ($template) {
            // Unmark all other templates as default
            Template::query()
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);

            // Mark this template as default and active
            $template->update([
                'is_default' => true,
                'is_active' => true,
            ]);

            $this->assertSingleDefaultTemplate();
        });

        return redirect()->route('admin.templates.index')->with('status', 'Template berhasil dijadikan default.');
    }

    private function assertSingleDefaultTemplate(): void
    {
        $defaultCount = Template::query()->where('is_default', true)->count();
        if ($defaultCount > 1) {
            $this->throwRuleValidation('Only one default template is allowed.', 'is_default');
        }
    }

    private function throwRuleValidation(string $message, string $field = 'status'): void
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    private function ensureTemplateBladeExists(string $slug): void
    {
        if (view()->exists("cv.templates.$slug")) {
            return;
        }

        throw ValidationException::withMessages([
            'slug' => "Template file tidak ditemukan: resources/views/cv/templates/$slug.blade.php",
        ]);
    }
}
