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
            'topics' => BaseGraphModel::getAll('Topic', 'name'),
            'coursesWithTopics' => $coursesWithTopics,
            'courseTopicEdgeWeights' => CourseTopicEdgeWeights::cases(),
        ]);
    }

    public function getCourses(Request $request, string $topicId): array
    {
        return Topic::getCourses($topicId);
    }

    public function store(Request $request): RedirectResponse
    {
        $id = Topic::create($request->name);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Topic::update($id, $request->name);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Topic::delete($id);

        return back();
    }
}
