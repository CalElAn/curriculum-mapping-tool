<?php

namespace App\GraphModels;

use Illuminate\Support\Collection;

class Course extends Node
{
    public static string $label = 'Course';

    public static function getTopics(string $courseId): array
    {
        $results = static::client()->run(
            <<<'CYPHER'
            MATCH (:Course {id: $courseId})-[r_c:COVERS]->(t:Topic)
            RETURN t, r_c
            ORDER BY LOWER(toString(t.name))
            CYPHER
            ,
            ['courseId' => $courseId],
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
