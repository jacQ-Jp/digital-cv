<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Models\Cv;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CvController extends Controller
{
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
    public function create(): View
    {
        $templates = Template::query()->where('is_active', true)->orderBy('name')->get();

        return view('cvs.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCvRequest $request): RedirectResponse
    {
        $cv = Cv::create([
            'user_id' => Auth::id(),
            'title' => $request->validated('title'),
            'summary' => $request->validated('summary'),
            'template_slug' => $request->validated('template_slug'),
            'status' => $request->validated('status'),
        ]);

        return redirect()->route('cvs.show', $cv);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['template', 'experiences', 'educations']);

        return view('cvs.show', compact('cv'));
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
