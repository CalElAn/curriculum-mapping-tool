<?php

namespace App\GraphModels;

use Illuminate\Support\Str;
use Laudis\Neo4j\Contracts\TransactionInterface;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;

class Topic extends Node
{
    public static string $label = 'Topic';

    public static function getCourses(string $topicId): array
    {
        $results = static::client()->run(
            <<<'CYPHER'
            MATCH (c:Course)-[r_c:COVERS]->(:Topic {id: $topicId})
            RETURN c, r_c
            ORDER BY c.number
            CYPHER
            ,
            ['topicId' => $topicId],
        );

        $courses = [];

        foreach ($results as $result) {
            $courses[] = [
                ...$result->get('r_c')->getProperties(),
                'course' => $result->get('c')->getProperties(),
            ];
        }

        return $courses;
    }
}
