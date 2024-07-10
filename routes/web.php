<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/data-entry/topics/form', [TopicController::class, 'form'])->name('topics.form');
Route::get('/topics/form/{topicId}/get-courses', [TopicController::class, 'getCourses'])->name('topics.get_courses');
Route::post('/topics/form', [TopicController::class, 'store'])->name('topics.store');
Route::patch('/topics/form/{id}', [TopicController::class, 'update'])->name('topics.update');
Route::delete('/topics/form/{id}', [TopicController::class, 'destroy'])->name('topics.destroy');

Route::get('/visualization/topics', [TopicController::class, 'visualization'])->name('topics.visualization');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
