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
     * The error code associated with invalid column names.
     *
     * @var int $invalidColumnNameExceptionCode Error code to use when an invalid column name is encountered.
     */
    private int $invalidColumnNameExceptionCode = BuilderExceptionsCode::InvalidColumnName->value;

    /**
     * The error code assiciated with group by having condition.
     *
     * @var int $invalidGroupByColumnsExceptionCode Error code to use when an invalid group by conditions is encoutered.
     */
    private int $invalidGroupByColumnsExceptionCode = BuilderExceptionsCode::InvalidGroupByColumns->value;

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
        $select = ' SELECT ';
        $columnsCounter = 1;

        foreach ($columns as $column) {
            @[$columnName, $columnAlias] = $column;

            // Validate SELECT columns
            if (is_null($columnName) || empty($columnName)) {
                throw new BuilderException(
                    'Invalid SELECT statement columns.',
                    $this->invalidColumnNameExceptionCode,
                );
            }

            $columnAliasValue = is_null($columnAlias) ? '' : ' AS ' . $columnAlias;

            if (($columnsCounter + 1) > sizeof($columns)) {
                $select .= ' ' . $columnName . ' ' . $columnAliasValue;

                break;
            }

            $select .= ' ' . $columnName . ' ' . $columnAliasValue . ', ';
            $columnsCounter += 1;
        }

        $query .= $select;

        return $query;
    }

    /**
     * Constructs a GROUP BY clause and appends it to the provided SQL query string.
     *
     * This method validates the existence of columns specified for grouping and constructs the GROUP BY clause.
     * It throws a BuilderException if the columns array is empty, indicating a misuse or logical error in the
     * query construction process.
     *
     * @param string $query The existing SQL query to which the GROUP BY clause will be appended.
     * @param array $columns An array of column names used for grouping the results of the query.
     * @return string The updated SQL query string including the constructed GROUP BY clause.
     * @throws BuilderException If the columns array is empty, utilizing the specified exception code
     *                          for invalid GROUP BY columns.
     */
    private function baseGroupBy(string $query, array $columns): string
    {
        // Existance validation
        if (empty($columns)) {
            throw new BuilderException(
                'Invalid GROUP BY columns.',
                $this->invalidGroupByColumnsExceptionCode,
            );
        }

        $columnsGroupBy = implode(', ', $columns);
        $query .= ' GROUP BY ' . $columnsGroupBy;

        return $query;
    }

    /**
     * Base method to handle pagination with LIMIT and OFFSET.
     *
     * Constructs the pagination part of the SQL query using LIMIT and optionally OFFSET parameters.
     * This private method is called by `pagination` to format and append the LIMIT and OFFSET clauses to
     * the current SQL query.
     *
     * @param string $query The current SQL query being constructed.
     * @param int $limit The number of records to return.
     * @param int|null $offset The starting point for records to return. If null, the query starts from the first record.
     * @return string The SQL query string modified to include the LIMIT and OFFSET clauses.
     */
    private function basePagination(string $query, int $limit, ?int $offset): string
    {
        $offsetValue = is_null($offset) ? '' : ' OFFSET ' . $offset . ' ';
        $query .= ' LIMIT ' . $limit . ' ' . $offsetValue . ' ';

        return $query;
    }
}
