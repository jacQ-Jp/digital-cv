<?php

use App\Http\Controllers\CvController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('roles', RoleController::class);
Route::resource('templates', TemplateController::class);
Route::resource('cvs', CvController::class);
Route::resource('experiences', ExperienceController::class);
Route::resource('educations', EducationController::class);
