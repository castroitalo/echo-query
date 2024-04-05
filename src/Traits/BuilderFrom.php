<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Traits;

use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;

/**
 * Provides FROM statement building capabilities to query builders.
 *
 * The BuilderFrom trait includes methods for constructing the FROM part of an SQL query,
 * supporting both standard table names and subqueries as sources. This trait ensures that
 * the FROM statement is valid within the context of an existing query, enforcing logical
 * constraints such as the presence of a SELECT statement prior to the FROM clause.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery\Traits
 */
trait BuilderFrom
{
    /**
     * The current FROM statement being built.
     *
     * @var string|null $from Holds the partial FROM statement as it is being constructed.
     */
    private ?string $from = null;

    /**
     * The error code for when there is no previous SELECT statement.
     *
     * @var int $noPreviousSelectStatementExceptionCode Error code for missing SELECT statement before FROM.
     */
    private int $noPreviousSelectStatementExceptionCode = BuilderExceptionsCode::NoPreviousSelectStatement->value;

    /**
     * The error code associated with an invalid table name.
     *
     * @var int $invalidTableNameExceptionCode Error code to use when an invalid table name is provided.
     */
    private int $invalidTableNameExceptionCode = BuilderExceptionsCode::InvalidTableName->value;

    /**
     * The error code associated with an invalid alias.
     *
     * @var int $invalidAliasExceptionCode Error code to use when an invalid alias is provided.
     */
    private int $invalidAliasExceptionCode = BuilderExceptionsCode::InvalidAlias->value;

    /**
     * The error code for when multiple FROM statements are included in a single query.
     *
     * This error code is used to indicate that a query cannot contain more than one FROM
     * clause, aligning with SQL standards. An attempt to append an additional FROM statement
     * to a query that already includes one will result in a BuilderException being thrown.
     *
     * @var int $multipleFromStatementExceptionCode Error code indicating the presence of multiple FROM statements.
     */
    private int $multipleFromStatementExceptionCode = BuilderExceptionsCode::MultipleFromStatement->value;

    /**
     * Validates the necessary conditions for a FROM statement within the query.
     *
     * This method checks for the existence of a SELECT statement prior to appending a FROM
     * clause and verifies that the table name provided is not empty. It throws a BuilderException
     * if any of these validations fail.
     *
     * @param string $query The query to validate.
     * @param string $tableName The table name to validate.
     * @throws BuilderException If the query does not contain a SELECT statement or if the table name is empty.
     */
    private function baseFromStatementValidation(string $query, string $tableName): void
    {
        // Validate FROM existance conditions
        if (! str_contains($query, 'SELECT')) {
            throw new BuilderException(
                'No previous SELECT statement for FROM statement.',
                $this->noPreviousSelectStatementExceptionCode,
            );
        }

        // Validate table name
        if (empty($tableName)) {
            throw new BuilderException(
                'Table name can\'t be empty.',
                $this->invalidTableNameExceptionCode,
            );
        }
    }

    /**
     * Constructs a FROM clause with an optional table alias.
     *
     * This method appends a FROM statement to the given query, using the provided table name
     * and optional alias. It leverages baseFromStatementValidation to ensure the query's validity
     * before appending the FROM clause.
     *
     * @param string $query The initial query string to append the FROM clause to.
     * @param string $tableName The name of the table to select from.
     * @param string|null $tableAlias Optional alias for the table.
     * @return string The modified query string including the FROM clause.
     * @throws BuilderException If the necessary conditions for a FROM statement are not met.
     */
    private function baseFrom(string $query, string $tableName, ?string $tableAlias): string
    {
        // Validate FROM statement
        $this->baseFromStatementValidation($query, $tableName);

        $tableAliasValue = (is_null($tableAlias) || empty($tableAlias)) ? '' : ' AS ' . $tableAlias;
        $this->from = ' FROM ' . $tableName . ' ' . $tableAliasValue;
        $query .= $this->from;

        return $query;
    }

    /**
     * Appends a FROM clause using a subquery with a mandatory alias.
     *
     * Incorporates a subquery into the FROM clause of the main SQL query. This method
     * enforces two critical validations: it checks for an existing SELECT statement to
     * ensure the logical order of query construction is maintained, and it requires a
     * non-empty alias for the subquery to provide a clear and unambiguous reference within
     * the main query. If these conditions are not met, or if an attempt is made to include
     * more than one FROM clause in a single query, a BuilderException is thrown to indicate
     * the specific error based on the provided error codes.
     *
     * @param string $query The initial or existing SQL query to which the subquery FROM clause will be appended.
     * @param string $subQuery The subquery string to be used in the FROM clause. This subquery should be a complete
     *                         and valid SQL query in itself, capable of being executed independently if needed.
     * @param string|null $subQueryAlias The alias for the subquery, providing a namespace within the main query.
     *                                   This parameter is mandatory to ensure the subquery can be referenced properly.
     * @return string The modified query string including the FROM clause with the subquery and its alias.
     * @throws BuilderException If a FROM statement already exists in the query, if the subquery alias is missing,
     *                          or if other conditions for a valid FROM clause are not satisfied.
     */
    public function baseSubQueryFrom(string $query, string $subQuery, ?string $subQueryAlias): string
    {
        if (is_null($subQueryAlias)) {
            throw new BuilderException(
                'FROM alias is mandatory when it\'s used with sub-query',
                $this->invalidAliasExceptionCode,
            );
        }

        if (str_contains($query, 'FROM')) {
            throw new BuilderException(
                'SELECT statement can\'t have multiples FROM statement',
                $this->multipleFromStatementExceptionCode,
            );
        }

        $this->from = ' FROM ( ' . $subQuery . ' ) AS ' . $subQueryAlias;
        $query .= $this->from;

        return $query;
    }
}
