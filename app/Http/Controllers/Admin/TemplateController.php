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
        $hasDefault = Template::query()->where('is_default', true)->exists();

        return view('admin.templates.create', compact('hasDefault'));
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

        if ($isDefault && Template::query()->where('is_default', true)->exists()) {
            $this->throwRuleValidation(
                'Sudah ada template default. Nonaktifkan status default pada template tersebut terlebih dahulu.',
                'is_default'
            );
        }

        $this->ensureTemplateBladeExists($data['slug']);

        $thumbnailPath = $request->file('thumbnail')->store('templates', 'public');

        try {
            DB::transaction(function () use ($data, $thumbnailPath, $isActive, $isDefault) {
                $normalizedIsActive = $isDefault ? true : $isActive;

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

        return redirect()->route('admin.templates.index');
    }

    public function edit(Template $template): View
    {
        $isUsed = $template->cvs()->exists();
        $hasAnotherDefault = Template::query()
            ->where('id', '!=', $template->id)
            ->where('is_default', true)
            ->exists();

        return view('admin.templates.edit', compact('template', 'isUsed', 'hasAnotherDefault'));
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

        $hasAnotherDefault = Template::query()
            ->where('id', '!=', $template->id)
            ->where('is_default', true)
            ->exists();

        if ($isDefault && $hasAnotherDefault) {
            $this->throwRuleValidation(
                'Sudah ada template default. Nonaktifkan status default pada template tersebut terlebih dahulu.',
                'is_default'
            );
        }

        try {
            DB::transaction(function () use ($template, $data, $isUsed, $uploadedThumbnailPath, $oldThumbnail, $isActive, $isDefault) {
                // Default template must be active.
                $normalizedIsActive = $isDefault ? true : $isActive;

                if (! $normalizedIsActive && $isUsed) {
                    $this->throwRuleValidation(
                        'Template cannot be inactivated because it is already used by users.',
                        'is_active'
                    );
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

        $hasAnotherDefault = Template::query()
            ->where('id', '!=', $template->id)
            ->where('is_default', true)
            ->exists();

        if ($hasAnotherDefault) {
            $this->throwRuleValidation(
                'Sudah ada template default. Nonaktifkan status default pada template tersebut terlebih dahulu.',
                'is_default'
            );
        }

        $template->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->assertSingleDefaultTemplate();

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
