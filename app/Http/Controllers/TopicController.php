<?php

namespace App\Http\Controllers;

use App\Enums\CoverageLevels;
use App\GraphModels\GraphModel;
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
            'initialTopics' => Topic::all('name'),
            'allCourses' => Course::all('number'),
            'coverageLevels' => CoverageLevels::cases(),
        ]);
    }

    public function visualization(): Response
    {
        $coursesWithTopics = Course::getAllWithTopics();

        return Inertia::render('Topic/Visualization', [
            'courses' => GraphModel::getUniqueResults(
                array_column($coursesWithTopics, 'course'),
            ),
            'topics' => Topic::all('name'),
            'coursesWithTopics' => $coursesWithTopics,
            'coverageLevels' => CoverageLevels::cases(),
        ]);
    }

    public function getCourses(Request $request, string $topicId): array
    {
        return Topic::getCourses($topicId);
    }

    public function store(Request $request): RedirectResponse
    {
        $id = Topic::create(['name' => $request->name]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Topic::update($id, ['name' => $request->name]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Topic::delete($id);

        return back();
    }
}
