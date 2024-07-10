<?php

namespace App\GraphModels;

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class BaseGraphModel
{
    public static function client(): ClientInterface
    {
        return ClientBuilder::create()
            ->withDriver(
                'aura',
                config('aura_db.url'),
                Authenticate::basic(
                    config('aura_db.username'),
                    config('aura_db.password'),
                ),
            )
            ->build();
    }

    public static function getAll(string $node, string $orderBy): array
    {
        $results = static::client()->run(
            <<<CYPHER
            MATCH (nodeVar:$node)
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
}
