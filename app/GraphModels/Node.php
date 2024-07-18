<?php

namespace App\GraphModels;

use Illuminate\Support\Str;
use Laudis\Neo4j\Types\CypherMap;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;

class Node extends GraphModel
{
    public static function node(): \WikibaseSolutions\CypherDSL\Patterns\Node
    {
        return node(static::$label);
    }

    public static function find(string $id): CypherMap
    {
        $node = static::node()
            ->withProperties(['id' => $id])
            ->withVariable('nodeVar');

        $query = query()->match($node)->returning($node);

        return static::client()
            ->run($query->build())
            ->get(0)
            ->get('nodeVar')
            ->getProperties();
    }

    public static function all(?string $orderBy = null): array
    {
        $node = static::node()->withVariable($nodeVar = 'nodeVar');

        $query = query()->match($node)->returning($node);

        if ($orderBy) {
            $query->orderBy($node->property($orderBy));
        }

        $results = static::client()->run($query->build());

        return static::buildArrayFromResults($results, [$nodeVar]);
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
