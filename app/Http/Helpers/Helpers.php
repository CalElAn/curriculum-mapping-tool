<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Helpers
{
    public static int $perPage = 7;

    public static function paginate(
        int|null $page,
        Collection $initialTopics,
    ): LengthAwarePaginator {
        $pageName = 'page';
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
        $perPage = static::$perPage;

        return (new LengthAwarePaginator(
            $initialTopics->forPage($page, $perPage),
            $initialTopics->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ))->withQueryString();
    }
}
