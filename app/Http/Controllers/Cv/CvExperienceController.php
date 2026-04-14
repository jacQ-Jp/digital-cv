<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Models\Cv;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CvExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $experiences = $cv->experiences()->latest()->get();

        return view('experiences.index', compact('cv', 'experiences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['template', 'experiences', 'educations', 'skills']);

        return view('experiences.create', compact('cv'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExperienceRequest $request, Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->experiences()->create($request->validated());

        return redirect()->route('cvs.educations.create', $cv);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv, Experience $experience): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($experience->cv_id === $cv->id, 404);

        return view('experiences.show', compact('cv', 'experience'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cv $cv, Experience $experience): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($experience->cv_id === $cv->id, 404);

        return view('experiences.edit', compact('cv', 'experience'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperienceRequest $request, Cv $cv, Experience $experience): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($experience->cv_id === $cv->id, 404);

        $experience->update($request->validated());

        return redirect()->route('cvs.experiences.index', $cv);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cv $cv, Experience $experience): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($experience->cv_id === $cv->id, 404);

        $experience->delete();

        return redirect()->route('cvs.experiences.index', $cv);
    }
}
