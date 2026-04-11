<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Models\Cv;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return redirect()->route('cvs.create');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $cvs = Cv::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('cvs.index', compact('cvs'));
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

        return redirect()->route('cvs.show', $cv);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return view('cvs.show', compact('cv'));
    }

    /**
     * Render CV dynamically based on template_slug (private preview).
     */
    public function render(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return $this->renderWithTemplateFallback($cv);
    }

    /**
     * Public CV (published only).
     */
    public function public(Cv $cv): View
    {
        abort_unless($cv->status === 'published', 404);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return $this->renderWithTemplateFallback($cv);
    }

    private function renderWithTemplateFallback(Cv $cv): View
    {
        $slug = $cv->template_slug;

        // Views are stored in: resources/views/cv/templates/{slug}.blade.php
        $view = $slug ? "cv.templates.$slug" : null;

        if ($view && view()->exists($view)) {
            return view($view, compact('cv'));
        }

        // Prefer DB default if it exists and has a view, otherwise use hard default view.
        $defaultSlug = Template::query()->where('is_default', true)->value('slug');
        if ($defaultSlug && view()->exists("cv.templates.$defaultSlug")) {
            return view("cv.templates.$defaultSlug", compact('cv'));
        }

        if (view()->exists('cv.templates.default')) {
            return view('cv.templates.default', compact('cv'));
        }

        abort(500, 'No template view available.');
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
}
