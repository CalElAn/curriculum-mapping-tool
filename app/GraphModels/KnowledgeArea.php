<?php

namespace App\GraphModels;

use Illuminate\Support\Collection;

class KnowledgeArea extends Node
{
    public static string $label = 'KnowledgeArea';

    public static function getTopics(string $knowledgeAreaId): array
    {
        $results = static::client()->run(
            <<<'CYPHER'
            MATCH (t:Topic)-[r_c:TEACHES]->(k:KnowledgeArea {id: $knowledgeAreaId})
            RETURN t, r_c, k
            ORDER BY LOWER(toString(t.name))
            CYPHER
            ,
            ['knowledgeAreaId' => $knowledgeAreaId],
        );

        $courses = [];

        foreach ($results as $result) {
            $courses[] = [
                ...$result->get('r_c')->getProperties(),
                'topic' => $result->get('t')->getProperties(),
            ];
        }

        return $courses;
    }

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
