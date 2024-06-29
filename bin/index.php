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
        ['column_three', 'ctr'],
        ['column_four', 'cfr']
    )
        ->from('table_one', 'to')
        ->where('column_one')
        ->isNotNull()
        ->and('column_two')
        ->isNotNull()
        ->and('column_three')
        ->isNull()
        ->and('column_four')
        ->equalsTo(49)
        ->getQuery();

    echo $query . PHP_EOL;
}

main();
