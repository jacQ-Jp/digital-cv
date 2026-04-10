<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::query()->orderByDesc('is_default')->orderBy('name')->get();

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
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        abort_unless(view()->exists("cv.templates.{$data['slug']}"), 422, 'Template view not found.');

        DB::transaction(function () use ($data) {
            if (!empty($data['is_default'])) {
                Template::query()->where('is_default', true)->update(['is_default' => false]);
                $data['is_active'] = true;
            }

            Template::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'thumbnail' => $data['thumbnail'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
                'is_default' => (bool)($data['is_default'] ?? false),
            ]);
        });

        return redirect()->route('admin.templates.index');
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
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        abort_unless(view()->exists("cv.templates.{$data['slug']}"), 422, 'Template view not found.');

        $isUsed = $template->cvs()->exists();

        DB::transaction(function () use ($template, $data, $isUsed) {
            $wantsInactive = array_key_exists('is_active', $data) && ! (bool) $data['is_active'];
            if ($wantsInactive && $isUsed) {
                abort(422, 'Template cannot be inactivated because it is already used by users.');
            }

            if (!empty($data['is_default'])) {
                Template::query()->where('id', '!=', $template->id)->where('is_default', true)->update(['is_default' => false]);
                $data['is_active'] = true;
            }

            $template->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'thumbnail' => $data['thumbnail'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? $template->is_active),
                'is_default' => (bool)($data['is_default'] ?? $template->is_default),
            ]);
        });

        return redirect()->route('admin.templates.index');
    }

    public function destroy(Template $template): RedirectResponse
    {
        abort_unless(! $template->cvs()->exists(), 422, 'Template cannot be deleted because it is already used by users.');

        $template->delete();

        return redirect()->route('admin.templates.index');
    }
}
