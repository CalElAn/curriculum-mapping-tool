<?php

namespace App\GraphModels;

use Illuminate\Support\Str;
use Laudis\Neo4j\Contracts\TransactionInterface;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;

class Topic extends BaseGraphModel
{
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
                ...$result->get('c')->getProperties(),
                'covers' => $result->get('r_c')->getProperties(),
            ];
        }

        return $courses;
    }

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

    public static function getAllTopicNames(): array
    {
        $topicNames = static::client()->run(
            <<<'CYPHER'
            MATCH (t:Topic)
            RETURN t.name
            ORDER BY t.name
            CYPHER
            ,
        );

        return static::buildArrayFromResults($topicNames, ['t.name']);
    }

    public static function create(string $name): string
    {
        $id = Str::uuid()->toString();

        $query = query()
            ->create(
                node('Topic')->withProperties([
                    'id' => $id,
                    'name' => $name,
                    'created_at' => 'datetime()',
                    'updated_at' => 'datetime()',
                ]),
            )
            ->build();

        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($query) {
            $tsx->run($query);
        });

        return $id;
    }

    public static function update(string $id, string $name): void
    {
        static::client()->writeTransaction(static function (
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

/*
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\procedure;
$topic = node('Topic')->withProperties(['name' => '602 another']);
        $course = node('Course')->withProperties(['name' => '$id']);
        $query = query()
            ->merge(
                $topic,
                (new SetClause())->add(
                    $topic
                        ->property('id')
                        ->replaceWith(procedure()::raw('randomUUID')),
                    $topic->property('name')->replaceWith('$name'),
                    $topic
                        ->property('created_at')
                        ->replaceWith(procedure()::raw('randomUUID')),
                ),
            )
            ->returning([$topic])
            ->match($course)
            ->create(
                node()
                    ->withVariable($course->getVariable())
                    ->relationshipTo(
                        node()->withVariable($topic->getVariable()),
                        'COVERS',
                        [
                            'id' => 'uuid()',
                            'coverage_level' => '$topicCoverageLevel',
                            'created_at' => 'datetime()',
                            'updated_at' => 'datetime()',
                        ],
                    ),
            )
            ->build();
        dd($query);*/
