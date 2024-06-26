<?php

namespace App\Http\Controllers;

use App\Enums\CourseTopicEdgeWeights;
use App\GraphModels\Course;
use App\GraphModels\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Laudis\Neo4j\Contracts\TransactionInterface;

class TopicController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('Topic/Form', [
            'courses' => Course::getAll(),
            'courseTopicEdgeWeights' => CourseTopicEdgeWeights::cases(),
        ]);
    }

    public function getTopics(Request $request, string $courseId): array
    {
        return Topic::getTopicsForCourse($courseId);
    }

    public function store(Request $request, string $courseId): RedirectResponse
    {
        $id = Topic::create(
            $courseId,
            $request->name,
            $request->coverage_level,
        );

        return back()->with('data', $id);
    }

    public function update(Request $request, string $topicId): RedirectResponse
    {
        Topic::update($topicId, $request->name);

        return back();
    }

    public function delete(Request $request, string $topicId): RedirectResponse
    {
        Topic::delete($topicId);

        return back();
    }
}
