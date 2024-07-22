<?php

namespace App\Http\Controllers;

use App\Enums\RelationshipLevels;
use App\GraphModels\Covers;
use App\GraphModels\GraphModel;
use App\GraphModels\Course;
use App\GraphModels\Teaches;
use App\GraphModels\Topic;
use App\Http\Helpers\Helpers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Laudis\Neo4j\Exception\Neo4jException;
use Illuminate\Support\Facades\Validator;
use Closure;

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

        $initialTopics = Helpers::paginate($request->page, $initialTopics);

        return Inertia::render('Topic/Form', [
            'initialTopics' => $initialTopics,
            'allCourses' => Course::all('number'),
            'levels' => RelationshipLevels::cases(),
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
            'levels' => RelationshipLevels::cases(),
        ]);
    }

    public function getCourses(Request $request, string $topicId): Collection
    {
        return Topic::getRelatedNodes($topicId, new Teaches(), 'number');
    }

    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'name' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    /* Note: neo4j uniqueness constraint is case-sensitive. e.g. if the name "Tableau" exists,
                            creating a node with name "tableau" will not throw a ConstraintValidationFailed error.
                            The "contains" collection method is case-sensitive
                        */
                    if (Topic::all()->pluck($attribute)->contains($value)) {
                        $fail("The $attribute has to be unique.");
                    }
                },
            ],
        ])->validate();

        $id = Topic::create(['name' => $request->name]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Validator::make($request->all(), [
            'name' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) use (
                    $id,
                ) {
                    $oldTopicName = Topic::find($id)->get($attribute);

                    if (
                        Topic::all()
                            ->where($attribute, '<>', $oldTopicName)
                            ->pluck('name')
                            ->contains($value)
                    ) {
                        $fail("The $attribute has to be unique.");
                    }
                },
            ],
        ])->validate();

        Topic::update($id, ['name' => $request->name]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Topic::delete($id);

        return back();
    }
}
