<?php

namespace App\Http\Controllers;

use App\Enums\RelationshipLevels;
use App\GraphModels\Covers;
use App\GraphModels\GraphModel;
use App\GraphModels\Course;
use App\GraphModels\KnowledgeArea;
use App\GraphModels\Teaches;
use App\GraphModels\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Helpers\Helpers;

class KnowledgeAreaController extends Controller
{
    public function form(Request $request): Response
    {
        $filter = $request->filter;

        $initialKnowledgeAreas = $filter
            ? KnowledgeArea::where(
                [
                    ['title', 'contains', $filter],
                    ['description', 'contains', $filter],
                ],
                'OR',
            )
                ->orderBy('title')
                ->get()
            : KnowledgeArea::all('title');

        $initialKnowledgeAreas = Helpers::paginate(
            $request->page,
            $initialKnowledgeAreas,
        );

        return Inertia::render('KnowledgeArea/Form', [
            'initialKnowledgeAreas' => $initialKnowledgeAreas,
            'allTopics' => Topic::all('name'),
            'levels' => RelationshipLevels::cases(),
            'filter' => $filter,
        ]);
    }

    public function getTopics(Request $request, string $knowledgeAreaId): Collection
    {
        return KnowledgeArea::getRelatedNodes($knowledgeAreaId, new Covers());
    }

    public function store(Request $request): RedirectResponse
    {
        $id = KnowledgeArea::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        KnowledgeArea::update($id, [
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        KnowledgeArea::delete($id);

        return back();
    }
}
