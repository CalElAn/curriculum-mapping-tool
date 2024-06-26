<?php

namespace App\GraphModels;

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class GraphClient
{
    public function client(): ClientInterface
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

}
