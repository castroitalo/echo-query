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
    $query = (new Builder())->select(
        ['COUNT(column_one)', 'co'],
        ['SUM(column_two)', 'ct'],
        ['column_three', 'ctr']
    )
        ->from('table_one', 'to')
        ->where('column_one')
        ->greaterThan(10)
        ->groupBy('column_one', 'column_two')
        ->__toString();

    echo $query . PHP_EOL;
}

main();
