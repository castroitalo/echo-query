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
     * @var int $noPreviousSelectStatementErrorCode Error code for missing SELECT statement before FROM.
     */
    private int $noPreviousSelectStatementErrorCode = BuilderExceptionsCode::NoPreviousSelectStatement->value;

    /**
     * The error code associated with an invalid table name.
     *
     * @var int $invalidTableName Error code to use when an invalid table name is provided.
     */
    private int $invalidTableName = BuilderExceptionsCode::InvalidTableName->value;

    /**
     * The error code associated with an invalid alias.
     *
     * @var int $invalidAlias Error code to use when an invalid alias is provided.
     */
    private int $invalidAlias = BuilderExceptionsCode::InvalidAlias->value;

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
                $this->noPreviousSelectStatementErrorCode,
            );
        }

        // Validate table name
        if (empty($tableName)) {
            throw new BuilderException(
                'Table name can\'t be empty.',
                $this->invalidTableName,
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
     * This method allows incorporating subqueries into the FROM clause of the main query.
     * It validates both the presence of a SELECT statement in the main query and the
     * non-emptiness of the subquery alias before appending the subquery as a source.
     *
     * @param string $query The initial query string to append the subquery to.
     * @param string $subQuery The subquery string to be used in the FROM clause.
     * @param string|null $subQueryAlias The alias for the subquery, which is mandatory.
     * @return string The modified query string including the FROM clause with the subquery.
     * @throws BuilderException If the necessary conditions for a FROM statement are not met or if the alias is invalid.
     */
    public function baseSubQueryFrom(string $query, string $subQuery, ?string $subQueryAlias): string
    {
        // TODO

        return $query;
    }
}
