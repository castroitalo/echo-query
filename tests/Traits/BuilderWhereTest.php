<?php

declare(strict_types=1);

namespace tests\Traits;

use CastroItalo\EchoQuery\Builder;
use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\RequiresPhpunit;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for verifying WHERE clause construction in the Builder class.
 *
 * This class tests the Builder class's ability to correctly form WHERE clauses in SQL queries,
 * ensuring that the syntax is correct and that the WHERE clause logically and functionally fits
 * within the context of the overall query.
 *
 * @requires PHP ^8.2
 * @requires PHPUnit ^10.5
 */
#[RequiresPhp('^8.2')]
#[RequiresPhpunit('^10.5')]
final class BuilderWhereTest extends TestCase
{
    /**
     * Builder instance for use in tests.
     *
     * @var Builder|null $builder An instance of the Builder class, used throughout the tests to construct SQL queries.
     */
    private ?Builder $builder = null;

    /**
     * Sets up the environment for each test.
     *
     * Initializes a new instance of the Builder class before each test method is run,
     * ensuring a clean slate for testing the WHERE clause functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->builder = new Builder();
    }

    /**
     * Tests the correct formation of a WHERE clause in an SQL query.
     *
     * Constructs an SQL query using the Builder class, appending a WHERE clause to the query.
     * The test verifies that the resulting SQL string matches the expected format, specifically
     * ensuring that the WHERE clause is correctly appended to the query and that the column
     * referenced in the WHERE clause is accurately represented. Whitespace differences are
     * ignored in the comparison to focus on structural accuracy.
     *
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testWhereStatement(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct ' .
            'FROM table_one AS to ' .
            'WHERE column_one';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the behavior when no previous SELECT statement exists before a WHERE clause is added.
     *
     * Verifies that attempting to add a WHERE clause without a preceding SELECT statement throws
     * a BuilderException with the specific error code and message related to the absence of a SELECT statement.
     *
     * @return void
     * @throws BuilderException
     */
    public function testWhereStatementNoPreviousSelectStatementException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousSelectStatement->value);
        $this->expectExceptionMessage('No previous SELECT statement.');
        $this->builder->where('column_one');
    }

    /**
     * Tests the exception thrown when an empty column name is provided to a WHERE clause.
     *
     * Ensures that providing an empty string as a column name in a WHERE clause results in a BuilderException
     * with an appropriate error code and message indicating an invalid WHERE statement column name.
     *
     * @return void
     * @throws BuilderException
     */
    public function testWhereStatementNoEmptyColumnNameException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidColumnName->value);
        $this->expectExceptionMessage('Invalid WHERE statement column name.');
        $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'cw'],
        )
            ->from('table_one', 'to')
            ->where('')
            ->__toString();
    }

    /**
     * Data provider for testWhereStatementComparisonOperatorsNonStringValues.
     *
     * Supplies a series of comparison operator method names and their corresponding SQL symbols
     * to the testWhereStatementComparisonOperatorsNonStringValues test method. This enables
     * parameterized testing of different comparison operators in the Builder class to ensure
     * that each operator correctly formats the WHERE clause with non-string values.
     *
     * Each array entry consists of:
     * - The method name as used in the Builder class for constructing SQL comparison operators.
     * - The expected SQL symbol that represents the comparison operation (e.g., '=', '<', '>=').
     *
     * This method supports testing the flexibility and correctness of the Builder's handling
     * of comparison operators with various data types, ensuring the generated SQL is syntactically
     * correct and matches expected output.
     *
     * @return array An array of arrays, each containing the method name and expected SQL symbol.
     */
    public static function whereStatementComparisonOperatorsNonStringValuesTestDataProvider(): array
    {
        return [
            'equals_to' => ['equalsTo', '='],
            'not_equals_to_default' => ['notEqualsTo', '!='],
            'less_than' => ['lessThan', '<'],
            'greater_than' => ['greaterThan', '>'],
            'less_than_equals_to' => ['lessThanEqualsTo', '<='],
            'greater_than_equals_to' => ['greaterThanEqualsTo', '>='],
        ];
    }

    /**
     * Tests the Builder's ability to handle comparison operators with non-string values in WHERE clauses.
     *
     * This test evaluates the flexibility and correctness of the Builder class in constructing
     * WHERE clauses using various comparison operators (such as equals, not equals, less than,
     * greater than, etc.) with non-string values. It uses a data provider to supply different
     * comparison operators and their expected SQL symbols, constructing a query for each case
     * and comparing the generated SQL to the expected outcome.
     *
     * The ability to accurately interpret and incorporate non-string values into SQL queries
     * is crucial for a wide range of applications, ensuring that the Builder class can be
     * effectively used in diverse scenarios requiring dynamic query generation. This test
     * demonstrates the class's capability to produce valid SQL statements that correctly
     * reflect the intended comparisons, regardless of the value type being compared.
     *
     * @param string $comparisonOperatorMethod The method name in the Builder class for the comparison operator.
     * @param string $comparisonOperatorSymbol The expected SQL symbol for the comparison operation.
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not match the expected format.
     */
    #[DataProvider('whereStatementComparisonOperatorsNonStringValuesTestDataProvider')]
    public function testWhereStatementComparisonOperatorsNonStringValues(
        string $comparisonOperatorMethod,
        string $comparisonOperatorSymbol,
    ): void {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->$comparisonOperatorMethod(5)
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to WHERE column_one ' . $comparisonOperatorSymbol . ' 5 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests custom "not equals to" comparison operator in a WHERE clause.
     *
     * Evaluates the ability of the Builder class to handle a custom "not equals to"
     * comparison operator within a WHERE clause. The test constructs an SQL query using
     * the Builder's `notEqualsTo` method with a custom operator ('<>') and a non-string
     * value. It then checks if the generated SQL query correctly incorporates the custom
     * operator and value within the WHERE clause.
     *
     * This test ensures that the Builder class is not only capable of using standard
     * comparison operators but can also correctly handle custom operators provided by
     * the user. This flexibility is crucial for supporting a wide range of SQL dialects
     * and specific query requirements.
     *
     * @return void
     * @throws ExpectationFailedException If the generated SQL does not match the expected output.
     */
    public function testWhereStatementCustomNotEqualsToComparisonOperator(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->notEqualsTo(5, '<>')
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to WHERE column_one <> 5 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the exception handling when a comparison operator is used without a preceding WHERE clause.
     *
     * This test verifies that the Builder class enforces correct SQL syntax by throwing a
     * BuilderException when a comparison operator (in this case, `equalsTo`) is used without
     * first specifying a column with a WHERE clause. The test expects a specific error code
     * and message to be associated with the exception, emphasizing the requirement for a
     * logical sequence in building SQL queries, where a column must be specified before it
     * can be compared to a value.
     *
     * Ensuring this sequence enforces the structural integrity of the SQL query and prevents
     * runtime errors during query execution. This test highlights the robustness of the Builder
     * class's error handling and its adherence to SQL syntax rules.
     *
     * @return void
     * @throws BuilderException If a comparison operator is invoked without a preceding WHERE clause.
     */
    public function testWhereStatementComparisonOperatorNoPreviousWhereStatement(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('Comparison Operator = must have a previsou WHERE statemen.');
        $this->builder->equalsTo(5);
    }

    /**
     * Provides a set of logical operators for testing their correct application in WHERE clauses.
     *
     * Supplies data for tests that verify the Builder's ability to handle logical operators (e.g., AND, OR, NOT)
     * within WHERE clauses. This data provider facilitates the testing of logical operator integration,
     * ensuring that logical conditions are correctly formatted and applied in the SQL queries generated
     * by the Builder class.
     *
     * @return array An array of logical operators for testing, each with the method name to test and the expected
     *               SQL logical operator expression.
     */
    public static function whereStatementLogicalOperatorsTestDataProvider(): array
    {
        return [
            'and' => ['and', 'AND'],
            'or' => ['or', 'OR'],
            'not' => ['not', 'NOT'],
        ];
    }

    /**
     * Tests the correct application of logical operators in WHERE clauses.
     *
     * Verifies the Builder class's capability to correctly apply logical operators (AND, OR, NOT) within
     * the WHERE clauses of SQL queries. It ensures that the logical conditions are properly formatted and
     * logically follow the initial condition specified in the WHERE clause. The test uses a data provider
     * to supply different logical operators and assesses the SQL query string generated by the Builder class
     * for correctness and adherence to SQL standards.
     *
     * @param string $logicalOperatorMethod The method name corresponding to the logical operator in the Builder class.
     * @param string $logicalOperatorExpression The expected SQL logical operator expression (e.g., 'AND', 'OR', 'NOT').
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not incorporate the logical operator as expected.
     */
    #[DataProvider('whereStatementLogicalOperatorsTestDataProvider')]
    public function testWhereStatementLogicalOperators(
        string $logicalOperatorMethod,
        string $logicalOperatorExpression,
    ): void {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->equalsTo(2)
            ->$logicalOperatorMethod('column_two')
            ->notEqualsTo('something')
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct' .
            'FROM table_one AS to' .
            'WHERE column_one = 2' .
            $logicalOperatorExpression . ' column_two != \'something\'';

        $this->assertEquals(
            str_replace(' ', '', $actual),
            str_replace(' ', '', $expect),
        );
    }

    /**
     * Tests exception handling for logical operators without a preceding WHERE clause.
     *
     * Verifies that using a logical operator method without a preceding WHERE clause in the query
     * construction process throws a BuilderException. This test ensures that the Builder class enforces
     * correct SQL query structure by requiring a valid WHERE clause before logical conditions can be applied.
     * An appropriate error code and message are expected to be thrown to indicate the absence of a WHERE clause.
     *
     * @return void
     * @throws BuilderException If a logical operator is used without a preceding WHERE clause.
     */
    public function testWhereStatementLogicalOperatorsNoPreviousWhereStatementException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('Logical Operator AND must have a previsou WHERE statemen.');
        $this->builder->and('column_two');
    }

    /**
     * Tests exception handling for invalid column names with logical operators.
     *
     * Ensures that providing an invalid (e.g., empty) column name to a logical operator method
     * within a WHERE clause throws a BuilderException. This test checks the Builder class's validation
     * of column names in the context of logical operations, expecting an error code and message that
     * indicate an invalid column name for the logical operator.
     *
     * @return void
     * @throws BuilderException If an invalid column name is provided for a logical operator in a WHERE clause.
     */
    public function testWhereStatementLogicalOperatorsInvalidColumnNameException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidColumnName->value);
        $this->expectExceptionMessage('Invalid AND logical operator column name.');
        $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->equalsTo(2)
            ->and('')
            ->notEqualsTo('something')
            ->__toString();
    }

    /**
     * Tests the Builder's capability to correctly apply pattern matching operators like 'LIKE' in WHERE clauses.
     *
     * Verifies the proper functionality of the Builder's ability to use 'LIKE' in constructing SQL queries.
     * The test constructs a query using the Builder, appends a WHERE clause with a 'LIKE' condition, and
     * asserts that the resulting SQL string is correctly formatted and matches the expected output. This test
     * ensures that the Builder handles SQL pattern matching accurately, reflecting the specified pattern in the query.
     *
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not match the expected format.
     */
    public function testWhereStatementPatternMatchingLike(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->like('%something')
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct' .
            'FROM table_one AS to' .
            'WHERE column_one LIKE \'%something\'';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the Builder's handling of the 'NOT LIKE' pattern matching operator in WHERE clauses.
     *
     * This test checks the Builder's capability to incorporate the 'NOT LIKE' operator into the WHERE clause,
     * constructing an SQL query to reflect this condition. It confirms that the Builder accurately interprets
     * and applies the 'NOT LIKE' operator, producing an SQL string that matches the expected format and correctly
     * incorporates the specified pattern.
     *
     * @return void
     * @throws ExpectationFailedException If the generated SQL does not match the expected output.
     */
    public function testWhereStatementPatternMatchingNotLike(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->notLike('%something')
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct' .
            'FROM table_one AS to' .
            'WHERE column_one NOT LIKE \'%something\'';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the exception handling when a pattern matching operator is used without a preceding WHERE clause.
     *
     * Verifies that the Builder class enforces correct SQL query structure by throwing a BuilderException
     * when a pattern matching operator such as 'LIKE' is used without first defining a WHERE clause. This test
     * emphasizes the importance of logical sequence in SQL query construction, ensuring that a WHERE clause
     * must precede the use of pattern matching operators to maintain query validity and prevent execution errors.
     *
     * @return void
     * @throws BuilderException If a pattern matching operator is invoked without a preceding WHERE clause.
     */
    public function testWhereStatementPatternMatchingNoPreviousWhereStatementException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('Pattern matching LIKE must have a previous WHERE statement.');
        $this->builder->like('%pattern%');
    }

    /**
     * Tests exception handling for invalid patterns in pattern matching operations.
     *
     * Ensures that providing an invalid (e.g., empty) pattern to a pattern matching operator within a WHERE clause
     * results in a BuilderException. This test checks the Builder class's validation of patterns, expecting an error
     * code and message that indicate an invalid pattern for the pattern matching operation. It confirms the Builder's
     * robust error handling and its adherence to SQL syntax rules in the context of pattern matching.
     *
     * @return void
     * @throws BuilderException If an invalid pattern is provided for a pattern matching operator in a WHERE clause.
     */
    public function testWhereStatementPatternMatchingInvalidPatternException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidPatternExceptionCode->value);
        $this->expectExceptionMessage('Invalid pattern matching pattern.');
        $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->like('');
    }

    /**
     * Tests the application of the 'BETWEEN' condition within a WHERE clause.
     *
     * This test assesses the Builder's functionality to apply a 'BETWEEN' condition, ensuring that
     * it correctly asserts the column's value lies within a specified range. It constructs a query
     * incorporating this condition and checks if the generated SQL matches the expected structure
     * and content.
     *
     * @return void
     * @throws ExpectationFailedException If the SQL generated does not conform to expectations.
     */
    public function testWhereStatementRangeConditionBetween(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->between(1, 10)
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one BETWEEN 1 AND 10 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the application of the 'NOT BETWEEN' condition within a WHERE clause.
     *
     * This test assesses the Builder's functionality to apply a 'BETWEEN' condition, ensuring that
     * it correctly asserts the column's value lies within a specified range. It constructs a query
     * incorporating this condition and checks if the generated SQL matches the expected structure
     * and content.
     *
     * @return void
     * @throws ExpectationFailedException If the SQL generated does not conform to expectations.
     */
    public function testWhereStatementRangeConditionNotBetween(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->notBetween(1, 10)
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one NOT BETWEEN 1 AND 10 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests exception handling when a range condition is used without a preceding WHERE clause.
     *
     * Verifies that attempting to apply a range condition such as 'BETWEEN' without a prior WHERE clause
     * throws a BuilderException, reflecting strict enforcement of SQL syntax rules by the Builder class.
     * The test ensures that logical order is maintained in SQL query construction.
     *
     * @return void
     * @throws BuilderException If the range condition is applied without a preceding WHERE clause.
     */
    public function testWhereStatementRangeConditionNoPreviousWhereStatementException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('Range condition BETWEEN must have a previous WHERE statement');
        $this->builder->between(1, 10);
    }

    /**
     * Tests the Builder's capability to apply the 'IN' list condition correctly in WHERE clauses.
     *
     * This test verifies the Builder's functionality in handling 'IN' conditions, ensuring it correctly
     * constructs a part of the WHERE clause that includes a list of values the column can match.
     * It builds an SQL query using the Builder, applies an 'IN' condition, and asserts that the
     * generated SQL string correctly reflects the condition with expected values.
     *
     * @return void
     * @throws ExpectationFailedException If the generated SQL does not conform to expectations.
     */
    public function testWhereStatementListConditionIn(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->in(['value_one', 2, 'value_three'])
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one IN (\'value_one\', 2, \'value_three\') ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the Builder's handling of the 'NOT IN' list condition in WHERE clauses.
     *
     * This test checks the Builder's capability to apply a 'NOT IN' condition correctly, ensuring that
     * it excludes specified values from the results accurately. The query is constructed to include a 'NOT IN'
     * condition and is then compared to the expected SQL string to verify correct syntax and logic.
     *
     * @return void
     * @throws ExpectationFailedException If the generated SQL does not match the expected output.
     */
    public function testWhereStatementListConditionNotInt(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->notIn(['value_one', 2, 'value_three'])
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one NOT IN (\'value_one\', 2, \'value_three\') ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the exception handling when a list condition is used without a preceding WHERE clause.
     *
     * Verifies that the Builder class throws a BuilderException when an 'IN' list condition is attempted
     * without a prior WHERE clause. This test underscores the importance of adhering to SQL syntax rules,
     * ensuring that logical conditions are applied in the correct order.
     *
     * @return void
     * @throws BuilderException If the list condition is applied without a preceding WHERE clause.
     */
    public function testWhereStatementListConditionNoPreviosuWhereStatementException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('List condition IN must have a previous WHERE statement');
        $this->builder->in(['value_one', 2, 'value_three']);
    }

    /**
     * Tests the correct application of the 'IS NULL' condition within a WHERE clause.
     *
     * Verifies the Builder's ability to apply the 'IS NULL' condition accurately, ensuring
     * that the column specified evaluates to NULL. The resulting SQL query string is
     * examined to ensure it is correctly formatted according to expectations.
     *
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not match the expected format.
     */
    public function testWhereStatementNullConditionsIsNull(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->isNull()
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one IS NULL ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the correct application of the 'IS NOT NULL' condition within a WHERE clause.
     *
     * Checks the Builder's functionality in applying the 'IS NOT NULL' condition, ensuring
     * that the specified column does not evaluate to NULL. The SQL query generated is
     * validated against expected results to confirm proper syntax and condition application.
     *
     * @return void
     * @throws ExpectationFailedException If the generated SQL does not match the expected output.
     */
    public function testWhereStatementNullConditionsIsNotNull(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->isNotNull()
            ->__toString();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one IS NOT NULL ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the exception handling when a 'IS NULL' condition is used without a preceding WHERE clause.
     *
     * Verifies that the Builder class throws an appropriate BuilderException when attempting to
     * apply a 'IS NULL' condition without first defining a WHERE clause. The test ensures that
     * SQL syntax and query structure rules are enforced, maintaining logical query construction.
     *
     * @return void
     * @throws BuilderException If the null condition is invoked without a preceding WHERE clause.
     */
    public function testWhereStatementNullConditionNoPreviousWhereException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('Null condition IS NULL must have a previous WHERE statement');
        $this->builder->isNull();
    }

    /**
     * Tests the application of the HAVING clause in conjunction with WHERE conditions.
     *
     * This test verifies that the Builder can construct SQL queries that include both WHERE and HAVING clauses.
     * It specifically checks if the HAVING clause correctly filters the results of aggregate functions based on
     * the specified condition. The test involves counting entries that meet a certain condition specified in the
     * WHERE clause and then applying a HAVING clause to filter these results further.
     *
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not match the expected format.
     */
    public function testWhereHaving(): void
    {
        $actual = $this->builder->select(
            ['COUNT(column_one)', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one')
            ->where('column_two')
            ->equalsTo(10)
            ->having('COUNT(column_one)')
            ->greaterThan(5)
            ->__toString();
        $expect = ' SELECT COUNT(column_one) AS co, ' .
            ' column_two AS ct ' .
            ' FROM table_one ' .
            ' WHERE column_two = 10 ' .
            ' HAVING COUNT(column_one) > 5 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests exception handling for invalid HAVING clause expressions.
     *
     * Ensures that the Builder class throws a BuilderException when an invalid or empty expression is
     * passed to the HAVING clause of a query. This test highlights the importance of correct syntax and
     * logical expression formulation in HAVING clauses, which are crucial for the accurate execution of
     * SQL queries involving aggregate functions.
     *
     * @return void
     * @throws BuilderException If an invalid HAVING statement is provided.
     */
    public function testWhereHavingInvalidHavingException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidHavingStatement->value);
        $this->expectExceptionMessage('Invalid HAVING statement.');
        $this->builder->select(
            ['COUNT(column_one)', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one')
            ->where('column_two')
            ->equalsTo(10)
            ->having('')
            ->greaterThan(5)
            ->__toString();
    }
}
