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
use App\Http\Helpers\Helpers;

class CourseController extends Controller
{
    public function form(Request $request): Response
    {
        $filter = $request->filter;

        $initialCourses = $filter
            ? Course::where(
                [
                    ['number', 'contains', $filter],
                    ['title', 'contains', $filter],
                ],
                false,
            )
                ->orderBy('number')
                ->get()
            : Course::all('number');

        $initialCourses = Helpers::paginate($request->page, $initialCourses);

        return Inertia::render('Course/Form', [
            'initialCourses' => $initialCourses,
            'allTopics' => Topic::all('name'),
            'coverageLevels' => CoverageLevels::cases(),
            'filter' => $filter,
        ]);
    }

    public function getTopics(Request $request, string $courseId): array
    {
        return Course::getTopics($courseId);
    }

    public function store(Request $request): RedirectResponse
    {
        $id = Course::create([
            'code' => $request->code,
            'title' => $request->title,
        ]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Course::update($id, [
            'code' => $request->code,
            'title' => $request->title,
        ]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Course::delete($id);

        return back();
    }
}
