<?php

namespace App\GraphModels;

use Illuminate\Support\Str;
use Laudis\Neo4j\Contracts\TransactionInterface;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;

class Covers extends BaseGraphModel
{
    public static function create(
        string $courseId,
        string $topicId,
        string $coverageLevel,
    ): string {
        $coversId = Str::uuid();

        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($coversId, $courseId, $topicId, $coverageLevel) {
            $tsx->run(
                <<<'CYPHER'
                MATCH (c:Course {id: $courseId}), (t:Topic {id: $topicId })
                CREATE (c)
                -[r:COVERS{
                    id: $coversId,
                    coverage_level: $coverageLevel,
                    created_at: datetime(),
                    updated_at: datetime()
                }]->
                (t)
                CYPHER
                ,
                [
                    'courseId' => $courseId,
                    'topicId' => $topicId,
                    'coversId' => $coversId,
                    'coverageLevel' => $coverageLevel,
                ],
            );
        });

        return $coversId;
    }

    public static function update(string $id, string $coverage_level): void
    {
        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($id, $coverage_level) {
            $tsx->run(
                <<<'CYPHER'
                MATCH ()-[r:COVERS{id: $id}]->()
                SET r.coverage_level = $coverage_level,
                    r.updated_at = datetime()
                CYPHER
                ,
                [
                    'id' => $id,
                    'coverage_level' => $coverage_level,
                ],
            );
        });
    }

    public static function delete($id): void
    {
        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($id, &$x) {
            $x = $tsx->run(
                <<<'CYPHER'
                MATCH ()-[r:COVERS{id: $id}]->()
                DELETE r
                CYPHER
                ,
                [
                    'id' => $id,
                ],
            );
        });
    }
}
