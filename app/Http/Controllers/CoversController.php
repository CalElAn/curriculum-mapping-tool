<?php

namespace App\Http\Controllers;

use App\GraphModels\Covers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CoversController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $id = Covers::create(
            $request->course_id,
            $request->topic_id,
            $request->coverage_level,
        );

        return back()->with('data', ['id' => $id]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Covers::update($id, $request->coverage_level);

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        Covers::delete($id);

        return back();
    }
}
