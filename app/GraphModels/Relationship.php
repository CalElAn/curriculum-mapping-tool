<?php

namespace App\GraphModels;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\relationshipTo;

abstract class Relationship extends GraphModel
{
    abstract public static function getFromNodeLabel(): string;
    abstract public static function getToNodeLabel(): string;

    public static function find(string $id)
    {
        $relationship = relationshipTo()
            ->withTypes([static::$label])
            ->withProperties(['id' => $id])
            ->withVariable($relationshipVar = 'relationshipVar');

        $query = query()
            ->match(node()->relationship($relationship, node()))
            ->returning($relationship);

        return static::client()
            ->run($query->build())
            ->get(0)
            ->get($relationshipVar)
            ->getProperties();
    }

    public static function all(?string $orderBy = null): Collection
    {
        $relationship = relationshipTo()
            ->withTypes([static::$label])
            ->withVariable($relationshipVar = 'relationshipVar');

        $query = query()
            ->match(node()->relationship($relationship, node()))
            ->returning($relationship);

        if ($orderBy) {
            $query->orderBy($relationship->property($orderBy));
        }

        $results = static::client()->run($query->build());

        return static::buildCollectionFromResults($results, [$relationshipVar]);
    }

    public static function allWithNodes(): Collection
    {
        $fromNodeLabel = static::getFromNodeLabel();
        $toNodeLabel = static::getToNodeLabel();
        $relationshipLabel = static::$label;

        $results = static::client()->run(
            <<<CYPHER
            MATCH ($fromNodeLabel:$fromNodeLabel)-[$relationshipLabel:$relationshipLabel]->($toNodeLabel:$toNodeLabel)
            RETURN $fromNodeLabel, $relationshipLabel, $toNodeLabel
            CYPHER
            ,
        );

        return static::buildCollectionFromResults($results, [
            $fromNodeLabel => $fromNodeLabel,
            $relationshipLabel => $relationshipLabel,
            $toNodeLabel => $toNodeLabel,
        ]);
    }

    public static function create(
        string $fromNodeId,
        string $toNodeId,
        array $relationshipProperties,
    ): string {
        /* This is quite verbose as compared to using a regular cypher query,
            but since we want to make it dynamic and use fancy stuff like array unpacking,
            it's best to do it this way */
        $fromNode = node(static::getFromNodeLabel())
            ->withProperties(['id' => $fromNodeId])
            ->withVariable($fromNodeVar = 'fromNodeVar');
        $toNode = node(static::getToNodeLabel())
            ->withProperties(['id' => $toNodeId])
            ->withVariable($toNodeVar = 'toNodeVar');
        $relationshipId = Str::uuid()->toString();

        $query = query()
            ->match($fromNode)
            ->match($toNode)
            ->create(
                node()
                    ->withVariable($fromNodeVar)
                    ->relationshipTo(
                        node()->withVariable($toNodeVar),
                        static::$label,
                        [
                            'id' => $relationshipId,
                            ...$relationshipProperties,
                            'created_at' => Procedure::datetime(),
                            'updated_at' => Procedure::datetime(),
                        ],
                    ),
            )
            ->build();

        static::runQueryInTransaction($query);

        return $relationshipId;
    }

    public static function update(string $id, array $properties): void
    {
        $relationship = relationshipTo()
            ->withTypes([static::$label])
            ->withProperties(['id' => $id]);

        $query = query()->match(node()->relationship($relationship, node()));

        foreach ($properties as $property => $value) {
            $query->set(
                $relationship->property($property)->replaceWith($value),
            );
        }

        static::setUpdatedAt($query, $relationship);

        static::runQueryInTransaction($query->build());
    }

    public static function delete(string $id): void
    {
        $relationshipLabel = static::$label;

        static::runQueryInTransaction(
            <<<CYPHER
            MATCH ()-[r:$relationshipLabel{id: \$id}]->()
            DELETE r
            CYPHER
            ,
            [
                'id' => $id,
            ],
        );
    }
}
