<?php

namespace App\GraphModels;

use Illuminate\Support\Str;
use Laudis\Neo4j\Contracts\TransactionInterface;

class Topic extends BaseGraphModel
{
    public static function getTopicsForCourse(string $courseId): array
    {
        $topicsResults = static::client()->run(
            <<<'CYPHER'
            MATCH (:Course {id: $courseId})-[r_c:COVERS]->(t:Topic)
            RETURN r_c, t
            ORDER BY t.name
            CYPHER
            ,
            ['courseId' => $courseId],
        );

        $topics = [];

        foreach ($topicsResults as $result) {
            $topics[] = [
                'id' => $result->get('t')->getProperties()->get('id'),
                'name' => $result->get('t')->getProperties()->get('name'),
                'coverage_level' => $result
                    ->get('r_c')
                    ->getProperties()
                    ->get('coverage_level'),
            ];
        }

        return $topics;
    }

    public static function create(
        string $courseId,
        string $topicName,
        string $topicCoverageLevel,
    ): string {
        $courseCoversId = Str::uuid();
        $topicId = Str::uuid();

        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use (
            $courseId,
            $courseCoversId,
            $topicCoverageLevel,
            $topicId,
            $topicName,
        ) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (c:Course {id: $courseId})
                CREATE (c)
                -[:COVERS{
                    id: $courseCoversId,
                    coverage_level: $topicCoverageLevel,
                    created_at: datetime(),
                    updated_at: datetime()
                }]->
                (:Topic {
                    id: $topicId,
                    name: $topicName,
                    created_at: datetime(),
                    updated_at: datetime()
                })
                CYPHER
                ,
                [
                    'courseId' => $courseId,
                    'courseCoversId' => $courseCoversId,
                    'topicCoverageLevel' => $topicCoverageLevel,
                    'topicId' => $topicId,
                    'topicName' => $topicName,
                ],
            );
        });

        return $topicId;
    }

    public static function update(
        $courseId,
        $topicId,
        $name,
        $coverage_level,
    ): void {
        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($courseId, $topicId, $name, $coverage_level) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (:Course {id: $courseId})-[r_c:COVERS]->(t:Topic {id: $topicId})
                SET t.name = $name
                SET r_c.coverage_level = $coverage_level
                CYPHER
                ,
                [
                    'courseId' => $courseId,
                    'topicId' => $topicId,
                    'name' => $name,
                    'coverage_level' => $coverage_level,
                ],
            );
        });
    }

    public static function delete($id): void
    {
        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($id) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (t:Topic {id: $id})
                DETACH DELETE t
                CYPHER
                ,
                [
                    'id' => $id,
                ],
            );
        });
    }
}
