<?php

declare(strict_types=1);

use CastroItalo\EchoQuery\Builder;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Main function
 * @return void
 */
function main(): void
{
    $echo_query = new Builder();
    echo 'hello world';
}

main();
