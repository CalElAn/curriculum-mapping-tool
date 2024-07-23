<?php

namespace App\Http\Controllers;

use App\GraphModels\Covers;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoversController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'topic_id' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) use (
                    $request,
                ) {
                    if (
                        Covers::allWithNodes()
                            ->filter(
                                fn($item) => $item['Topic']['id'] ===
                                    $request->topic_id &&
                                    $item['KnowledgeArea']['id'] ===
                                        $request->knowledge_area_id,
                            )
                            ->isNotEmpty()
                    ) {
                        $fail('The relationship has to be unique.');
                    }
                },
            ],
            'knowledge_area_id' => 'required',
        ])->validate();

        $id = Covers::create($request->topic_id, $request->knowledge_area_id, [
            'level' => $request->level, //TODO update CoversControllerTest
            'comments' => $request->comments,
        ]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Covers::update($id, [
            'level' => $request->level,
            'comments' => $request->comments,
        ]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Covers::delete($id);

        return back();
    }
}
