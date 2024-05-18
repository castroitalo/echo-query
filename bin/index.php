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
        ['column_one', 'co'],
        ['column_two', 'ct'],
        ['columnt_three', 'ctr']
    )
        ->from('table_one', 'to')
        ->orderBy(
            ['column_one'],
            ['column_two', 'desc']
        )
        ->__toString();

    echo $query . PHP_EOL;
}

main();
