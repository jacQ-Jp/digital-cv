<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\Template;
use App\Support\CvTemplateRenderer;
use Illuminate\Support\Collection;
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
    private const PHOTO_ENABLED_TEMPLATE_SLUGS = [
        'classic',
        'modern',
        'sidebar',
        'mono-poster',
    ];

    private const ACCENT_ENABLED_TEMPLATE_SLUGS = [
        'classic',
        'modern',
        'minimalist',
        'minimal',
    ];

    private const ACCENT_COLORS = [
        '#7C3AED',
        '#0EA5A4',
        '#3B82F6',
        '#EA580C',
        '#334155',
        '#166534',
        '#BE123C',
    ];

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

        $supportsPhoto = $this->templateSupportsPhoto($cv);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'personal_name' => ['nullable', 'string', 'max:255'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'summary' => ['nullable', 'string'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'remove_photo' => ['nullable', 'boolean'],
            'photo' => $supportsPhoto ? ['nullable', 'image', 'max:2048'] : ['prohibited'],
        ]);

        if ($supportsPhoto && ($data['remove_photo'] ?? false) && $cv->photo_path) {
            Storage::disk('public')->delete($cv->photo_path);
            $cv->photo_path = null;
        }

        if ($supportsPhoto && $request->hasFile('photo')) {
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
            'accent_color' => $this->normalizeAccentColor($data['accent_color'] ?? $cv->accent_color),
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
                    'level' => null,
                ]);
            } else {
                $record->update([
                    'name' => $item['name'],
                    'level' => null,
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

        if ($data['status'] === 'published') {
            $publishErrors = $cv->publishingErrors();
            if (! empty($publishErrors)) {
                return response()->json([
                    'message' => 'Cannot publish CV. Complete required personal fields first.',
                    'errors' => $publishErrors,
                ], 422);
            }

            if (! $cv->public_uuid) {
                $cv->public_uuid = (string) Str::uuid();
            }
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

    public function livePreview(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'personal_name' => ['nullable', 'string', 'max:255'],
            'personal_email' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'photo_url' => ['nullable', 'string', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
            'experiences' => ['nullable', 'array'],
            'experiences.*.id' => ['nullable'],
            'experiences.*.position' => ['nullable', 'string', 'max:255'],
            'experiences.*.company' => ['nullable', 'string', 'max:255'],
            'experiences.*.start_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.end_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.description' => ['nullable', 'string'],
            'educations' => ['nullable', 'array'],
            'educations.*.id' => ['nullable'],
            'educations.*.school' => ['nullable', 'string', 'max:255'],
            'educations.*.degree' => ['nullable', 'string', 'max:255'],
            'educations.*.year' => ['nullable', 'string', 'max:50'],
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['nullable'],
            'skills.*.name' => ['nullable', 'string', 'max:255'],
        ]);

        $cv->loadMissing(['user', 'template']);

        $previewCv = $this->buildCvFromPayload($cv, $payload);

        $renderer = app(CvTemplateRenderer::class);
        $resolvedView = $renderer->resolveView($previewCv);
        $resolvedSlug = preg_replace('/^(cv\.)?templates\./', '', $resolvedView) ?: 'default';

        $html = view($resolvedView, [
            'cv' => $previewCv,
            'previewMode' => true,
            'layout' => 'layouts.thumb',
        ])->render();

        return response()->json([
            'html' => $html,
            'template_slug' => $previewCv->template_slug,
            'resolved_slug' => $resolvedSlug,
            'template_supports_photo' => $this->templateSupportsPhotoSlug($resolvedSlug),
            'template_supports_accent' => $this->templateSupportsAccentSlug($resolvedSlug),
        ]);
    }

    public function downloadPdf(Cv $cv)
    {
        $this->authorizeCv($cv);

        $cv->load(['user', 'template', 'experiences', 'educations', 'skills']);

        $embeddedPhoto = $this->embeddedPhotoDataUri($cv->photo_path);
        if ($embeddedPhoto !== null) {
            $cv->setAttribute('photo_preview_url', $embeddedPhoto);
        }

        $renderer = app(CvTemplateRenderer::class);
        $resolvedView = $renderer->resolveView($cv);

        $html = view($resolvedView, [
            'cv' => $cv,
            'previewMode' => true,
            'layout' => 'layouts.pdf',
        ])->render();

        try {
            $pdf = $this->buildPdfBrowsershot($html)->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="cv-'.$cv->id.'.pdf"',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('cvs.render', $cv)
                ->with('pdf_warning', 'PDF otomatis belum tersedia di runtime ini. Gunakan tombol Print / PDF sebagai fallback sementara.');
        }
    }

    public function switchTemplate(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $data = $request->validate([
            'template_slug' => ['required', 'string', 'max:255', 'exists:templates,slug'],
        ]);

        $template = Template::where('slug', $data['template_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        // Update CV dengan template baru, tetap preserve data yang sudah ada
        $cv->update([
            'template_slug' => $template->slug,
        ]);

        // Refresh dan load semua relasi
        $cv->refresh()->load(['template', 'experiences', 'educations', 'skills']);

        return response()->json($this->serializeState($cv));
    }

    public function listTemplates(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $templates = Template::where('is_active', true)
            ->get()
            ->map(fn ($template) => [
                'slug' => $template->slug,
                'name' => $template->name,
                'description' => $template->description,
                'thumbnail_url' => $template->thumbnailPreviewUrl(),
            ])
            ->values()
            ->toArray();

        return response()->json([
            'templates' => $templates,
        ]);
    }

    private function buildPdfBrowsershot(string $html): Browsershot
    {
        $browsershot = Browsershot::html($html)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->emulateMedia('screen')
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->scale(1)
            ->setOption('preferCSSPageSize', true)
            ->addChromiumArguments([
                'disable-dev-shm-usage',
                'font-render-hinting=none',
            ])
            ->timeout(120);

        $nodeBinary = $this->firstExistingPath([
            (string) config('services.browsershot.node_binary', ''),
            '/opt/homebrew/bin/node',
            '/usr/local/bin/node',
            '/usr/bin/node',
        ]);
        if ($nodeBinary) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        $npmBinary = $this->firstExistingPath([
            (string) config('services.browsershot.npm_binary', ''),
            '/opt/homebrew/bin/npm',
            '/usr/local/bin/npm',
            '/usr/bin/npm',
        ]);
        if ($npmBinary) {
            $browsershot->setNpmBinary($npmBinary);
        }

        $chromePath = $this->firstExistingPath([
            (string) config('services.browsershot.chrome_path', ''),
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/Applications/Chromium.app/Contents/MacOS/Chromium',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
        ]);
        if ($chromePath) {
            $browsershot->setChromePath($chromePath);
        }

        if ((bool) config('services.browsershot.no_sandbox', false)) {
            $browsershot->noSandbox();
        }

        return $browsershot;
    }

    private function firstExistingPath(array $paths): ?string
    {
        foreach ($paths as $path) {
            $candidate = trim((string) $path);
            if ($candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
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
                'template_supports_photo' => $this->templateSupportsPhoto($cv),
                'template_supports_accent' => $this->templateSupportsAccent($cv),
                'status' => $cv->status,
                'personal_name' => $cv->personal_name,
                'personal_email' => $cv->personal_email,
                'accent_color' => $this->normalizeAccentColor($cv->accent_color),
                'photo_url' => $this->publicPhotoUrl($cv->photo_path),
                'public_uuid' => $cv->public_uuid,
                'public_url' => $cv->status === 'published' && $cv->public_uuid
                    ? route('cvs.public', ['token' => $cv->public_uuid])
                    : null,
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
            ])->values(),
        ];
    }

    private function buildCvFromPayload(Cv $cv, array $payload): Cv
    {
        $previewCv = $cv->replicate();
        $previewCv->id = $cv->id;

        $previewCv->forceFill([
            'template_slug' => $cv->template_slug,
            'title' => $payload['title'] ?? $cv->title,
            'personal_name' => $payload['personal_name'] ?? $cv->personal_name,
            'personal_email' => $payload['personal_email'] ?? $cv->personal_email,
            'summary' => $payload['summary'] ?? $cv->summary,
            'accent_color' => $this->normalizeAccentColor($payload['accent_color'] ?? $cv->accent_color),
            'status' => $cv->status,
            'photo_path' => $cv->photo_path,
            'public_uuid' => $cv->public_uuid,
            'user_id' => $cv->user_id,
        ]);

        $placeholderFlags = [];

        $previewCv->title = $this->valueOrPlaceholder(
            $previewCv->title,
            'Your Profession',
            $placeholderFlags,
            'title'
        );

        $previewCv->personal_name = $this->valueOrPlaceholder(
            $previewCv->personal_name,
            'Your Name',
            $placeholderFlags,
            'personal_name'
        );

        $previewCv->personal_email = $this->valueOrPlaceholder(
            $previewCv->personal_email,
            'your@email.com',
            $placeholderFlags,
            'personal_email'
        );

        $previewCv->summary = $this->valueOrPlaceholder(
            $previewCv->summary,
            'Write a short professional summary about yourself...',
            $placeholderFlags,
            'summary'
        );

        $removePhoto = (bool) ($payload['remove_photo'] ?? false);

        if ($this->templateSupportsPhoto($previewCv)) {
            if ($removePhoto) {
                $previewCv->setAttribute('photo_path', null);
                $previewCv->setAttribute('photo_preview_url', null);
            } elseif (array_key_exists('photo_url', $payload) && is_string($payload['photo_url']) && $payload['photo_url'] !== '') {
                $previewCv->setAttribute('photo_preview_url', $payload['photo_url']);
            }
        }

        $template = null;
        if ($previewCv->template_slug) {
            $template = Template::query()->where('slug', $previewCv->template_slug)->first();
        }

        $experiences = $this->applyExperiencePlaceholders($this->mapExperienceCollection($payload['experiences'] ?? []));
        $educations = $this->applyEducationPlaceholders($this->mapEducationCollection($payload['educations'] ?? []));
        $skills = $this->applySkillPlaceholders($this->mapSkillCollection($payload['skills'] ?? []));

        $previewCv->setAttribute('preview_placeholder_flags', $placeholderFlags);
        $previewCv->setRelation('user', $cv->user);
        $previewCv->setRelation('template', $template);
        $previewCv->setRelation('experiences', $experiences);
        $previewCv->setRelation('educations', $educations);
        $previewCv->setRelation('skills', $skills);

        return $previewCv;
    }

    private function mapExperienceCollection(array $items): Collection
    {
        return collect($items)
            ->map(function (array $item) {
                return (object) [
                    'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : null,
                    'position' => $this->cleanString($item['position'] ?? null),
                    'company' => $this->cleanString($item['company'] ?? null),
                    'start_date' => $this->cleanString($item['start_date'] ?? null),
                    'end_date' => $this->cleanString($item['end_date'] ?? null),
                    'description' => $this->cleanString($item['description'] ?? null),
                    '_placeholder' => [],
                ];
            })
            ->values();
    }

    private function mapEducationCollection(array $items): Collection
    {
        return collect($items)
            ->map(function (array $item) {
                return (object) [
                    'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : null,
                    'school' => $this->cleanString($item['school'] ?? null),
                    'degree' => $this->cleanString($item['degree'] ?? null),
                    'year' => $this->cleanString($item['year'] ?? null),
                    '_placeholder' => [],
                ];
            })
            ->values();
    }

    private function mapSkillCollection(array $items): Collection
    {
        return collect($items)
            ->map(function (array $item) {
                return (object) [
                    'id' => is_numeric($item['id'] ?? null) ? (int) $item['id'] : null,
                    'name' => $this->cleanString($item['name'] ?? null),
                    '_placeholder' => [],
                ];
            })
            ->values();
    }

    private function applyExperiencePlaceholders(Collection $items): Collection
    {
        $presets = [
            [
                'position' => 'Your Role',
                'company' => 'Company Name',
                'start_date' => '2022',
                'end_date' => 'Present',
                'description' => 'Describe key responsibilities and measurable achievements in this role.',
            ],
            [
                'position' => 'Previous Role',
                'company' => 'Previous Company',
                'start_date' => '2020',
                'end_date' => '2022',
                'description' => 'Highlight projects or impact that show your professional growth.',
            ],
        ];

        // Keep preview visually balanced on a full page even when user data is still sparse.
        $targetCount = max($items->count(), 2);
        $result = collect();

        for ($index = 0; $index < $targetCount; $index++) {
            $row = $items->get($index) ?: (object) [
                'id' => null,
                'position' => null,
                'company' => null,
                'start_date' => null,
                'end_date' => null,
                'description' => null,
                '_placeholder' => [],
            ];

            $preset = $presets[min($index, count($presets) - 1)];
            $placeholder = [];

            $result->push((object) [
                'id' => $row->id,
                'position' => $this->valueOrPlaceholder($row->position ?? null, $preset['position'], $placeholder, 'position'),
                'company' => $this->valueOrPlaceholder($row->company ?? null, $preset['company'], $placeholder, 'company'),
                'start_date' => $this->valueOrPlaceholder($row->start_date ?? null, $preset['start_date'], $placeholder, 'start_date'),
                'end_date' => $this->valueOrPlaceholder($row->end_date ?? null, $preset['end_date'], $placeholder, 'end_date'),
                'description' => $this->valueOrPlaceholder($row->description ?? null, $preset['description'], $placeholder, 'description'),
                '_placeholder' => $placeholder,
            ]);
        }

        return $result;
    }

    private function applyEducationPlaceholders(Collection $items): Collection
    {
        // Keep preview visually balanced on a full page even when user data is still sparse.
        $targetCount = max($items->count(), 2);
        $result = collect();

        for ($index = 0; $index < $targetCount; $index++) {
            $row = $items->get($index) ?: (object) [
                'id' => null,
                'school' => null,
                'degree' => null,
                'year' => null,
                '_placeholder' => [],
            ];

            $placeholder = [];

            $result->push((object) [
                'id' => $row->id,
                'school' => $this->valueOrPlaceholder($row->school ?? null, 'School Name', $placeholder, 'school'),
                'degree' => $this->valueOrPlaceholder($row->degree ?? null, 'Degree', $placeholder, 'degree'),
                'year' => $this->valueOrPlaceholder($row->year ?? null, 'Year', $placeholder, 'year'),
                '_placeholder' => $placeholder,
            ]);
        }

        return $result;
    }

    private function applySkillPlaceholders(Collection $items): Collection
    {
        // Keep preview visually balanced on a full page even when user data is still sparse.
        $targetCount = max($items->count(), 6);
        $result = collect();

        for ($index = 0; $index < $targetCount; $index++) {
            $row = $items->get($index) ?: (object) [
                'id' => null,
                'name' => null,
                '_placeholder' => [],
            ];

            $placeholder = [];
            $skillPlaceholder = 'Skill '.($index + 1);

            $result->push((object) [
                'id' => $row->id,
                'name' => $this->valueOrPlaceholder($row->name ?? null, $skillPlaceholder, $placeholder, 'name'),
                '_placeholder' => $placeholder,
            ]);
        }

        return $result;
    }

    private function normalizeAccentColor(mixed $value): string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return $this->defaultAccentColor();
        }

        $normalized = strtoupper(trim((string) $value));

        if (! preg_match('/^#[0-9A-F]{6}$/', $normalized)) {
            return $this->defaultAccentColor();
        }

        if (! in_array($normalized, self::ACCENT_COLORS, true)) {
            return $this->defaultAccentColor();
        }

        return $normalized;
    }

    private function defaultAccentColor(): string
    {
        return self::ACCENT_COLORS[0];
    }

    private function cleanString(mixed $value): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function publicPhotoUrl(mixed $value): ?string
    {
        $photoPath = $this->cleanString($value);

        if ($photoPath === null) {
            return null;
        }

        if (
            preg_match('/^(https?:)?\/\//i', $photoPath)
            || str_starts_with($photoPath, 'data:')
            || str_starts_with($photoPath, 'blob:')
        ) {
            return $photoPath;
        }

        $normalizedPath = ltrim($photoPath, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            return '/'.$normalizedPath;
        }

        return '/storage/'.$normalizedPath;
    }

    private function embeddedPhotoDataUri(mixed $value): ?string
    {
        $photoPath = $this->cleanString($value);

        if ($photoPath === null) {
            return null;
        }

        if (str_starts_with($photoPath, 'data:')) {
            return $photoPath;
        }

        if (preg_match('/^(https?:)?\/\//i', $photoPath) || str_starts_with($photoPath, 'blob:')) {
            return null;
        }

        $relativePath = ltrim($photoPath, '/');
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = ltrim(substr($relativePath, strlen('storage/')), '/');
        }

        if ($relativePath === '' || ! Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $binary = @file_get_contents($absolutePath);
        if ($binary === false || $binary === '') {
            return null;
        }

        $mime = function_exists('mime_content_type')
            ? (mime_content_type($absolutePath) ?: 'image/jpeg')
            : 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    private function valueOrPlaceholder(mixed $value, string $placeholder, array &$placeholderFlags, string $key): string
    {
        $normalized = $this->cleanString($value);

        if ($normalized !== null) {
            return $normalized;
        }

        $placeholderFlags[$key] = true;

        return $placeholder;
    }

    private function templateSupportsPhoto(Cv $cv): bool
    {
        $slug = trim((string) $cv->template_slug);

        if ($slug === '') {
            try {
                $resolvedView = app(CvTemplateRenderer::class)->resolveView($cv);
                $slug = preg_replace('/^(cv\.)?templates\./', '', $resolvedView) ?: '';
            } catch (Throwable) {
                $slug = '';
            }
        }

        return $this->templateSupportsPhotoSlug($slug);
    }

    private function templateSupportsPhotoSlug(?string $slug): bool
    {
        $normalized = Str::lower(trim((string) $slug));

        return in_array($normalized, self::PHOTO_ENABLED_TEMPLATE_SLUGS, true);
    }

    private function templateSupportsAccent(Cv $cv): bool
    {
        $slug = trim((string) $cv->template_slug);

        if ($slug === '') {
            try {
                $resolvedView = app(CvTemplateRenderer::class)->resolveView($cv);
                $slug = preg_replace('/^(cv\.)?templates\./', '', $resolvedView) ?: '';
            } catch (Throwable) {
                $slug = '';
            }
        }

        return $this->templateSupportsAccentSlug($slug);
    }

    private function templateSupportsAccentSlug(?string $slug): bool
    {
        $normalized = Str::lower(trim((string) $slug));

        return in_array($normalized, self::ACCENT_ENABLED_TEMPLATE_SLUGS, true);
    }
}
