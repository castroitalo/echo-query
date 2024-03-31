<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery;

use CastroItalo\EchoQuery\Traits\BuilderFrom;
use CastroItalo\EchoQuery\Traits\BuilderSelect;

/**
 *
 * @package CastroItalo\EchoQuery
 */
final class Builder
{
    // User Builder functionalities
    use BuilderSelect;
    use BuilderFrom;

    private string $query = "";

    public function select(array ...$columns): self
    {
        $this->query = $this->baseSelect($this->query, $columns);

        return $this;
    }

    public function from(string $tableName, ?string $tableAlias = null, bool $subQueryFrom = false): self
    {
        if ($subQueryFrom === true) {
            $this->query = $this->baseSubQueryFrom($this->query, $tableName, $tableAlias);
        } elseif ($subQueryFrom === false) {
            $this->query = $this->baseFrom($this->query, $tableName, $tableAlias);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
