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
}
