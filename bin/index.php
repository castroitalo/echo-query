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
    $sub_query = (new Builder())->select(
        ['column_one'],
        ['column_four']
    )
        ->from('table_three', 'ttr')
        ->where('column_two')
        ->equalsTo(2)
        ->__toString();

    $query = (new Builder())->select(
        ['a.column_one', 'co'],
        ['b.column_two', 'ct'],
        ['c.column_four', 'cfr']
    )
        ->from('table_one', 'a')
        ->innerJoin(
            ['table_two', 'b'],
            ['a.column_one', 'b.column_one']
        )
        ->innerJoinSub(
            [$sub_query, 'c'],
            ['b.column_one', 'c.column_one']
        )
        ->__toString();

    echo $query . PHP_EOL;
}

main();
