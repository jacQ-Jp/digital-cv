<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Support\CvTemplateRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;
use Throwable;

class CvWizardController extends Controller
{
    public function show(Cv $cv): View
    {
        $this->authorizeCv($cv);

        $cv->load(['template']);

        return view('cvs.wizard', [
            'cv' => $cv,
        ]);
    }

    public function state(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $cv->load([
            'template',
            'experiences' => fn ($q) => $q->orderBy('id'),
            'educations' => fn ($q) => $q->orderBy('id'),
            'skills' => fn ($q) => $q->orderBy('id'),
        ]);

        return response()->json($this->serializeState($cv));
    }

    public function savePersonal(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'personal_name' => ['nullable', 'string', 'max:255'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'summary' => ['nullable', 'string'],
            'remove_photo' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if (($data['remove_photo'] ?? false) && $cv->photo_path) {
            Storage::disk('public')->delete($cv->photo_path);
            $cv->photo_path = null;
        }

        if ($request->hasFile('photo')) {
            if ($cv->photo_path) {
                Storage::disk('public')->delete($cv->photo_path);
            }

            $cv->photo_path = $request->file('photo')->store('cv-photos', 'public');
        }

        $cv->fill([
            'title' => $data['title'] ?? $cv->title,
            'personal_name' => $data['personal_name'] ?? $cv->personal_name,
            'personal_email' => $data['personal_email'] ?? $cv->personal_email,
            'summary' => $data['summary'] ?? null,
            'status' => 'draft',
        ])->save();

        $cv->refresh()->load(['template', 'experiences', 'educations', 'skills']);

        return response()->json($this->serializeState($cv));
    }

    public function saveExperiences(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $payload = $request->validate([
            'experiences' => ['array'],
            'experiences.*.id' => ['nullable', 'integer'],
            'experiences.*.position' => ['required', 'string', 'max:255'],
            'experiences.*.company' => ['required', 'string', 'max:255'],
            'experiences.*.start_date' => ['required', 'date'],
            'experiences.*.end_date' => ['nullable', 'date'],
            'experiences.*.description' => ['nullable', 'string'],
        ]);

        $items = $payload['experiences'] ?? [];
        $savedIds = [];

        foreach ($items as $item) {
            $record = null;

            if (! empty($item['id'])) {
                $record = $cv->experiences()->whereKey($item['id'])->first();
            }

            if (! $record) {
                $record = $cv->experiences()->create([
                    'position' => $item['position'],
                    'company' => $item['company'],
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'] ?? null,
                    'description' => $item['description'] ?? null,
                ]);
            } else {
                $record->update([
                    'position' => $item['position'],
                    'company' => $item['company'],
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'] ?? null,
                    'description' => $item['description'] ?? null,
                ]);
            }

            $savedIds[] = $record->id;
        }

        $cv->experiences()->when(
            count($savedIds) > 0,
            fn ($q) => $q->whereNotIn('id', $savedIds),
            fn ($q) => $q
        )->delete();

        $cv->status = 'draft';
        $cv->save();

        $cv->refresh()->load(['template', 'experiences', 'educations', 'skills']);

        return response()->json($this->serializeState($cv));
    }

    public function saveEducations(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $payload = $request->validate([
            'educations' => ['array'],
            'educations.*.id' => ['nullable', 'integer'],
            'educations.*.school' => ['required', 'string', 'max:255'],
            'educations.*.degree' => ['required', 'string', 'max:255'],
            'educations.*.year' => ['required', 'string', 'max:50'],
        ]);

        $items = $payload['educations'] ?? [];
        $savedIds = [];

        foreach ($items as $item) {
            $record = null;

            if (! empty($item['id'])) {
                $record = $cv->educations()->whereKey($item['id'])->first();
            }

            if (! $record) {
                $record = $cv->educations()->create([
                    'school' => $item['school'],
                    'degree' => $item['degree'],
                    'year' => $item['year'],
                ]);
            } else {
                $record->update([
                    'school' => $item['school'],
                    'degree' => $item['degree'],
                    'year' => $item['year'],
                ]);
            }

            $savedIds[] = $record->id;
        }

        $cv->educations()->when(
            count($savedIds) > 0,
            fn ($q) => $q->whereNotIn('id', $savedIds),
            fn ($q) => $q
        )->delete();

        $cv->status = 'draft';
        $cv->save();

        $cv->refresh()->load(['template', 'experiences', 'educations', 'skills']);

        return response()->json($this->serializeState($cv));
    }

    public function saveSkills(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $payload = $request->validate([
            'skills' => ['array'],
            'skills.*.id' => ['nullable', 'integer'],
            'skills.*.name' => ['required', 'string', 'max:255'],
            'skills.*.level' => ['nullable', 'string', 'max:255'],
        ]);

        $items = $payload['skills'] ?? [];
        $savedIds = [];

        foreach ($items as $item) {
            $record = null;

            if (! empty($item['id'])) {
                $record = $cv->skills()->whereKey($item['id'])->first();
            }

            if (! $record) {
                $record = $cv->skills()->create([
                    'name' => $item['name'],
                    'level' => $item['level'] ?? null,
                ]);
            } else {
                $record->update([
                    'name' => $item['name'],
                    'level' => $item['level'] ?? null,
                ]);
            }

            $savedIds[] = $record->id;
        }

        $cv->skills()->when(
            count($savedIds) > 0,
            fn ($q) => $q->whereNotIn('id', $savedIds),
            fn ($q) => $q
        )->delete();

        $cv->status = 'draft';
        $cv->save();

        $cv->refresh()->load(['template', 'experiences', 'educations', 'skills']);

        return response()->json($this->serializeState($cv));
    }

    public function saveReview(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $data = $request->validate([
            'status' => ['required', 'in:draft,published'],
        ]);

        if ($data['status'] === 'published' && ! $cv->public_uuid) {
            $cv->public_uuid = (string) Str::uuid();
        }

        $cv->status = $data['status'];
        $cv->save();

        $cv->refresh()->load(['template', 'experiences', 'educations', 'skills']);

        return response()->json($this->serializeState($cv));
    }

    public function preview(Cv $cv): View
    {
        $this->authorizeCv($cv);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        return app(CvTemplateRenderer::class)->render($cv, [
            'previewMode' => true,
        ]);
    }

    public function downloadPdf(Cv $cv)
    {
        $this->authorizeCv($cv);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        $html = app(CvTemplateRenderer::class)
            ->render($cv, ['previewMode' => true])
            ->render();

        try {
            $pdf = Browsershot::html($html)
                ->showBackground()
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="cv-'.$cv->id.'.pdf"',
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'PDF generation failed. Ensure Chromium/Node runtime is available.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    private function authorizeCv(Cv $cv): void
    {
        abort_unless($cv->user_id === Auth::id(), 403);
    }

    private function serializeState(Cv $cv): array
    {
        return [
            'cv' => [
                'id' => $cv->id,
                'title' => $cv->title,
                'summary' => $cv->summary,
                'template_slug' => $cv->template_slug,
                'template_name' => $cv->template?->name ?? $cv->template_slug,
                'status' => $cv->status,
                'personal_name' => $cv->personal_name,
                'personal_email' => $cv->personal_email,
                'photo_url' => $cv->photo_path ? Storage::disk('public')->url($cv->photo_path) : null,
                'public_uuid' => $cv->public_uuid,
                'public_url' => route('cvs.public', ['token' => $cv->public_uuid ?: $cv->id]),
            ],
            'experiences' => $cv->experiences->map(fn ($item) => [
                'id' => $item->id,
                'position' => $item->position,
                'company' => $item->company,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'description' => $item->description,
            ])->values(),
            'educations' => $cv->educations->map(fn ($item) => [
                'id' => $item->id,
                'school' => $item->school,
                'degree' => $item->degree,
                'year' => $item->year,
            ])->values(),
            'skills' => $cv->skills->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'level' => $item->level,
            ])->values(),
        ];
    }
}
