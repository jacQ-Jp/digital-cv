<?php

namespace App\Support;

use App\Models\Cv;
use App\Models\Template;
use Illuminate\View\View;

class CvTemplateRenderer
{
    public function resolveView(Cv $cv): string
    {
        $slug = trim((string) $cv->template_slug);
        if ($slug !== '') {
            $resolvedView = $this->resolveViewNameForSlug($slug);
            if ($resolvedView) {
                return $resolvedView;
            }
        }

        if (view()->exists('templates.default')) {
            return 'templates.default';
        }

        $defaultSlug = Template::query()->where('is_default', true)->value('slug');
        if ($defaultSlug) {
            $resolvedView = $this->resolveViewNameForSlug($defaultSlug);
            if ($resolvedView) {
                return $resolvedView;
            }
        }

        $firstTemplateSlug = Template::query()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->value('slug');
        if ($firstTemplateSlug) {
            $resolvedView = $this->resolveViewNameForSlug($firstTemplateSlug);
            if ($resolvedView) {
                return $resolvedView;
            }
        }

        abort(500, 'No template view available.');
    }

    public function render(Cv $cv, array $extraData = []): View
    {
        $cv->loadMissing(['user', 'template', 'experiences', 'educations', 'skills']);

        $view = $this->resolveView($cv);

        $embedded = $extraData['embedded'] ?? false;
        $layout = $embedded ? 'layouts.thumb' : 'layouts.render';

        return view($view, array_merge(['cv' => $cv, 'layout' => $layout], $extraData));
    }

    private function resolveViewNameForSlug(string $slug): ?string
    {
        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        $viewName = "templates.$slug";

        return view()->exists($viewName) ? $viewName : null;
    }
}
