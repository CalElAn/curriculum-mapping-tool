<?php

namespace App\GraphModels;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laudis\Neo4j\Types\CypherMap;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Query;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\relationshipTo;

class Node extends GraphModel
{
    public static Query $currentQuery;
    public static string $nodeVar = 'nodeVar';

    public static function node(): \WikibaseSolutions\CypherDSL\Patterns\Node
    {
        return node(static::$label);
    }

    public static function find(string $id): CypherMap
    {
        $node = static::node()
            ->withProperties(['id' => $id])
            ->withVariable(self::$nodeVar);

        $query = query()->match($node)->returning($node);

        return static::client()
            ->run($query->build())
            ->get(0)
            ->get(self::$nodeVar)
            ->getProperties();
    }

    public static function addToCurrentQuery(Query $query): void
    {
        self::$currentQuery = $query;
    }

    public static function orderBy(string $orderBy): static
    {
        $nodeVar = self::$nodeVar;

        self::$currentQuery->raw(
            'ORDER BY',
            "LOWER(toString($nodeVar.$orderBy))",
        );

        return new static();
    }

    public static function where(
        array $whereClauses,
        string $operator = 'AND',
    ): static {
        /* $whereClauses should be an array of arrays where each array has 3 values and is of the format
         [property, operator, value]*/

        $node = static::node()->withVariable(self::$nodeVar);

        $query = query()->match($node);

        $nodeVar = self::$nodeVar;
        $parsedWhereClauses = [];

        foreach ($whereClauses as $whereClause) {
            $parsedWhereClauses[] = "(LOWER(toString($nodeVar.$whereClause[0])) $whereClause[1] LOWER(toString('$whereClause[2]')))";
        }

        $parsedWhereClauses = implode(" $operator ", $parsedWhereClauses);

        $query->raw('WHERE', "($parsedWhereClauses)");

        $query->returning($node);

        self::addToCurrentQuery($query);

        return new static();
    }

    public static function get(): Collection
    {
        $results = static::client()->run(self::$currentQuery->build());

        return static::buildCollectionFromResults($results, [self::$nodeVar]);
    }

    public static function all(?string $orderBy = null): Collection
    {
        $node = static::node()->withVariable($nodeVar = 'nodeVar');

        $query = query()->match($node)->returning($node);

        if ($orderBy) {
            $query->raw('ORDER BY', "LOWER(toString($nodeVar.$orderBy))"); // to make orderBy case-insensitive https://stackoverflow.com/a/26017623/14324308
        }

        $results = static::client()->run($query->build());

        return static::buildCollectionFromResults($results, [$nodeVar]);
    }

    public static function getRelatedNodes(
        string $id,
        Relationship $relationship,
        ?string $orderBy = null,
    ): Collection {
        $nodeLabelToFind = static::$label;
        $fromNodeLabel = $relationship::getFromNodeLabel();
        $toNodeLabel = $relationship::getToNodeLabel();

        $fromNode = node($fromNodeLabel);
        $toNode = node($toNodeLabel);

        if ($fromNodeLabel === $nodeLabelToFind) {
            $fromNode->withProperties(['id' => $id]);

            $nodeToReturn = $toNode;
            $nodeLabelToReturn = $toNodeLabel;
            $toNode->withVariable($toNodeLabel);
        } else {
            $toNode->withProperties(['id' => $id]);

            $nodeToReturn = $fromNode;
            $nodeLabelToReturn = $fromNodeLabel;
            $fromNode->withVariable($fromNodeLabel);
        }

        $relationshipTo = relationshipTo()
            ->withTypes([$relationship::$label])
            ->withVariable($relationship::$label);

        $query = query()
            ->match($fromNode->relationship($relationshipTo, $toNode))
            ->returning([$relationshipTo, $nodeToReturn]);

        if ($orderBy) {
            $query->raw(
                'ORDER BY',
                "LOWER(toString($nodeLabelToReturn.$orderBy))",
            );
        }

        $results = static::client()->run($query->build());

        return static::buildCollectionFromResults($results, [
            $relationship::$label => $relationship::$label,
            $nodeLabelToReturn => $nodeLabelToReturn,
        ]);
    }

    public static function create(array $properties): string
    {
        $id = Str::uuid()->toString();

        $query = query()
            ->create(
                static::node()->withProperties([
                    'id' => $id,
                    ...$properties,
                    'created_at' => Procedure::datetime(),
                    'updated_at' => Procedure::datetime(),
                ]),
            )
            ->build();

        static::runQueryInTransaction($query);

        return $id;
    }

    public static function update(string $id, array $properties): void
    {
        $node = static::node()->withProperties(['id' => $id]);

        $query = query()->match($node);

        foreach ($properties as $property => $value) {
            $query->set($node->property($property)->replaceWith($value));
        }

        static::setUpdatedAt($query, $node);

        static::runQueryInTransaction($query->build());
    }

    public static function delete(string $id): void
    {
        $node = static::node()->withProperties(['id' => $id]);

        $query = query()->match($node)->delete($node, true)->build();

        static::runQueryInTransaction($query);
    }
}
