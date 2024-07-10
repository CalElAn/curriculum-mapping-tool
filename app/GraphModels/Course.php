<?php

namespace App\GraphModels;

class Course extends BaseGraphModel
{
    public static function getAllWithTopics(): array
    {
        $results = static::client()->run(
            <<<'CYPHER'
            MATCH (c:Course)-[r_c:COVERS]->(t:Topic)
            RETURN c, r_c, t
            ORDER BY c.number
            CYPHER
            ,
        );

        return static::buildArrayFromResults($results, [
            'course' => 'c',
            'covers' => 'r_c',
            'topic' => 't',
        ]);
    }
}
