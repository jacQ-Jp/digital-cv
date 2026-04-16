<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CvController extends Controller
{
    public function index()
    {
        $cvs = Cv::query()
            ->where('user_id', auth()->id())
            ->with(['experiences', 'educations', 'skills', 'template'])
            ->when(request('q'), fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->when(request('template'), fn($q, $s) => $q->where('template_slug', $s))
            ->latest('updated_at')
            ->get();

        $templates = Template::where('is_active', true)->get();

        return view('cvs.index', compact('cvs', 'templates'));
    }

    public function selectTemplate()
    {
        $templates = Template::where('is_active', true)->get();
        return view('cvs.select-template', compact('templates'));
    }

    public function saveTemplateSelection(Request $request)
    {
        $request->validate([
            'template_slug' => 'required|exists:templates,slug',
            'title' => 'nullable|string|max:255',
        ]);

        $cv = Cv::create([
            'user_id' => auth()->id(),
            'title' => $request->title ?: 'Untitled CV',
            'template_slug' => $request->template_slug,
            'status' => 'draft',
            'public_uuid' => Str::uuid()->toString(),
        ]);

        return redirect()->route('cvs.wizard', $cv);
    }

    public function show(Cv $cv)
    {
        $this->own($cv);
        $cv->load(['experiences', 'educations', 'skills', 'template']);
        return view('cvs.show', compact('cv'));
    }

    public function edit(Cv $cv)
    {
        $this->own($cv);
        return redirect()->route('cvs.wizard', $cv);
    }

    public function update(Request $request, Cv $cv) { $this->own($cv); }

    public function destroy(Cv $cv)
    {
        $this->own($cv);
        $cv->delete();
        return back()->with('success', 'CV berhasil dihapus.');
    }

    public function render(Cv $cv)
    {
        $this->own($cv);
        $cv->load(['experiences', 'educations', 'skills', 'user']);
        return view($this->tplView($cv->template_slug), ['cv' => $cv, 'layout' => 'layouts.render']);
    }

    public function publicByUuid($token)
    {
        $cv = Cv::where('public_uuid', $token)->where('status', 'published')->firstOrFail();
        $cv->load(['experiences', 'educations', 'skills', 'user']);
        return view($this->tplView($cv->template_slug), ['cv' => $cv, 'layout' => 'layouts.render']);
    }

    public function togglePublish(Cv $cv)
    {
        $this->own($cv);
        if ($cv->status === 'published') {
            $cv->update(['status' => 'draft']);
            return back()->with('success', 'CV ditarik sebagai draft.');
        }
        $errors = $cv->publishingErrors();
        if (!empty($errors)) return back()->withErrors($errors);
        if (empty($cv->public_uuid)) $cv->public_uuid = Str::uuid()->toString();
        $cv->status = 'published';
        $cv->save();
        return back()->with('success', 'CV berhasil dipublikasikan.');
    }

    private function tplView(?string $slug): string
    {
        $slug = trim((string) $slug);

        if ($slug !== '') {
            $view = "cv.templates.$slug";
            if (view()->exists($view)) {
                return $view;
            }

            $legacyView = "templates.$slug";
            if (view()->exists($legacyView)) {
                return $legacyView;
            }
        }

        if (view()->exists('cv.templates.default')) {
            return 'cv.templates.default';
        }

        return 'templates.default';
    }

    private function own(Cv $cv): void
    {
        if ($cv->user_id !== auth()->id()) abort(403);
    }
}