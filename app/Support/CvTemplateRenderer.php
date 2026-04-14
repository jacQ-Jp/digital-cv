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
        $view = $slug ? "cv.templates.$slug" : null;

        if ($view && view()->exists($view)) {
            return $view;
        }

        $defaultSlug = Template::query()->where('is_default', true)->value('slug');
        if ($defaultSlug && view()->exists("cv.templates.$defaultSlug")) {
            return "cv.templates.$defaultSlug";
        }

        if (view()->exists('cv.templates.default')) {
            return 'cv.templates.default';
        }

        abort(500, 'No template view available.');
    }

    public function render(Cv $cv, array $extraData = []): View
    {
        $view = $this->resolveView($cv);

        return view($view, array_merge(['cv' => $cv], $extraData));
    }
}
