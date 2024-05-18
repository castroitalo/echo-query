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
        ['column_two', 'ct'],
        ['column_three', 'ctr']
    )
        ->from('table_one', 'to')
        ->where('column_one')
        ->greaterThan(10)
        ->having('COUNT(column_one)')
        ->greaterThan(10)
        ->and('COUNT(column_one)')
        ->lessThan(100)
        ->__toString();

    echo $query . PHP_EOL;
}

main();
