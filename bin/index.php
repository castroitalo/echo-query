<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CastroItalo\EchoQuery\Builder;

/**
 * Main function
 * @return void
 */
function main(): void
{
    $echo_query = new Builder();
    $query = $echo_query->select(
        ['column_one', 'co'],
        ['column_two']
    )
    ->__toString();

    echo $query . PHP_EOL;
}

main();
