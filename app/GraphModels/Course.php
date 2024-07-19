<?php

namespace App\GraphModels;

use Illuminate\Support\Collection;

class Course extends Node
{
    public static string $label = 'Course';

    public static function getAllWithTopics(): Collection
    {
        $results = static::client()->run(
            <<<'CYPHER'
            MATCH (c:Course)-[r_c:COVERS]->(t:Topic)
            RETURN c, r_c, t
            ORDER BY c.number
            CYPHER
            ,
        );

        return static::buildCollectionFromResults($results, [
            'course' => 'c',
            'covers' => 'r_c',
            'topic' => 't',
        ]);
    }
}
