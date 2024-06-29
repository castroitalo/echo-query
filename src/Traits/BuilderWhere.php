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
     * The error code for when an invalid pattern is used in a pattern matching operation.
     *
     * This error code is triggered when a pattern provided for a pattern matching operation (e.g., LIKE, NOT LIKE)
     * is found to be invalid or empty. This validation ensures that the SQL pattern matching operation does not
     * run with malformed or potentially harmful input, thus maintaining the integrity and security of the database
     * interaction.
     *
     * @var int $invalidPatternExceptionCode Error code indicating an invalid pattern for pattern matching operations.
     */
    private int $invalidPatternExceptionCode = BuilderExceptionsCode::InvalidPatternExceptionCode->value;

    /**
     * Error code for invalid HAVING statements within SQL queries.
     *
     * This property holds the error code used when an invalid or malformed HAVING clause is detected
     * during the construction of SQL queries. The error ensures that only syntactically correct and
     * logically valid HAVING clauses are included in SQL statements.
     *
     * @var int
     */
    private int $invalidHavingExceptionCode = BuilderExceptionsCode::InvalidHavingStatement->value;

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
        // Existence conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Comparison Operator ' . $comparisonOperator . ' must have a previsou WHERE statemen.',
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
        $newWhere = $this->where .= (' ' . $comparisonOperator . ' ' . $value);
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Constructs a HAVING clause for SQL queries based on the provided condition.
     *
     * This method allows for the dynamic insertion of a HAVING clause into an ongoing SQL query construction,
     * enabling aggregate functions to be filtered according to specified conditions. It checks the validity
     * of the HAVING expression to ensure the integrity and functionality of the SQL query. If the condition is
     * found to be invalid (e.g., empty or syntactically incorrect), a BuilderException is thrown.
     *
     * @param string $query The SQL query string being constructed.
     * @param string $having The condition to be applied in the HAVING clause.
     * @return string The modified SQL query including the HAVING clause.
     * @throws BuilderException If the HAVING condition is empty or otherwise invalid.
     */
    private function baseHaving(string $query, string $having): string
    {
        // Validate having
        if (empty($having)) {
            throw new BuilderException(
                'Invalid HAVING statement.',
                $this->invalidHavingExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->where .= ' HAVING ' . $having . ' ';
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Dynamically applies a logical operator (AND, OR) within an existing WHERE clause of the SQL query.
     *
     * This method allows for the extension of the WHERE clause by adding additional conditions using
     * logical operators ('AND', 'OR'). It ensures the logical integrity of the SQL query by verifying
     * the presence of an initial WHERE clause and the validity of the column name provided for the new condition.
     * If these conditions are not met, a BuilderException is thrown with the corresponding error code.
     *
     * @param string $query The current SQL query being constructed.
     * @param string $logicalOperator The logical operator to apply ('AND', 'OR'). Must be validated
     *                                against a predefined set of allowed logical operators if applicable.
     * @param string $columnName The column name to be used in the logical operation. Must be non-empty
     *                           and valid within the context of the database schema.
     * @return string The updated SQL query including the extended WHERE clause with the applied logical operator.
     * @throws BuilderException If no previous WHERE clause is found or if the column name is invalid.
     */
    private function baseLogicalOperator(string $query, string $logicalOperator, string $columnName): string
    {
        // Existence conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Logical Operator ' . $logicalOperator . ' must have a previsou WHERE statemen.',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }

        if (empty($columnName)) {
            throw new BuilderException(
                'Invalid ' . $logicalOperator . ' logical operator column name.',
                $this->invalidColumnNameExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->where .= (' ' . $logicalOperator . ' ' . $columnName);
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Dynamically applies a pattern matching operator within an existing WHERE clause of the SQL query.
     *
     * This method extends the WHERE clause by incorporating a specified pattern matching operator and pattern,
     * such as LIKE or NOT LIKE. It ensures the WHERE clause construction adheres to SQL syntax rules and checks
     * for the presence of a WHERE clause before modification. If these conditions are not met, a BuilderException
     * is thrown with the appropriate error code. The method also validates that the provided pattern is non-empty.
     *
     * @param string $query The current SQL query being constructed.
     * @param string $patternMatchingOperator The pattern matching operator to apply (e.g., 'LIKE', 'NOT LIKE').
     * @param string $pattern The pattern to match against, which must not be empty.
     * @return string The updated SQL query including the extended WHERE clause with the applied pattern matching.
     * @throws BuilderException If no previous WHERE clause is found, if the pattern is empty, or if there's another
     *                          issue with pattern matching logic.
     */
    private function basePatternMatching(string $query, string $patternMatchingOperator, string $pattern): string
    {
        // Existence conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Pattern matching ' . $patternMatchingOperator . ' must have a previous WHERE statement.',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }

        if (empty($pattern)) {
            throw new BuilderException(
                'Invalid pattern matching pattern.',
                $this->invalidPatternExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->where .= ' ' . $patternMatchingOperator . ' \'' . $pattern . '\' ';
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Applies a range condition within an existing WHERE clause of the SQL query.
     *
     * This method extends the WHERE clause to include a range condition, typically used with 'BETWEEN' or
     * similar operators. It adds a condition that checks if a value lies within a specified start and end range.
     * Before modifying the query, it checks for the existence of a previous WHERE clause and validates that both
     * start and end values are specified. If these conditions are not met, a BuilderException is thrown with the
     * corresponding error code.
     *
     * @param string $query The current SQL query being constructed.
     * @param string $rangeCondition The range condition operator to apply (e.g., 'BETWEEN').
     * @param mixed $start The start value of the range.
     * @param mixed $end The end value of the range.
     * @return string The updated SQL query including the extended WHERE clause with the new range condition.
     * @throws BuilderException If no previous WHERE clause is found, or if the start or end values are not properly specified.
     */
    private function baseRangeCondition(string $query, string $rangeCondition, mixed $start, mixed $end): string
    {
        // Existence conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Range condition ' . $rangeCondition . ' must have a previous WHERE statement',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->where .= ' ' . $rangeCondition . ' ' . $start . ' AND ' . $end . ' ';
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Dynamically applies a list condition within an existing WHERE clause of the SQL query.
     *
     * This method extends the WHERE clause by incorporating a specified list condition operator (e.g., 'IN', 'NOT IN')
     * and a list of values, which must be non-empty. The method constructs a condition that checks if a column's value
     * is included or excluded from the specified list. It ensures the logical integrity of the SQL query by verifying
     * the presence of an initial WHERE clause and the non-emptiness of the list before modifying the query.
     *
     * @param string $query The current SQL query being constructed.
     * @param string $listCondition The list condition operator to apply ('IN', 'NOT IN').
     * @param array $list The array of values to include in the list condition.
     * @return string The updated SQL query including the extended WHERE clause with the applied list condition.
     * @throws BuilderException If no previous WHERE clause is found, if the list is empty, or if another
     *                          issue arises with list condition logic.
     */
    private function baseListcondition(string $query, string $listCondition, array $list): string
    {
        // Existence conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'List condition ' . $listCondition . ' must have a previous WHERE statement',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }

        $listValue = ' (';
        $listCounter = 1;

        foreach ($list as $element) {
            if (($listCounter + 1) > sizeof($list)) {
                $listValue .= ' ' . $element . ' ) ';

                break;
            }

            $listValue .= ' ' . $element . ', ';
            $listCounter += 1;
        }

        $oldWhere = $this->where;
        $newWhere = $this->where .= ' ' . $listCondition . ' ' . $listValue . ' ';
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }

    /**
     * Applies a null condition ('IS NULL' or 'IS NOT NULL') within an existing WHERE clause of the SQL query.
     *
     * This method extends the WHERE clause to include a null condition check. It validates the presence
     * of a WHERE clause before modifying the query, throwing an exception for any discrepancies.
     *
     * @param string $query The current SQL query being constructed.
     * @param string $nullCondition The null condition operator to apply ('IS NULL' or 'IS NOT NULL').
     * @return string The updated SQL query including the extended WHERE clause with the new null condition.
     * @throws BuilderException If no previous WHERE clause is found or if there's another issue with null condition logic.
     */
    public function baseNullConditions(string $query, string $nullCondition): string
    {
        // Existence conditions
        if (! str_contains($query, 'WHERE') || is_null($this->where)) {
            throw new BuilderException(
                'Null condition ' . $nullCondition . ' must have a previous WHERE statement',
                $this->noPreviousWhereStatementExceptionCode,
            );
        }

        $oldWhere = $this->where;
        $newWhere = $this->where .= ' ' . $nullCondition . ' ';
        $query = str_replace($oldWhere, $newWhere, $query);

        return $query;
    }
}
