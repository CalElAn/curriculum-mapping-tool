<?php

namespace App\GraphModels;

use Illuminate\Support\Str;
use Laudis\Neo4j\Contracts\TransactionInterface;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;

class Covers extends Relationship
{
    public static string $label = 'COVERS';

    public static function getFromNodeLabel(): string
    {
        return Course::$label;
    }

    public static function getToNodeLabel(): string
    {
        return Topic::$label;
    }
}
