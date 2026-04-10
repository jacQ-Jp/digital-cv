<?php

use App\Http\Controllers\CvController;
use App\Http\Controllers\CvEducationController;
use App\Http\Controllers\CvExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public CV page (only published)
Route::get('/p/{cv}', [CvController::class, 'public'])->name('cvs.public');

Route::middleware('auth')->group(function () {
    // Step 1: template selection
    Route::get('/cv-builder/templates', [CvController::class, 'selectTemplate'])->name('cv-builder.templates');
    Route::post('/cv-builder/templates', [CvController::class, 'saveTemplateSelection'])->name('cv-builder.templates.save');

    Route::get('/cvs/{cv}/render', [CvController::class, 'render'])->name('cvs.render');

    Route::resource('cvs', CvController::class);

    Route::resource('cvs.experiences', CvExperienceController::class);
    Route::resource('cvs.educations', CvEducationController::class);
});

// Admin (template only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class);
});

Route::resource('roles', RoleController::class);
Route::resource('experiences', ExperienceController::class);
Route::resource('educations', EducationController::class);
