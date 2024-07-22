<?php

namespace App\Http\Controllers;

use App\GraphModels\Covers;
use App\GraphModels\Teaches;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeachesController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'course_id' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) use (
                    $request,
                ) {
                    if (
                        Teaches::allWithNodes()
                            ->filter(
                                fn($item) => $item['Course']['id'] ===
                                    $request->course_id &&
                                    $item['Topic']['id'] === $request->topic_id,
                            )
                            ->isNotEmpty()
                    ) {
                        $fail('The relationship has to be unique.');
                    }
                },
            ],
            'topic_id' => 'required',
        ])->validate();

        $id = Teaches::create($request->course_id, $request->topic_id, [
            'level' => $request->level,
            'tools' => $request->tools, //TODO update CoversControllerTest
            'comments' => $request->comments,
        ]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Teaches::update($id, [
            'level' => $request->level,
        ]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Teaches::delete($id);

        return back();
    }
}
