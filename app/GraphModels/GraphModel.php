<?php

namespace App\GraphModels;

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
                'neo4j',
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

    public static function getAll(string $nodeLabel, string $orderBy): array
    {
        $results = static::client()->run(
            <<<CYPHER
            MATCH (nodeVar:$nodeLabel)
            RETURN nodeVar
            ORDER BY nodeVar.$orderBy
            CYPHER
            ,
        );

        return static::buildArrayFromResults($results, ['nodeVar']);
    }

    public static function buildArrayFromResults(
        $results,
        array $keyValuePairs,
    ): array {
        $resultsArray = [];

        foreach ($results as $result) {
            $keyValuePairArray = [];

            foreach ($keyValuePairs as $key => $value) {
                if (is_int($key)) {
                    if (is_string($result->get($value))) {
                        $resultsArray[] = $result->get($value);
                    } else {
                        $resultsArray[] = $result->get($value)->getProperties();
                    }
                } else {
                    $keyValuePairArray[$key] = $result
                        ->get($value)
                        ->getProperties();
                }
            }

            if (!empty($keyValuePairArray)) {
                $resultsArray[] = $keyValuePairArray;
            }
        }

        return $resultsArray;
    }

    public static function getUniqueResults($results): array
    {
        $uniqueResults = [];
        $uniqueResultIds = [];

        foreach ($results as $result) {
            if (!in_array($result->get('id'), $uniqueResultIds, true)) {
                $uniqueResultIds[] = $result->get('id');
                $uniqueResults[] = $result;
            }
        }

        return $uniqueResults;
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
