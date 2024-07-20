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
        array $propertyOperatorValues,
        bool $and = true,
    ): static {
        $node = static::node()->withVariable(self::$nodeVar);

        $query = query()->match($node);

        $whereClauses = [];

        foreach ($propertyOperatorValues as $propertyOperatorValue) {
            $whereClauses[] = $node
                ->property($propertyOperatorValue[0])
                ->{$propertyOperatorValue[1]}($propertyOperatorValue[2]); //TODO how to make this a raw stmnt so toLower can be added to both sides
        }

        $query->where($whereClauses, $and ? WhereClause::AND : WhereClause::OR);

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
