<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CastroItalo\EchoQuery\Builder;

// TODO:
/**
 *
 * - Create tests for BuilderFrom::baseSubQueryFrom
 *
 */

/**
 * Main function
 * @return void
 */
function main(): void
{
    $sub_query = (new Builder())->select(
        ['column_one'],
        ['column_two']
    )
        ->from('table_one', 'to')
        ->__toString();

    $query = (new Builder())->select(
        ['a.column_one', 'co'],
        ['a.column_two', 'ct']
    )
        ->from($sub_query, 'a', true)
        ->__toString();

    echo $query . PHP_EOL;
}

main();
