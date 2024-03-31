<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery;

use CastroItalo\EchoQuery\Traits\BuilderSelect;

/**
 *
 * @package CastroItalo\EchoQuery
 */
final class Builder
{
    // User Builder functionalities
    use BuilderSelect;

    private string $query = "";

    public function select(array ...$columns): self
    {
        $this->query = $this->baseSelect($this->query, $columns);

        return $this;
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
