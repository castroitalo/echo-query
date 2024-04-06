<?php

declare(strict_types=1);

namespace tests\Traits;

use CastroItalo\EchoQuery\Builder;
use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;
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
     * Tests the correct appending of an equality comparison operator in a WHERE clause.
     *
     * Constructs an SQL query using the Builder class and applies an equals to ('=') comparison operator
     * in the WHERE clause. The test verifies that the resulting SQL string accurately reflects the intended
     * comparison, including the correct syntax and the specified value for comparison. Whitespace differences
     * in the comparison are disregarded to focus on structural and syntactical accuracy.
     *
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testWhereStatementEqualsToComparisonOperator(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->equalsTo(1)
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one = 1 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the exception thrown when an equals to comparison is attempted without a preceding WHERE clause.
     *
     * Verifies that attempting to apply an equals to comparison operator without an established WHERE clause
     * throws a BuilderException. The exception should carry the specific error code and message indicating
     * the requirement for a preceding WHERE statement to use comparison operators.
     *
     * @return void
     * @throws BuilderException
     */
    public function testWhereStatementEqualsToComparisonOperatorNoPreviousWhereException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::NoPreviousWhereStatement->value);
        $this->expectExceptionMessage('Comparison operator = must have a previsou WHERE statemen.');
        $this->builder->equalsTo(5);
    }
}
