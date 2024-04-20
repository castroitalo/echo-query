<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery;

use CastroItalo\EchoQuery\Exceptions\BuilderException;
use CastroItalo\EchoQuery\Traits\BuilderFrom;
use CastroItalo\EchoQuery\Traits\BuilderJoin;
use CastroItalo\EchoQuery\Traits\BuilderSelect;
use CastroItalo\EchoQuery\Traits\BuilderWhere;

/**
 * Main class to construct SQL queries through a fluent interface.
 *
 * The Builder class facilitates the construction of SQL queries by providing a fluent interface to
 * define SELECT, FROM, and WHERE clauses, among other SQL components. It leverages traits to separate
 * and organize the logic of constructing different parts of a query, promoting code reusability and maintainability.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery
 */
final class Builder
{
    use BuilderSelect;
    use BuilderFrom;
    use BuilderWhere;
    use BuilderJoin;

    /**
     * Holds the current state of the SQL query being constructed.
     *
     * @var string $query The SQL query string in its current form.
     */
    private string $query = '';

    /**
     * Initiates or appends a SELECT clause to the query.
     *
     * This method takes one or more columns as arguments, each represented as an array where the first element is
     * the column name and the optional second element is the alias. It constructs or appends a SELECT clause to the
     * current query based on the input columns.
     *
     * @param array ...$columns An array of columns to be selected, optionally including aliases.
     * @return self Returns $this to enable method chaining.
     */
    public function select(array ...$columns): self
    {
        $this->query = $this->baseSelect($this->query, $columns);

        return $this;
    }

    /**
     * Defines the FROM part of the query with support for subqueries.
     *
     * Sets or appends the FROM clause of the query. If the $subQueryFrom parameter is true, treats the $tableName
     * as a subquery. Otherwise, $tableName is treated as a regular table name. An optional table alias can also be specified.
     *
     * @param string $tableName The name of the table or subquery.
     * @param string|null $tableAlias An optional alias for the table or subquery.
     * @param bool $subQueryFrom Indicates whether $tableName is a subquery.
     * @return self Returns $this to enable method chaining.
     */
    public function from(string $tableName, ?string $tableAlias = null, bool $subQueryFrom = false): self
    {
        if ($subQueryFrom === true) {
            $this->query = $this->baseSubQueryFrom($this->query, $tableName, $tableAlias);
        } elseif ($subQueryFrom === false) {
            $this->query = $this->baseFrom($this->query, $tableName, $tableAlias);
        }

        return $this;
    }

    /**
     * Starts or extends a WHERE clause with the specified column name.
     *
     * Begins or appends a condition to the WHERE clause of the query using the provided column name. The condition
     * can later be completed with a comparison operator and value through other methods.
     *
     * @param string $columnName The column name to use in the WHERE condition.
     * @return self Returns $this to enable method chaining.
     */
    public function where(string $columnName): self
    {
        $this->query = $this->baseWhere($this->query, $columnName);

        return $this;
    }

    /**
     * Appends an '=' comparison operator and value to the current WHERE clause.
     *
     * @param mixed $value The value to compare against.
     * @return self Returns $this to enable method chaining.
     */
    public function equalsTo(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '=', $value);

        return $this;
    }

    /**
     * Appends a '!=' or '<>' comparison operator and value to the current WHERE clause.
     *
     * @param mixed $value The value to compare against.
     * @param string $notEqualsToOperator The operator to use for the comparison ('!=' by default).
     * @return self Returns $this to enable method chaining.
     * @throws BuilderException If an unsupported comparison operator is used.
     */
    public function notEqualsTo(mixed $value, string $notEqualsToOperator = '!='): self
    {
        // Validate not equals to comparison operator
        if (!in_array($notEqualsToOperator, ['!=', '<>'])) {
            throw new BuilderException(
                'Invalid ' . $notEqualsToOperator . ' comparison operator',
                $this->invalidComparisonOperatorExceptionCode,
            );
        }

        $this->query = $this->baseComparisonOperator($this->query, $notEqualsToOperator, $value);

        return $this;
    }

    /**
     * Appends a '<' comparison operator and value to the current WHERE clause.
     *
     * @param mixed $value The value to compare against.
     * @return self Returns $this to enable method chaining.
     */
    public function lessThan(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '<', $value);

        return $this;
    }

    /**
     * Appends a '<=' comparison operator and value to the current WHERE clause.
     *
     * @param mixed $value The value to compare against.
     * @return self Returns $this to enable method chaining.
     */
    public function lessThanEqualsTo(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '<=', $value);

        return $this;
    }

    /**
     * Appends a '>' comparison operator and value to the current WHERE clause.
     *
     * @param mixed $value The value to compare against.
     * @return self Returns $this to enable method chaining.
     */
    public function greaterThan(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '>', $value);

        return $this;
    }

    /**
     * Appends a '>=' comparison operator and value to the current WHERE clause.
     *
     * @param mixed $value The value to compare against.
     * @return self Returns $this to enable method chaining.
     */
    public function greaterThanEqualsTo(mixed $value): self
    {
        $this->query = $this->baseComparisonOperator($this->query, '>=', $value);

        return $this;
    }

    /**
     * Appends logical operators AND and a column name to the WHERE clause.
     *
     * These methods allow for the logical extension of WHERE clauses by appending conditions with logical
     * operators AND followed by column names. This enables complex query conditions to be constructed.
     *
     * @param string $columnName The column name to apply the logical operator to.
     * @return self Returns $this to enable method chaining.
     */
    public function and(string $columnName): self
    {
        $this->query = $this->baseLogicalOperator($this->query, 'AND', $columnName);

        return $this;
    }

    /**
     * Appends logical operators OR and a column name to the WHERE clause.
     *
     * These methods allow for the logical extension of WHERE clauses by appending conditions with logical
     * operators OR followed by column names. This enables complex query conditions to be constructed.
     *
     * @param string $columnName The column name to apply the logical operator to.
     * @return self Returns $this to enable method chaining.
     */
    public function or(string $columnName): self
    {
        $this->query = $this->baseLogicalOperator($this->query, 'OR', $columnName);

        return $this;
    }

    /**
     * Appends logical operators NOT and a column name to the WHERE clause.
     *
     * These methods allow for the logical extension of WHERE clauses by appending conditions with logical
     * operators NOT followed by column names. This enables complex query conditions to be constructed.
     *
     * @param string $columnName The column name to apply the logical operator to.
     * @return self Returns $this to enable method chaining.
     */
    public function not(string $columnName): self
    {
        $this->query = $this->baseLogicalOperator($this->query, 'NOT', $columnName);

        return $this;
    }

    /**
     * Appends a 'LIKE' pattern matching condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding a 'LIKE' pattern matching condition to the query.
     * It allows for matching parts of a column against a specified pattern.
     *
     * @param string $pattern The pattern to match against.
     * @return self Returns $this to enable method chaining.
     */
    public function like(string $pattern): self
    {
        $this->query = $this->basePatternMatching($this->query, 'LIKE', $pattern);

        return $this;
    }

    /**
     * Appends a 'NOT LIKE' pattern matching condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding a 'NOT LIKE' pattern matching condition to the query.
     * It allows for specifying parts of a column that should not match a given pattern.
     *
     * @param string $pattern The pattern to exclude in matches.
     * @return self Returns $this to enable method chaining.
     */
    public function notLike(string $pattern): self
    {
        $this->query = $this->basePatternMatching($this->query, 'NOT LIKE', $pattern);

        return $this;
    }

    /**
     * Appends a 'BETWEEN' range condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding a 'BETWEEN' condition, specifying that a column's value
     * must lie within a specified range, inclusive of the boundary values.
     *
     * @param mixed $start The lower boundary of the range.
     * @param mixed $end The upper boundary of the range.
     * @return self Returns $this to enable method chaining.
     */
    public function between(mixed $start, mixed $end): self
    {
        $this->query = $this->baseRangeCondition($this->query, 'BETWEEN', $start, $end);

        return $this;
    }

    /**
     * Appends a 'NOT BETWEEN' range condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding a 'NOT BETWEEN' condition, specifying that a column's value
     * must not lie within a specified range, exclusive of the boundary values.
     *
     * @param mixed $start The lower boundary of the range.
     * @param mixed $end The upper boundary of the range.
     * @return self Returns $this to enable method chaining.
     */
    public function notBetween(mixed $start, mixed $end): self
    {
        $this->query = $this->baseRangeCondition($this->query, 'NOT BETWEEN', $start, $end);

        return $this;
    }

    /**
     * Appends an 'IN' list condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding an 'IN' condition to check if a column's value is within a specified list.
     * It enables the specification of multiple acceptable values for a single column, enhancing the flexibility of the query.
     *
     * @param array $list An array of values that the column can match.
     * @return self Returns $this to enable method chaining.
     */
    public function in(array $list): self
    {
        $this->query = $this->baseListcondition($this->query, 'IN', $list);

        return $this;
    }


    /**
     * Appends a 'NOT IN' list condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding a 'NOT IN' condition to exclude a column's values from a specified list.
     * It allows for excluding multiple specific values from the results, which can be particularly useful in filtering operations.
     *
     * @param array $list An array of values that the column should not match.
     * @return self Returns $this to enable method chaining.
     */
    public function notIn(array $list): self
    {
        $this->query = $this->baseListcondition($this->query, 'NOT IN', $list);

        return $this;
    }

    /**
     * Appends an 'IS NULL' condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding an 'IS NULL' condition to check if a column's value is null.
     * It is a crucial feature for filtering SQL queries based on null values in the database.
     *
     * @return self Returns $this to enable method chaining.
     */
    public function isNull(): self
    {
        $this->query = $this->baseNullConditions($this->query, 'IS NULL');

        return $this;
    }

    /**
     * Appends an 'IS NOT NULL' condition to the WHERE clause.
     *
     * This method extends the WHERE clause by adding an 'IS NOT NULL' condition to ensure a column's value is not null.
     * This condition is essential for filtering SQL queries to exclude null values and ensure data integrity.
     *
     * @return self Returns $this to enable method chaining.
     */
    public function isNotNull(): self
    {
        $this->query = $this->baseNullConditions($this->query, 'IS NOT NULL');

        return $this;
    }

    /**
     * Performs an INNER JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function innerJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'INNER', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an INNER JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function innerJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'INNER', $joinInfo, true);

        return $this;
    }

    /**
     * Performs an LEFT JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function leftJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'LEFT', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an LEFT JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function leftJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'LEFT', $joinInfo, true);

        return $this;
    }

    /**
     * Performs an RIGHT JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function rightJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'RIGHT', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an RIGHT JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function rightJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'RIGHT', $joinInfo, true);

        return $this;
    }

    /**
     * Performs an FULL JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function fullJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'FULL', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an FULL JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function fullJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'FULL', $joinInfo, true);

        return $this;
    }

    /**
     * Performs an CROSS JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function crossJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'CROSS', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an CROSS JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function crossJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'CROSS', $joinInfo, true);

        return $this;
    }

    /**
     * Performs an SELF JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function selfJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'SELF', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an SELF JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function selfJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'SELF', $joinInfo, true);

        return $this;
    }

    /**
     * Performs an NATURAL JOIN with the specified conditions.
     *
     * This method constructs an INNER JOIN clause for the query, allowing the query to include
     * data from multiple related tables based on specified join conditions.
     *
     * @param array ...$joinInfo An array detailing the join conditions.
     * @return self Returns $this to enable method chaining.
     */
    public function naturalJoin(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'NATURAL', $joinInfo, false);

        return $this;
    }

    /**
     * Performs an NATURAL JOIN with a subquery and specified conditions.
     *
     * Similar to the `innerJoin` method, this method constructs an INNER JOIN clause that includes a subquery.
     * This allows complex joins involving subqueries to be easily constructed and incorporated into the main query.
     *
     * @param array ...$joinInfo An array detailing the join conditions including subqueries.
     * @return self Returns $this to enable method chaining.
     */
    public function naturalJoinSub(array ...$joinInfo): self
    {
        $this->query = $this->baseJoin($this->query, 'NATURAL', $joinInfo, true);

        return $this;
    }

    /**
     * Appends a UNION operation to the query.
     *
     * This method allows for combining the results of two SELECT statements into a single result set
     * that includes all the records returned by both SELECT statements. This variant of UNION automatically
     * removes duplicate records.
     *
     * @param string $unionQuery The SQL query to union with the current query.
     * @return self Returns $this to enable method chaining.
     */
    public function union(string $unionQuery): self
    {
        $this->query = $this->baseUnion($this->query, $unionQuery, false);

        return $this;
    }

    /**
     * Appends a UNION ALL operation to the query.
     *
     * This method combines the results of two SELECT statements into a single result set, including all duplicates.
     * It is useful when you need to include all duplicates between two or more datasets.
     *
     * @param string $unionQuery The SQL query to union with the current query.
     * @return self Returns $this to enable method chaining.
     */
    public function unionAll(string $unionQuery): self
    {
        $this->query = $this->baseUnion($this->query, $unionQuery, true);

        return $this;
    }

    /**
     * Converts the built query to a string.
     *
     * This method allows the Builder instance to be used directly in contexts expecting a string,
     * returning the SQL query string that has been constructed.
     *
     * @return string The constructed SQL query.
     */
    public function __toString(): string
    {
        return $this->query;
    }
}
