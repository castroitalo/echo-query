<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Traits;

use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;

/**
 * Enhances query builders with WHERE clause construction capabilities.
 *
 * The BuilderWhere trait provides functionality to construct the WHERE part of an SQL query,
 * ensuring that the necessary conditions for a valid WHERE clause are met. This includes
 * verifying the presence of a FROM statement in the query and validating column names used in
 * the WHERE clause.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery\Traits
 */
trait BuilderWhere
{
    /**
     * Holds the current WHERE clause being constructed.
     *
     * @var string|null $where The partial or complete WHERE clause as it's built.
     */
    private ?string $where = null;

    /**
     * List of available comparison operators supported by the query builder.
     *
     * This constant defines a set of SQL comparison operators that are validated for use
     * in constructing WHERE clauses, ensuring adherence to SQL standards and compatibility
     * with the underlying database engine.
     *
     * @var string[] A list of valid SQL comparison operators.
     */
    private const AVALIABLE_COMPARISON_OPERATORS = ['=', '!=', '<>', '<', '>', '<=', '>='];

    /**
     * The error code for when a WHERE statement is attempted without a preceding FROM statement.
     *
     * @var int $noPreviousSelectStatementExceptionCode Error code indicating the absence of a required FROM clause.
     */
    private int $noPreviousSelectStatementExceptionCode = BuilderExceptionsCode::NoPreviousSelectStatement->value;

    /**
     * The error code for when an invalid column name is provided in the WHERE clause.
     *
     * @var int $invalidColumnNameExceptionCode Error code used when the column name for a WHERE clause is invalid or empty.
     */
    private int $invalidColumnNameExceptionCode = BuilderExceptionsCode::InvalidColumnName->value;

    /**
     * The error code for when an attempt is made to use a comparison operator without a preceding WHERE statement.
     *
     * This error code is triggered when a query tries to apply a comparison operation (e.g., '=', '>', '<')
     * before establishing a WHERE clause, indicating a misuse of the query building process.
     *
     * @var int $noPreviousWhereStatementExceptionCode Error code indicating the required WHERE clause is missing.
     */
    private int $noPreviousWhereStatementExceptionCode = BuilderExceptionsCode::NoPreviousWhereStatement->value;

    /**
     * The error code for using an unsupported comparison operator in a WHERE clause.
     *
     * Triggered when a query attempts to use a comparison operator outside of those deemed valid
     * by the query builder. This ensures that only SQL-standard and universally recognized comparison
     * operators are used, maintaining the integrity and executability of the SQL query.
     *
     * @var int $invalidComparisonOperatorExceptionCode Error code for an invalid comparison operator.
     */
    private int $invalidComparisonOperatorExceptionCode = BuilderExceptionsCode::InvalidComparisonOperator->value;

    /**
     * Constructs a WHERE clause for an SQL query based on the provided column name.
     *
     * This method appends a WHERE clause to a given SQL query, using the specified column name for the condition.
     * Before appending, it validates the presence of a SELECT statement to ensure the logical structure of the SQL
     * query is maintained, and checks the validity of the column name to prevent SQL errors. If either condition
     * is not met, a BuilderException is thrown with an appropriate error code.
     *
     * @param string $query The initial or existing SQL query to which the WHERE clause will be appended.
     * @param string $columnName The column name to be used in the WHERE clause condition. The column name must
     *                           be valid and non-empty to ensure the generated SQL is syntactically correct.
     * @return string The modified query string including the newly constructed WHERE clause.
     * @throws BuilderException If there is no previous SELECT statement in the query or if the column name is invalid.
     */
    private function baseWhere(string $query, string $columnName): string
    {
        // Exitance conditions
        if (! str_contains($query, 'SELECT')) {
            throw new BuilderException(
                'No previous SELECT statement.',
                $this->noPreviousSelectStatementExceptionCode,
            );
        }

        if (empty($columnName)) {
            throw new BuilderException(
                'Invalid WHERE statement column name.',
                $this->invalidColumnNameExceptionCode,
            );
        }

        $this->where = ' WHERE ' . $columnName;
        $query .= $this->where;

        return $query;
    }

    /**
     * Dynamically applies a comparison operator within an existing WHERE clause of the SQL query.
     *
     * This method extends the WHERE clause by incorporating a specified comparison operator and value,
     * ensuring the operator is supported and the WHERE clause construction adheres to SQL syntax rules.
     * It validates the presence of a WHERE clause and the validity of the comparison operator before
     * modifying the query, throwing an exception for any discrepancies.
     *
     * @param string $query The current SQL query being constructed.
     * @param string $comparisonOperator The comparison operator to apply (e.g., '=', '!=', '<').
     * @param mixed $value The value to be compared against, formatted appropriately based on its type.
     * @return string The updated SQL query including the modified WHERE clause with the new comparison.
     * @throws BuilderException If no previous WHERE clause is found or if an unsupported comparison operator is used.
     */
    private function baseComparisonOperator(string $query, string $comparisonOperator, mixed $value): string
    {
        // Existance conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Operator ' . $comparisonOperator . ' must have a previsou WHERE statemen.',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }

        if (! in_array($comparisonOperator, self::AVALIABLE_COMPARISON_OPERATORS)) {
            throw new BuilderException(
                'Invalid ' . $comparisonOperator . ' comparison operator',
                $this->invalidComparisonOperatorExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->where . ' ' . $comparisonOperator . ' ' . (is_string($value) ? ' \'' . $value . '\' ' : $value);
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }
}
