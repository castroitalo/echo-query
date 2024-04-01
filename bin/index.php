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

    $sub_query = $echo_query->select(
        ['column_one'],
        ['column_two']
    )
        ->from('table_one')
        ->__toString();
    $query = $echo_query->select(
        ['a.column_one', 'co'],
        ['a.column_two', 'ct']
    )
        ->from($sub_query, 'a', true)
        ->__toString();

    echo $query . PHP_EOL;
}

main();
