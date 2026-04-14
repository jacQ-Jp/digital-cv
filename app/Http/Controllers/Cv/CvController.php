<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Models\Cv;
use App\Models\Template;
use App\Support\CvTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CvController extends Controller
{
    // Step 1: show active templates for selection
    public function selectTemplate(): View
    {
        $templates = Template::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('cvs.select-template', compact('templates'));
    }

    // Step 1: persist template selection in session (fallback to default)
    public function saveTemplateSelection(Request $request): RedirectResponse
    {
        $request->validate([
            'template_slug' => ['nullable', 'string', 'exists:templates,slug'],
            'redirect_to' => ['nullable', 'string'],
        ]);

        $selectedSlug = $request->input('template_slug');

        $template = null;
        if ($selectedSlug) {
            $template = Template::query()->where('is_active', true)->where('slug', $selectedSlug)->first();
        }

        if (! $template) {
            $template = Template::query()->where('is_default', true)->first()
                ?? Template::query()->where('is_active', true)->orderBy('id')->first();
        }

        if (! $template) {
            abort(422, 'No active template available.');
        }

        session(['cv_builder.template_slug' => $template->slug]);

        $redirectTo = $request->input('redirect_to');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo);
        }

        $cv = Cv::create([
            'user_id' => Auth::id(),
            'title' => 'Untitled CV',
            'summary' => null,
            'template_slug' => $template->slug,
            'status' => 'draft',
            'personal_name' => Auth::user()?->name,
            'personal_email' => Auth::user()?->email,
            'public_uuid' => (string) Str::uuid(),
        ]);

        return redirect()->route('cvs.wizard', $cv);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $query = Cv::query()->where('user_id', Auth::id());

        $status = request()->string('status')->toString();
        if (in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }

        $template = request()->string('template')->toString();
        if ($template !== '') {
            $query->where('template_slug', $template);
        }

        $search = trim((string) request()->get('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $cvs = $query->latest()->get();

        $templates = Template::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['name', 'slug', 'thumbnail']);

        return view('cvs.index', compact('cvs', 'templates', 'status', 'template', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        $selectedTemplateSlug = session('cv_builder.template_slug');

        // Hard-enforce: must pick a template first (step 1).
        if (! $selectedTemplateSlug) {
            return redirect()->route('cv-builder.templates');
        }

        $templates = Template::query()->where('is_active', true)->orderBy('name')->get();

        return view('cvs.create', [
            'templates' => $templates,
            'selectedTemplateSlug' => $selectedTemplateSlug,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCvRequest $request): RedirectResponse
    {
        $templateSlug = $request->validated('template_slug');

        $cv = Cv::create([
            'user_id' => Auth::id(),
            'title' => $request->validated('title'),
            'summary' => $request->validated('summary'),
            'template_slug' => $templateSlug,
            'status' => $request->validated('status'),
        ]);

        // Reset builder selection after successful creation.
        session()->forget('cv_builder.template_slug');

        return redirect()->route('cvs.experiences.create', $cv);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        // Detail/review should follow the selected CV template.
        return $this->renderWithTemplateFallback($cv);
    }

    /**
     * Render CV dynamically based on template_slug (private preview).
     */
    public function render(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return $this->renderWithTemplateFallback($cv, ['previewMode' => true]);
    }

    /**
     * Public CV (published only).
     */
    public function public(Cv $cv): View
    {
        abort_unless($cv->status === 'published', 404);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return $this->renderWithTemplateFallback($cv, ['previewMode' => true]);
    }

    public function publicByUuid(string $token): View
    {
        $cv = Cv::query()
            ->where('public_uuid', $token)
            ->where('status', 'published')
            ->first();

        if (! $cv && ctype_digit($token)) {
            $cv = Cv::query()
                ->whereKey((int) $token)
                ->where('status', 'published')
                ->first();
        }

        abort_if(! $cv, 404);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return $this->renderWithTemplateFallback($cv, ['previewMode' => true]);
    }

    private function renderWithTemplateFallback(Cv $cv, array $extraData = []): View
    {
        return app(CvTemplateRenderer::class)->render($cv, $extraData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $templates = Template::query()->where('is_active', true)->orderBy('name')->get();

        return view('cvs.edit', compact('cv', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCvRequest $request, Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->update($request->validated());

        return redirect()->route('cvs.show', $cv);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->delete();

        return redirect()->route('cvs.index');
    }

    public function togglePublish(Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $nextStatus = $cv->status === 'published' ? 'draft' : 'published';
        if ($nextStatus === 'published') {
            $publishErrors = $cv->publishingErrors();
            if (! empty($publishErrors)) {
                return redirect()
                    ->back()
                    ->withErrors($publishErrors)
                    ->with('status', 'Cannot publish CV. Complete required personal fields first.');
            }

            if (! $cv->public_uuid) {
                $cv->public_uuid = (string) Str::uuid();
            }
        }

        $cv->update([
            'status' => $nextStatus,
            'public_uuid' => $cv->public_uuid,
        ]);

        return redirect()->route('cvs.index')->with('status', 'CV status updated.');
    }
}
