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
    $union_query = (new Builder())->select(
        ['column_four', 'cfr'],
        ['column_five', 'cf'],
        ['column_six', 'cs']
    )
        ->from('table_two', 'tt')
        ->where('column_five')
        ->notIn([1, 3, 4, 6])
        ->__toString();
    $query = (new Builder())->select(
        ['column_one', 'co'],
        ['column_two', 'ct'],
        ['column_three', 'ctr']
    )
        ->from('table_one', 'to')
        ->where('column_one')
        ->greaterThan(10)
        ->union($union_query)
        ->__toString();

    echo $query . PHP_EOL;
}

main();
