<?php

namespace App\Support;

use App\Models\Cv;
use App\Models\Template;
use Illuminate\View\View;

class CvTemplateRenderer
{
    public function resolveView(Cv $cv): string
    {
        $slug = $cv->template_slug;
        if ($slug && $this->canRenderTemplateSlug($cv, $slug)) {
            return "cv.templates.$slug";
        }

        $defaultSlug = Template::query()->where('is_default', true)->value('slug');
        if ($defaultSlug && view()->exists("cv.templates.$defaultSlug")) {
            return "cv.templates.$defaultSlug";
        }

        $firstActiveSlug = Template::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->value('slug');
        if ($firstActiveSlug && view()->exists("cv.templates.$firstActiveSlug")) {
            return "cv.templates.$firstActiveSlug";
        }

        if (view()->exists('cv.templates.default')) {
            return 'cv.templates.default';
        }

        abort(500, 'No template view available.');
    }

    public function render(Cv $cv, array $extraData = []): View
    {
        $cv->loadMissing(['user', 'template', 'experiences', 'educations', 'skills']);

        $view = $this->resolveView($cv);

        return view($view, array_merge(['cv' => $cv], $extraData));
    }

    private function canRenderTemplateSlug(Cv $cv, string $slug): bool
    {
        $viewName = "cv.templates.$slug";
        if (! view()->exists($viewName)) {
            return false;
        }

        $template = null;
        if ($cv->relationLoaded('template') && $cv->template && $cv->template->slug === $slug) {
            $template = $cv->template;
        }

        if (! $template) {
            $template = Template::query()->where('slug', $slug)->first();
        }

        return (bool) ($template?->is_active);
    }
}
