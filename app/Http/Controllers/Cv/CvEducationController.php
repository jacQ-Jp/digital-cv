<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Models\Cv;
use App\Models\Education;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CvEducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $educations = $cv->educations()->latest()->get();

        return view('educations.index', compact('cv', 'educations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        return view('educations.create', compact('cv'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEducationRequest $request, Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->educations()->create($request->validated());

        return redirect()->route('cvs.educations.index', $cv);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv, Education $education): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($education->cv_id === $cv->id, 404);

        return view('educations.show', compact('cv', 'education'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cv $cv, Education $education): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($education->cv_id === $cv->id, 404);

        return view('educations.edit', compact('cv', 'education'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, Cv $cv, Education $education): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($education->cv_id === $cv->id, 404);

        $education->update($request->validated());

        return redirect()->route('cvs.educations.index', $cv);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cv $cv, Education $education): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($education->cv_id === $cv->id, 404);

        $education->delete();

        return redirect()->route('cvs.educations.index', $cv);
    }
}
