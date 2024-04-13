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
        ['a.column_one', 'co'],
        ['a.column_two', 'ct'],
        ['a.column_three', 'cth']
    )
        ->from('table_one', 'a')
        ->where('a.column_one')
        ->notIn(['value_one', 2, 'value_three', 4])
        ->__toString();

    echo $query . PHP_EOL;
}

main();
