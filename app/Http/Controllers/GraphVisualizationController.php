<?php

namespace App\Http\Controllers;

use App\Enums\RelationshipLevels;
use App\GraphModels\Covers;
use App\GraphModels\GraphModel;
use App\GraphModels\IsPrerequisiteOf;
use App\GraphModels\Teaches;
use App\GraphModels\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GraphVisualizationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $coursesWithTopics = Teaches::allWithNodes();
        $knowledgeAreasWithTopics = Covers::allWithNodes();

        return Inertia::render('GraphVisualization', [
            'courses' => GraphModel::getUniqueResults(
                $coursesWithTopics->pluck('Course'),
            ),
            'topics' => Topic::all('name'),
            'coursesWithTopics' => $coursesWithTopics,
            'knowledgeAreas' => GraphModel::getUniqueResults(
                $knowledgeAreasWithTopics->pluck('KnowledgeArea'),
            ),
            'knowledgeAreasWithTopics' => $knowledgeAreasWithTopics,
            'prerequisiteCourses' => IsPrerequisiteOf::allWithNodes(),
            'levels' => RelationshipLevels::cases(),
        ]);
    }
}
