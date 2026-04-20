<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Cv\CvController;
use App\Http\Controllers\Cv\CvEducationController;
use App\Http\Controllers\Cv\CvExperienceController;
use App\Http\Controllers\Cv\CvSkillController;
use App\Http\Controllers\Cv\CvWizardController;
use App\Models\Cv;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $templates = Template::query()
        ->where('is_active', true)
        ->orderByDesc('is_default')
        ->orderBy('name')
        ->get();

    $templateCategories = $templates
        ->pluck('slug')
        ->filter()
        ->map(fn ($slug) => strtolower(trim((string) $slug)))
        ->unique()
        ->values();

    $stats = [
        'templates' => $templates->count(),
        'published_cvs' => Cv::query()->where('status', 'published')->count(),
        'users' => User::query()->count(),
    ];

    return view('welcome', compact('templates', 'templateCategories', 'stats'));
});

// Authentication (manual)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Logged-in landing (optional convenience)
Route::middleware('auth')->get('/home', fn () => redirect()->route('cvs.index'))->name('home');

// Public CV page (only published)
Route::get('/p/{token}', [CvController::class, 'publicByUuid'])->name('cvs.public');

Route::middleware('auth')->group(function () {
    // Keep admin users out of the user CV dashboard.
    Route::get('/cvs', function () {
        if (auth()->user()?->role?->slug === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return app(\App\Http\Controllers\Cv\CvController::class)->index();
    })->name('cvs.index');

    // Step 1: template selection
    Route::get('/cv-builder/templates', [CvController::class, 'selectTemplate'])->name('cv-builder.templates');
    Route::post('/cv-builder/templates', [CvController::class, 'saveTemplateSelection'])->name('cv-builder.templates.save');

    // SPA-style wizard
    Route::get('/cvs/{cv}/wizard', [CvWizardController::class, 'show'])->name('cvs.wizard');
    Route::get('/cvs/{cv}/wizard/state', [CvWizardController::class, 'state'])->name('cvs.wizard.state');
    Route::put('/cvs/{cv}/wizard/personal', [CvWizardController::class, 'savePersonal'])->name('cvs.wizard.personal');
    Route::put('/cvs/{cv}/wizard/experiences', [CvWizardController::class, 'saveExperiences'])->name('cvs.wizard.experiences');
    Route::put('/cvs/{cv}/wizard/educations', [CvWizardController::class, 'saveEducations'])->name('cvs.wizard.educations');
    Route::put('/cvs/{cv}/wizard/skills', [CvWizardController::class, 'saveSkills'])->name('cvs.wizard.skills');
    Route::put('/cvs/{cv}/wizard/review', [CvWizardController::class, 'saveReview'])->name('cvs.wizard.review');
    Route::get('/cvs/{cv}/wizard/preview', [CvWizardController::class, 'preview'])->name('cvs.wizard.preview');
    Route::post('/cvs/{cv}/wizard/preview/live', [CvWizardController::class, 'livePreview'])->name('cvs.wizard.preview.live');
    Route::get('/cvs/{cv}/wizard/pdf', [CvWizardController::class, 'downloadPdf'])->name('cvs.wizard.pdf');
    Route::get('/cvs/{cv}/wizard/templates', [CvWizardController::class, 'listTemplates'])->name('cvs.wizard.templates');
    Route::patch('/cvs/{cv}/wizard/switch-template', [CvWizardController::class, 'switchTemplate'])->name('cvs.wizard.switch-template');

    Route::get('/cvs/{cv}/render', [CvController::class, 'render'])->name('cvs.render');
    Route::get('/cvs/{cv}/thumb', [CvController::class, 'thumb'])->name('cvs.thumb');

    // Advanced actions
    Route::patch('/cvs/{cv}/toggle-publish', [CvController::class, 'togglePublish'])->name('cvs.toggle-publish');
    Route::delete('/cvs/bulk-delete', [CvController::class, 'bulkDestroy'])->name('cvs.bulk-destroy');
    Route::delete('/cvs/destroy-all', [CvController::class, 'destroyAll'])->name('cvs.destroy-all');

    Route::resource('cvs', CvController::class)->except(['index']);

    Route::resource('cvs.experiences', CvExperienceController::class);
    Route::resource('cvs.educations', CvEducationController::class);
    Route::resource('cvs.skills', CvSkillController::class)->except(['show']);
});

// Admin (template only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.templates.index'))->name('dashboard');

    Route::delete('templates/bulk-delete', [\App\Http\Controllers\Admin\TemplateController::class, 'bulkDestroy'])
        ->name('templates.bulk-destroy');

    Route::delete('templates/destroy-all', [\App\Http\Controllers\Admin\TemplateController::class, 'destroyAll'])
        ->name('templates.destroy-all');

    Route::patch('templates/{template}/toggle-active', [\App\Http\Controllers\Admin\TemplateController::class, 'toggleActive'])
        ->name('templates.toggle-active');

    Route::patch('templates/{template}/set-default', [\App\Http\Controllers\Admin\TemplateController::class, 'setDefault'])
        ->name('templates.set-default');

    Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class);
});
