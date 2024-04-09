<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery;

use CastroItalo\EchoQuery\Exceptions\BuilderException;
use CastroItalo\EchoQuery\Traits\BuilderFrom;
use CastroItalo\EchoQuery\Traits\BuilderSelect;
use CastroItalo\EchoQuery\Traits\BuilderWhere;

/**
 *
 * @package CastroItalo\EchoQuery
 */
final class Builder
{
    // User Builder functionalities
    use BuilderSelect;
    use BuilderFrom;
    use BuilderWhere;

    private string $query = '';

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

    public function where(string $columnName): self
    {
        $this->query = $this->baseWhere($this->query, $columnName);

        return $this;
    }

    public function equalsTo(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '=', $value);

        return $this;
    }

    public function notEqualsTo(mixed $value, string $notEqualsToOperator = '!='): self
    {
        // Validate not equals to comparison operator
        if (! in_array($notEqualsToOperator, ['!=', '<>'])) {
            throw new BuilderException(
                'Invalid ' . $notEqualsToOperator . ' comparison operator',
                $this->invalidComparisonOperatorExceptionCode,
            );
        }

        $this->query = $this->baseComparisonOperator($this->query, $notEqualsToOperator, $value);

        return $this;
    }

    public function lessThan(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '<', $value);

        return $this;
    }

    public function lessThanEqualsTo(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '<=', $value);

        return $this;
    }

    public function greaterThan(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '>', $value);

        return $this;
    }

    public function greaterThanEqualsTo(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '>=', $value);

        return $this;
    }

    public function and(string $columnName): self
    {
        $this->query = $this->baseLogicalOperator($this->query, 'AND', $columnName);

        return $this;
    }

    public function or(string $columnName): self
    {
        $this->query = $this->baseLogicalOperator($this->query, 'OR', $columnName);

        return $this;
    }

    public function not(string $columnName): self
    {
        $this->query = $this->baseLogicalOperator($this->query, 'NOT', $columnName);

        return $this;
    }

    public function __toString(): string
    {
        return $this->query;
    }
}
