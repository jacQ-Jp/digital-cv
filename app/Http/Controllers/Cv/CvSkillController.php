<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CvSkillController extends Controller
{
    public function index(Cv $cv): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $cv->load(['template', 'experiences', 'educations', 'skills']);
        $skills = $cv->skills()->latest()->get();

        return view('skills.index', compact('cv', 'skills'));
    }

    public function create(Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        return redirect()->route('cvs.wizard', $cv);
    }

    public function store(Request $request, Cv $cv): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
        ]);

        $cv->skills()->create($data);

        return redirect()->route('cvs.skills.index', $cv);
    }

    public function edit(Cv $cv, Skill $skill): View
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($skill->cv_id === $cv->id, 404);

        return view('skills.edit', compact('cv', 'skill'));
    }

    public function update(Request $request, Cv $cv, Skill $skill): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($skill->cv_id === $cv->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
        ]);

        $skill->update($data);

        return redirect()->route('cvs.skills.index', $cv);
    }

    public function destroy(Cv $cv, Skill $skill): RedirectResponse
    {
        abort_unless($cv->user_id === Auth::id(), 403);
        abort_unless($skill->cv_id === $cv->id, 404);

        $skill->delete();

        return redirect()->route('cvs.skills.index', $cv);
    }
}
