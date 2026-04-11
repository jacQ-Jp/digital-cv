<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\StoreExperienceRequest;
use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CvApiExampleController extends Controller
{
    /**
     * Contoh eager loading: ambil CV beserta relasi User/Role, Template, Experiences/Educations/Skills.
     */
    public function show(int $id): JsonResponse
    {
        $cv = Cv::query()
            ->with([
                'user.role',
                'template',
                'experiences',
                'educations',
                'skills',
            ])
            ->findOrFail($id);

        return response()->json($cv);
    }

    /**
     * Simpan CV.
     */
    public function store(StoreCvRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cv = Cv::create([
            'user_id' => $request->user()?->id ?? $data['user_id'] ?? null,
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'template_slug' => $data['template_slug'],
            'status' => $data['status'] ?? 'draft',
        ]);

        return response()->json($cv, 201);
    }

    /**
     * Simpan multiple experiences menggunakan foreach.
     */
    public function storeExperiences(StoreExperienceRequest $request, Cv $cv): JsonResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($cv, $validated) {
            foreach ($validated['experiences'] as $experience) {
                $cv->experiences()->create($experience);
            }
        });

        $cv->load('experiences');

        return response()->json($cv);
    }
}
