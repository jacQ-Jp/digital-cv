<?php

namespace App\Console\Commands;

use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\Browsershot\Browsershot;
use stdClass;
use Throwable;

class GenerateTemplateThumbnails extends Command
{
    protected $signature = 'templates:generate-thumbnails
                            {--slug=* : Generate only specific template slug(s)}
                            {--width=1240 : Screenshot width (A4-ish ratio)}
                            {--height=1754 : Screenshot height (A4-ish ratio)}
                            {--force : Regenerate even if image exists}';

    protected $description = 'Generate PNG thumbnail previews for active CV templates';

    public function handle(): int
    {
        $templates = Template::query()
            ->where('is_active', true)
            ->when(
                !empty($this->option('slug')),
                fn ($query) => $query->whereIn('slug', (array) $this->option('slug'))
            )
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        if ($templates->isEmpty()) {
            $this->warn('No active templates found.');

            return self::SUCCESS;
        }

        $outputDir = public_path('images/templates');
        File::ensureDirectoryExists($outputDir);

        $chromePath = $this->detectChromePath();
        if ($chromePath === null) {
            $this->error('Chrome/Chromium binary not found. Set BROWSERSHOT_CHROME_PATH in .env.');

            return self::FAILURE;
        }

        $width = (int) $this->option('width');
        $height = (int) $this->option('height');
        $force = (bool) $this->option('force');

        $success = 0;
        $failed = 0;

        foreach ($templates as $template) {
            $view = $this->resolveTemplateView($template->slug);
            if ($view === null) {
                $this->warn("[skip] view not found for slug '{$template->slug}'");
                $failed++;

                continue;
            }

            $relativePath = 'images/templates/'.$template->slug.'.png';
            $absolutePath = public_path($relativePath);

            if (!$force && File::exists($absolutePath)) {
                $this->line("[skip] already exists: {$relativePath}");

                if ($template->thumbnail !== '/'.$relativePath) {
                    $template->update(['thumbnail' => '/'.$relativePath]);
                }

                $success++;
                continue;
            }

            try {
                $html = view($view, [
                    'cv' => $this->makeSampleCv($template->slug),
                    'previewMode' => true,
                ])->render();

                Browsershot::html($html)
                    ->setNodeModulePath(base_path('node_modules'))
                    ->setChromePath($chromePath)
                    ->windowSize($width, $height)
                    ->deviceScaleFactor(1)
                    ->waitUntilNetworkIdle()
                    ->setDelay(1200)
                    ->timeout(120)
                    ->save($absolutePath);

                $template->update(['thumbnail' => '/'.$relativePath]);

                $this->info("[ok] {$template->slug} -> {$relativePath}");
                $success++;
            } catch (Throwable $e) {
                $this->error("[fail] {$template->slug}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->line("Done. success={$success}, failed={$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolveTemplateView(string $slug): ?string
    {
        $aliases = [
            'minimal' => 'minimalist',
            'profile_sidebar' => 'profile-sidebar',
        ];

        $normalized = $aliases[$slug] ?? $slug;

        $candidates = [
            "cv.templates.{$normalized}",
            'cv.templates.'.str_replace('-', '_', $normalized),
            'cv.templates.'.str_replace('_', '-', $normalized),
        ];

        foreach (array_unique($candidates) as $view) {
            if (view()->exists($view)) {
                return $view;
            }
        }

        return null;
    }

    private function detectChromePath(): ?string
    {
        $fromEnv = env('BROWSERSHOT_CHROME_PATH');
        if (is_string($fromEnv) && $fromEnv !== '' && File::exists($fromEnv)) {
            return $fromEnv;
        }

        $candidates = [
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/Applications/Chromium.app/Contents/MacOS/Chromium',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function makeSampleCv(string $templateSlug): stdClass
    {
        $cv = new stdClass();
        $cv->template_slug = $templateSlug;
        $cv->title = 'Senior Product Engineer';
        $cv->summary = 'Product-minded engineer with 8+ years of experience building high-performance web applications, improving conversion funnels, and mentoring cross-functional teams from idea to production.';
        $cv->status = 'published';

        $cv->user = (object) [
            'name' => 'Alex Johnson',
            'email' => 'alex.johnson@example.com',
        ];

        $cv->phone = '+62 812-3456-7789';
        $cv->location = 'Bandung, Indonesia';
        $cv->website = 'alexjohnson.dev';
        $cv->linkedin = 'linkedin.com/in/alex-johnson';
        $cv->github = 'github.com/alexjohnson';

        $cv->experiences = new Collection([
            (object) [
                'position' => 'Staff Product Engineer',
                'company' => 'Orbit Commerce',
                'start_date' => '2023',
                'end_date' => 'Present',
                'description' => 'Led checkout optimization and search revamp, increasing conversion by 18% and reducing average page load time by 41%.',
            ],
            (object) [
                'position' => 'Senior Full-Stack Engineer',
                'company' => 'Cloudify Labs',
                'start_date' => '2020',
                'end_date' => '2023',
                'description' => 'Built modular API and analytics pipelines used by 150k+ monthly users, while mentoring 4 junior engineers.',
            ],
            (object) [
                'position' => 'Software Engineer',
                'company' => 'NodePeak Studio',
                'start_date' => '2018',
                'end_date' => '2020',
                'description' => 'Implemented CI/CD and test automation, cutting release cycle from bi-weekly to daily deployments.',
            ],
            (object) [
                'position' => 'Frontend Developer',
                'company' => 'PixelCraft Agency',
                'start_date' => '2016',
                'end_date' => '2018',
                'description' => 'Delivered responsive marketing websites and dashboard interfaces for 20+ SME clients.',
            ],
        ]);

        $cv->educations = new Collection([
            (object) [
                'school' => 'Institut Teknologi Nasional',
                'degree' => 'B.Sc. Computer Science',
                'year' => '2016',
            ],
            (object) [
                'school' => 'Dicoding Academy',
                'degree' => 'Advanced Web Performance Certification',
                'year' => '2021',
            ],
            (object) [
                'school' => 'Google Career Certificates',
                'degree' => 'UX Design Foundations',
                'year' => '2022',
            ],
        ]);

        $cv->skills = new Collection([
            (object) ['name' => 'Laravel', 'level' => 'Advanced'],
            (object) ['name' => 'PHP', 'level' => 'Advanced'],
            (object) ['name' => 'Vue.js', 'level' => 'Advanced'],
            (object) ['name' => 'React', 'level' => 'Intermediate'],
            (object) ['name' => 'TypeScript', 'level' => 'Advanced'],
            (object) ['name' => 'PostgreSQL', 'level' => 'Advanced'],
            (object) ['name' => 'Redis', 'level' => 'Intermediate'],
            (object) ['name' => 'Docker', 'level' => 'Advanced'],
            (object) ['name' => 'CI/CD', 'level' => 'Advanced'],
            (object) ['name' => 'System Design', 'level' => 'Advanced'],
            (object) ['name' => 'Product Analytics', 'level' => 'Intermediate'],
        ]);

        return $cv;
    }
}
