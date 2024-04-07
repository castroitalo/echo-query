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
     * The error code for using an unsupported 'not equals to' operator.
     *
     * This code is used when an attempt is made to use a 'not equals to' operator
     * that is not recognized by the SQL standard supported by the query builder.
     * Only '!=' and '<>' operators are considered valid for expressing inequality.
     *
     * @var int $invalidNotEqualsToOperatorExceptionCode Error code for using an invalid 'not equals to' operator.
     */
    private int $invalidNotEqualsToOperatorExceptionCode = BuilderExceptionsCode::InvalidNotEqualsToOperator->value;

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

    private function validateWhereStatementFilterOperator(string $operator, string $query): void
    {
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Operator ' . $operator . ' must have a previsou WHERE statemen.',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }
    }

    /**
     * Constructs a new WHERE clause string with the specified comparison operator and value.
     *
     * This method forms a new WHERE clause by appending a comparison operator and its corresponding
     * value to the existing WHERE condition. If the value is a string, it is enclosed in quotes.
     *
     * @param string $operator The comparison operator to use in the WHERE clause.
     * @param mixed $value The value to compare against in the WHERE clause.
     * @return string The newly constructed WHERE clause with the added condition.
     */
    private function getNewWhere(string $operator, mixed $value): string
    {
        $newWhere = $this->where;

        if (is_string($value)) {
            $newWhere .= ' ' . $operator . ' \'' . $value . '\' ';
        } else {
            $newWhere .= ' ' . $operator . ' ' . $value;
        }

        return $newWhere;
    }

    /**
     * Appends an equality comparison to the WHERE clause of the query.
     *
     * After validating the presence of a WHERE clause, this method extends it with an
     * equality ('=') comparison operator followed by the specified value. It updates the
     * query to include this new condition, ensuring the WHERE clause accurately reflects
     * the desired filter criteria.
     *
     * @param string $query The current SQL query being constructed.
     * @param mixed $value The value to compare against using the equality operator.
     * @return string The updated query string including the extended WHERE clause with the equality comparison.
     * @throws BuilderException If an equality comparison is attempted without a prior WHERE clause.
     */
    private function baseEqualsTo(string $query, mixed $value): string
    {
        // Existance conditions
        $this->validateWhereStatementFilterOperator('=', $query);

        $oldWhere = $this->where;
        $newWhere = $this->getNewWhere('=', $value);

        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Appends a 'not equals to' comparison to the WHERE clause of the query.
     *
     * Validates the presence of a WHERE clause and extends it with a 'not equals to'
     * comparison operator ('!=' or '<>'), followed by the specified value. This ensures
     * the WHERE clause accurately reflects the intended criteria.
     *
     * @param string $query The current SQL query being constructed.
     * @param mixed $value The value to use in the 'not equals to' comparison.
     * @param string $notEqualsToOperator The operator for the 'not equals to' comparison.
     * @return string The updated query string including the extended WHERE clause.
     * @throws BuilderException If attempting to use an unsupported 'not equals to' operator or without a prior WHERE clause.
     */
    private function baseNotEqualsTo(string $query, mixed $value, string $notEqualsToOperator): string
    {
        // Existance conditions
        $this->validateWhereStatementFilterOperator($notEqualsToOperator, $query);

        if ($notEqualsToOperator !== '!=' && $notEqualsToOperator !== '<>') {
            throw new BuilderException(
                'Invalid not equals to operator: ' . $notEqualsToOperator,
                $this->invalidNotEqualsToOperatorExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->getNewWhere($notEqualsToOperator, $value);
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Appends a 'less than' comparison to the WHERE clause of the query.
     *
     * After ensuring a WHERE clause is present, this method extends it with a 'less than' ('<')
     * comparison operator followed by the specified value. The method seamlessly updates the query,
     * incorporating this new condition to reflect the intended filtering criteria accurately.
     *
     * This approach allows for granular control over the query's filtering logic, enabling the
     * construction of more complex and precise query conditions.
     *
     * @param string $query The current SQL query under construction.
     * @param mixed $value The value against which the column specified in the WHERE clause is to be compared.
     *                     The method handles both numerical and string values appropriately, ensuring the
     *                     generated SQL is syntactically correct.
     * @return string The modified SQL query string, now including the 'less than' comparison in the WHERE clause.
     * @throws BuilderException If a 'less than' comparison is attempted without a prior WHERE clause, reflecting
     *                          a misunderstanding or error in the order of query construction.
     */
    private function baseLessThan(string $query, mixed $value): string
    {
        // Existance conditions
        $this->validateWhereStatementFilterOperator('<', $query);

        $oldWhere = $this->where;
        $newWhere = $this->getNewWhere('<', $value);
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }
}
