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
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        abort_unless(view()->exists("templates.{$data['slug']}"), 422, 'Template view not found.');

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

            $this->assertSingleDefaultTemplate();
        });

        return redirect()->route('admin.templates.index');
    }

    public function edit(Template $template): View
    {
        $isUsed = $template->cvs()->exists();

        return view('admin.templates.edit', compact('template', 'isUsed'));
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

        abort_unless(view()->exists("templates.{$data['slug']}"), 422, 'Template view not found.');

        $isUsed = $template->cvs()->exists();

        DB::transaction(function () use ($template, $data, $isUsed) {
            $wantsInactive = array_key_exists('is_active', $data) && ! (bool) $data['is_active'];
            if ($wantsInactive && $isUsed) {
                abort(422, 'Template cannot be inactivated because it is already used by users.');
            }

            // Do not allow deactivating a default template.
            if ($wantsInactive && $template->is_default) {
                abort(422, 'Default template cannot be inactivated.');
            }

            if (!empty($data['is_default'])) {
                // Only one default template allowed.
                Template::query()
                    ->where('id', '!=', $template->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);

                // Default template must be active.
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

            $this->assertSingleDefaultTemplate();
        });

        return redirect()->route('admin.templates.index')->with('status', 'Template updated.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        abort_unless(! $template->is_default, 422, 'Default template cannot be deleted.');
        abort_unless(! $template->cvs()->exists(), 422, 'Template cannot be deleted because it is already used by users.');

        $template->delete();

        return redirect()->route('admin.templates.index')->with('status', 'Template deleted.');
    }

    public function toggleActive(Template $template): RedirectResponse
    {
        if ($template->is_default) {
            abort(422, 'Default template cannot be inactivated.');
        }

        if ($template->is_active && $template->cvs()->exists()) {
            abort(422, 'Template cannot be inactivated because it is already used by users.');
        }

        $template->update(['is_active' => ! $template->is_active]);

        $this->assertSingleDefaultTemplate();

        return redirect()->route('admin.templates.index')->with('status', 'Template status updated.');
    }

    private function assertSingleDefaultTemplate(): void
    {
        $defaultCount = Template::query()->where('is_default', true)->count();
        abort_if($defaultCount > 1, 422, 'Only one default template is allowed.');
    }
}
