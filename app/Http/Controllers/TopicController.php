<?php

namespace App\Http\Controllers;

use App\Enums\CoverageLevels;
use App\GraphModels\GraphModel;
use App\GraphModels\Course;
use App\GraphModels\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class TopicController extends Controller
{
    public function form(Request $request): Response
    {
        $filter = $request->filter;

        $initialTopics = $filter
            ? Topic::where([['name', 'contains', $filter]])
                ->orderBy('name')
                ->get()
            : Topic::all('name');

        $page = $request->page;

        $pageName = 'page';
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
        $perPage = 5;

        $initialTopics = (new LengthAwarePaginator(
            $initialTopics->forPage($page, $perPage),
            $initialTopics->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ))->withQueryString();

        return Inertia::render('Topic/Form', [
            'initialTopics' => $initialTopics,
            'allCourses' => Course::all('number'),
            'coverageLevels' => CoverageLevels::cases(),
            'filter' => $filter,
        ]);
    }

    public function visualization(): Response
    {
        $coursesWithTopics = Course::getAllWithTopics();

        return Inertia::render('Topic/Visualization', [
            'courses' => GraphModel::getUniqueResults(
                array_column($coursesWithTopics->toArray(), 'course'),
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
