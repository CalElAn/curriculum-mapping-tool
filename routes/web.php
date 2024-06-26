<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/data-entry/topics/form', [TopicController::class, 'form'])->name('topics.form');
Route::get('/topics/form/{courseId}/get-topics', [TopicController::class, 'getTopics'])->name('topics.get_topics');
Route::post('/topics/form/{courseId}', [TopicController::class, 'store'])->name('topics.store');
Route::patch('/topics/form/{topicId}', [TopicController::class, 'update'])->name('topics.update');
Route::delete('/topics/form/{topicId}', [TopicController::class, 'delete'])->name('topics.delete');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
