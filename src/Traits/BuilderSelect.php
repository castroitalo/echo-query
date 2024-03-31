<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Traits;

use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;

/**
 * Provides SELECT statement building capabilities to query builders.
 *
 * The BuilderSelect trait encapsulates the logic necessary for constructing the SELECT
 * part of an SQL query. It is designed to be used by query builder classes that require
 * the ability to dynamically generate SELECT statements based on a set of columns and
 * optional aliases provided by the user.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery\Traits
 */
trait BuilderSelect
{
    /**
     * The current SELECT statement being built.
     *
     * @var string|null $select Holds the partial SELECT statement as it is being constructed.
     */
    private ?string $select = null;

    /**
     * The error code associated with invalid column names.
     *
     * @var int $invalidColumnName Error code to use when an invalid column name is encountered.
     */
    private int $invalidColumnName = BuilderExceptionsCode::InvalidColumnName->value;

    /**
     * Constructs the SELECT part of a query based on provided columns.
     *
     * This method initializes or appends to the $select property based on an array of columns
     * provided. Each column can optionally have an alias. If any column name is invalid (i.e.,
     * null or an empty string), a BuilderException is thrown to indicate this error state.
     *
     * @param string $query The initial or existing query to which the SELECT statement will be appended.
     * @param array $columns An array of columns to include in the SELECT statement. Each element in the
     *                       array should be an array itself, containing the column name and optionally,
     *                       an alias for the column.
     * @return string The modified query string including the constructed SELECT statement.
     * @throws BuilderException If any column name is invalid.
     */
    private function baseSelect(string $query, array $columns): string
    {
        $this->select = ' SELECT ';
        $columnsCounter = 1;

        foreach ($columns as $column) {
            @[$columnName, $columnAlias] = $column;

            // Validate SELECT columns
            if (is_null($columnName) || empty($columnName)) {
                throw new BuilderException(
                    'Invalid SELECT statement columns.',
                    $this->invalidColumnName,
                );
            }

            $columnAliasValue = is_null($columnAlias) ? '' : ' AS ' . $columnAlias;

            if (($columnsCounter + 1) > sizeof($columns)) {
                $this->select .= ' ' . $columnName . ' ' . $columnAliasValue;

                break;
            }

            $this->select .= ' ' . $columnName . ' ' . $columnAliasValue . ', ';
            $columnsCounter += 1;
        }

        $query .= $this->select;

        return $query;
    }
}
