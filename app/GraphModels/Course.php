<?php

namespace App\GraphModels;

class Course extends BaseGraphModel
{
    public static function getAll(): array
    {
        $coursesResults = static::client()->run(
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
