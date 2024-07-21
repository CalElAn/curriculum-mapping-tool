<?php

namespace App\GraphModels;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Patterns\Relationship as RelationshipPattern;
use WikibaseSolutions\CypherDSL\Patterns\Node as NodePattern;

class GraphModel
{
    public static function client(): ClientInterface
    {
        return ClientBuilder::create()
            ->withDriver(
                'aura',
                config('neo4j.url'),
                Authenticate::basic(
                    config('neo4j.username'),
                    config('neo4j.password'),
                ),
            )
            ->build();
    }

    public static function runQueryInTransaction(
        string $query,
        iterable $parameters = [],
    ): void {
        static::client()->writeTransaction(static function (
            TransactionInterface $tsx,
        ) use ($query, $parameters) {
            $tsx->run($query, $parameters);
        });
    }

    public static function getAll(
        string $nodeLabel,
        string $orderBy,
    ): Collection {
        $results = static::client()->run(
            <<<CYPHER
            MATCH (nodeVar:$nodeLabel)
            RETURN nodeVar
            ORDER BY nodeVar.$orderBy
            CYPHER
            ,
        );

        return static::buildCollectionFromResults($results, ['nodeVar']);
    }

    /**
     * For $keyValuePairs of ['foo', 'bar']:
     *      returns [$result->get('foo'), $result->get('bar')] for each $result in $results.
     *      if $result->get('foo') is an array, returns [$result->get('foo')->getProperties(), $result->get('bar')] for each $result in $results.
     *
     * For $keyValuePairs of ['foo' => 'bar', 'baz' => 'boz']:
     *       returns ['foo' => $result->get('bar')->getProperties(), 'baz' => $result->get('boz')->getProperties()] for each $result in $results.
     */
    public static function buildCollectionFromResults(
        $results,
        array $keyValuePairs,
    ): Collection {
        $resultsArray = [];

        foreach ($results as $result) {
            $keyValuePairArray = [];

            foreach ($keyValuePairs as $key => $value) {
                if (is_int($key)) {
                    // then we know $keyValuePairs is of the format ['foo', 'bar']
                    if (is_string($result->get($value))) {
                        $resultsArray[] = $result->get($value);
                    } else {
                        $resultsArray[] = $result->get($value)->getProperties();
                    }
                } else {
                    // $keyValuePairs is of the format ['foo' => 'bar', 'baz' => 'boz']
                    $keyValuePairArray[$key] = $result
                        ->get($value)
                        ->getProperties();
                }
            }

            if (!empty($keyValuePairArray)) {
                $resultsArray[] = $keyValuePairArray;
            }
        }

        return collect($resultsArray);
    }

    public static function getUniqueResults($results): Collection
    {
        $uniqueResults = [];
        $uniqueResultIds = [];

        foreach ($results as $result) {
            if (!in_array($result->get('id'), $uniqueResultIds, true)) {
                $uniqueResultIds[] = $result->get('id');
                $uniqueResults[] = $result;
            }
        }

        return collect($uniqueResults);
    }

    public static function setUpdatedAt(
        Query $query,
        RelationshipPattern|NodePattern $relationship,
    ): void {
        $query->set(
            $relationship
                ->property('updated_at')
                ->replaceWith(Procedure::datetime()),
        );
    }
}
