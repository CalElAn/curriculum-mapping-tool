<?php

namespace App\GraphModels;

use Facades\App\GraphModels\GraphClient;

class Course //extends BaseGraphModel
{
    public static function getAll(): array
    {
        $coursesResults = GraphClient::client()->run(
            <<<'CYPHER'
            MATCH (c:Course)
            RETURN c
            ORDER BY c.number
            CYPHER
            ,
        );

        $courses = [];

        foreach ($coursesResults as $result) {
            $courses[] = $result->get('c')->getProperties();
        }

        return $courses;
    }
}
