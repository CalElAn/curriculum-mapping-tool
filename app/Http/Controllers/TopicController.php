<?php

namespace App\Http\Controllers;

use App\Enums\CourseTopicEdgeWeights;
use App\GraphModels\BaseGraphModel;
use App\GraphModels\Course;
use App\GraphModels\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TopicController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('Topic/Form', [
            'initialTopics' => BaseGraphModel::getAll('Topic', 'name'),
            'allCourses' => BaseGraphModel::getAll('Course', 'number'),
            'courseTopicEdgeWeights' => CourseTopicEdgeWeights::cases(),
        ]);
    }

    public function visualization(): Response
    {
        $coursesWithTopics = Course::getAllWithTopics();

        return Inertia::render('Topic/Visualization', [
            'courses' => BaseGraphModel::getUniqueResults(
                array_column($coursesWithTopics, 'course'),
            ),
            'topics' => BaseGraphModel::getUniqueResults(
                array_column($coursesWithTopics, 'topic'),
            ),
            'coursesWithTopics' => $coursesWithTopics,
            'courseTopicEdgeWeights' => CourseTopicEdgeWeights::cases(),
        ]);
    }

    public function getCourses(Request $request, string $topicId): array
    {
        return Topic::getCourses($topicId);
    }

    public function getTopics(Request $request, string $courseId): array
    {
        return [
            'allTopicNames' => Topic::getAllTopicNames(),
            'topics' => Topic::getTopicsForCourse($courseId),
        ];
    }

    public function store(Request $request, string $courseId): RedirectResponse
    {
        $id = Topic::create(
            $courseId,
            $request->name,
            $request->coverage_level,
        );

        return back()->with('data', [
            'id' => $id,
            'newNames' => Topic::getTopicsForCourse($courseId),
        ]);
    }

    public function update(
        Request $request,
        string $courseId,
        string $topicId,
    ): RedirectResponse {
        Topic::update(
            $courseId,
            $topicId,
            $request->name,
            $request->coverage_level,
        );

        return back();
    }

    public function destroy(Request $request, string $topicId): RedirectResponse
    {
        Topic::delete($topicId);

        return back();
    }
}
