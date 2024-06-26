<?php

namespace App\GraphModels;

use Facades\App\GraphModels\GraphClient;
use Illuminate\Support\Str;
use Laudis\Neo4j\Contracts\TransactionInterface;

class Topic extends BaseGraphModel
{
    public static function getTopicsForCourse(string $courseId): array
    {
        $topicsResults = GraphClient::client()->run(
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
                'topic' => $result->get('t')->getProperties(),
                'covers' => $result->get('r_c')->getProperties(),
            ];
        }

        return $topics;
    }

    public static function create(
        string $courseId,
        string $topicName,
        string $topicCoverageLevel,
    ): string {
        $id = Str::uuid();

        GraphClient::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($id, $courseId, $topicName, $topicCoverageLevel) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (c:Course {id: $courseId})
                CREATE (c)
                -[:COVERS{coverage_level: $topicCoverageLevel}]->
                (:Topic {
                    id: $id
                    name: $topicName,
                    created_at: datetime(),
                    updated_at: datetime()
                })
                CYPHER
                ,
                [
                    'id' => $id,
                    'courseId' => $courseId,
                    'topicName' => $topicName,
                    'topicCoverageLevel' => $topicCoverageLevel,
                ],
            );
        });

        return $id;
    }

    public static function update($id, $name): void
    {
        GraphClient::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($id, $name) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (t:Topic {id: $id})
                SET t.name = $name
                CYPHER
                ,
                [
                    'id' => $id,
                    'name' => $name,
                ],
            );
        });
    }

    public static function delete($id): void
    {
        GraphClient::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($id) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (t:Topic {id: $id})
                DELETE t
                CYPHER
                ,
                [
                    'id' => $id,
                ],
            );
        });
    }
}
