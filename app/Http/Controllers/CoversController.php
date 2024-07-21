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
            'course_id' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) use (
                    $request,
                ) {
                    if (
                        Covers::allWithNodes()
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
        ])->validate();

        $id = Covers::create($request->course_id, $request->topic_id, [
            'coverage_level' => $request->coverage_level,
            'tools' => $request->tools, //TODO update CoversControllerTest
            'comments' => $request->comments,
        ]);

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Covers::update($id, [
            'coverage_level' => $request->coverage_level,
        ]);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Covers::delete($id);

        return back();
    }
}
