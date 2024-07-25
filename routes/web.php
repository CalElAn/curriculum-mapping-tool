<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoversController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GraphVisualizationController;
use App\Http\Controllers\KnowledgeAreaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeachesController;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/data-entry/topics/form', [TopicController::class, 'form'])->name('topics.form');
    Route::get('/topics/form/{topicId}/get-courses', [TopicController::class, 'getCourses'])->name('topics.get_courses');
    Route::get('/topics/form/{topicId}/get-knowledge-areas', [TopicController::class, 'getKnowledgeAreas'])->name('topics.get_knowledge_areas');
    Route::post('/topics/form', [TopicController::class, 'store'])->name('topics.store');
    Route::patch('/topics/form/{id}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/topics/form/{id}', [TopicController::class, 'destroy'])->name('topics.destroy');

    Route::get('/graph-visualization', GraphVisualizationController::class)->name('graph_visualization');

    Route::get('/data-entry/courses/form', [CourseController::class, 'form'])->name('courses.form');
    Route::get('/courses/form/{courseId}/get-topics', [CourseController::class, 'getTopics'])->name('courses.get_topics');
    Route::middleware('role:admin')->group(function () {
        Route::post('/courses/form', [CourseController::class, 'store'])->name('courses.store');
        Route::patch('/courses/form/{id}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/form/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    Route::get('/data-entry/knowledge-areas/form', [KnowledgeAreaController::class, 'form'])->name('knowledge_areas.form');
    Route::get('/knowledge-areas/form/{knowledgeAreaId}/get-topics', [KnowledgeAreaController::class, 'getTopics'])->name('knowledge_areas.get_topics');
    Route::middleware('role:admin')->group(function () {
        Route::post('/knowledge-areas/form', [KnowledgeAreaController::class, 'store'])->name('knowledge_areas.store');
        Route::patch('/knowledge-areas/form/{id}', [KnowledgeAreaController::class, 'update'])->name('knowledge_areas.update');
        Route::delete('/knowledge-areas/form/{id}', [KnowledgeAreaController::class, 'destroy'])->name('knowledge_areas.destroy');
    });

    Route::post('/teaches/form', [TeachesController::class, 'store'])->name('teaches.store');
    Route::patch('/teaches/form/{id}', [TeachesController::class, 'update'])->name('teaches.update');
    Route::delete('/teaches/form/{id}', [TeachesController::class, 'destroy'])->name('teaches.destroy');

    Route::post('/covers/form', [CoversController::class, 'store'])->name('covers.store');
    Route::patch('/covers/form/{id}', [CoversController::class, 'update'])->name('covers.update');
    Route::delete('/covers/form/{id}', [CoversController::class, 'destroy'])->name('covers.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
