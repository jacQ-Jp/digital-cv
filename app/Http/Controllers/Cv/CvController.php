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

    public function previewTemplate(string $slug)
    {
        $cv = (object) [
            'title' => 'Professional Profile',
            'summary' => 'Experienced professional with a proven track record of delivering results.',
            'personal_name' => 'Alex Johnson',
            'personal_email' => 'alex.johnson@email.com',
            'personal_phone' => '+62 812 3456 7890',
            'personal_location' => 'Jakarta, Indonesia',
            'personal_linkedin' => 'linkedin.com/in/alexjohnson',
            'photo_path' => null,
            'template_slug' => $slug,
            'experiences' => collect([
                (object) ['position' => 'Senior Product Designer', 'company' => 'TechCorp Indonesia', 'start_date' => '2022', 'end_date' => null, 'description' => 'Led design system initiative serving 12 product teams.'],
                (object) ['position' => 'UI/UX Designer', 'company' => 'Creative Studio', 'start_date' => '2019', 'end_date' => '2022', 'description' => 'Designed and shipped 20+ mobile and web applications.'],
                (object) ['position' => 'Junior Designer', 'company' => 'StartUp Inc.', 'start_date' => '2017', 'end_date' => '2019', 'description' => null],
            ]),
            'educations' => collect([
                (object) ['school' => 'Universitas Indonesia', 'degree' => 'Bachelor of Design', 'year' => '2017'],
            ]),
            'skills' => collect([
                (object) ['name' => 'Figma'], (object) ['name' => 'UI Design'], (object) ['name' => 'UX Research'],
                (object) ['name' => 'Prototyping'], (object) ['name' => 'Design Systems'], (object) ['name' => 'Tailwind CSS'],
            ]),
            'user' => (object) ['name' => 'Alex Johnson', 'email' => 'alex.johnson@email.com'],
        ];

        return response()->view($this->tplView($slug), ['cv' => $cv, 'layout' => 'layouts.thumb'])
            ->header('Content-Type', 'text/html')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    public function render(Cv $cv)
    {
        $this->own($cv);
        $cv->load(['experiences', 'educations', 'skills', 'user']);
        return view($this->tplView($cv->template_slug), ['cv' => $cv, 'layout' => 'layouts.render']);
    }

    public function thumbnail(Cv $cv)
    {
        $this->own($cv);
        $cv->load(['experiences', 'educations', 'skills', 'user']);
        return response()->view($this->tplView($cv->template_slug), ['cv' => $cv, 'layout' => 'layouts.thumb'])
            ->header('Content-Type', 'text/html')
            ->header('X-Frame-Options', 'SAMEORIGIN');
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
        $view = "templates.{$slug}";
        return view()->exists($view) ? $view : 'templates.default';
    }

    private function own(Cv $cv): void
    {
        if ($cv->user_id !== auth()->id()) abort(403);
    }
}