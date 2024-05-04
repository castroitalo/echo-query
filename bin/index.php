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
        ['name'],
        ['age']
    )
        ->from('contacts')
        ->where('age')
        ->in(['\'nome\'', '\'nome_dois\''])
        ->__toString();

    echo $query . PHP_EOL;
}

main();
