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
 * Test suite for the SELECT statement functionality in the Builder class.
 *
 * This class contains tests that verify the correctness and reliability of
 * the SELECT statement construction within the Builder class of the EchoQuery library.
 * It tests various scenarios, including the inclusion of column aliases, the absence
 * of aliases, and error handling when invalid input is provided.
 *
 * @requires PHP ^8.2
 * @requires PHPUnit 10.5
 */
#[RequiresPhp('^8.2')]
#[RequiresPhpunit('^10.5')]
final class BuilderSelectTest extends TestCase
{
    /**
     * The Builder instance used in the tests.
     *
     * @var Builder|null $builder An instance of the Builder class to be used for testing SELECT statement functionality.
     */
    private ?Builder $builder = null;

    /**
     * Set up the test environment.
     *
     * Initializes a new Builder instance before each test is run, ensuring a clean
     * state for every test case.
     * @return void
     */
    protected function setUp(): void
    {
        $this->builder = new Builder();
    }

    /**
     * Tests the construction of a SELECT statement with column aliases.
     *
     * Verifies that the Builder class correctly constructs a SELECT statement
     * that includes column names with their respective aliases. The test checks
     * if the resulting SQL string matches the expected output, disregarding
     * differences in whitespace for comparison.
     *
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testSelectStatementWithAlias(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->getQuery();
        $expect = 'SELECT column_one AS co, column_two AS ct';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the construction of a SELECT statement without column aliases.
     *
     * Ensures that the Builder class can generate a SELECT statement comprising
     * multiple columns without any aliases. The test compares the generated SQL
     * string against the expected format, ignoring whitespace variations.
     *
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testSelectStatementWithoutAlias(): void
    {
        $actual = $this->builder->select(
            ['column_one'],
            ['column_two'],
        )
            ->getQuery();
        $expect = 'SELECT column_one, column_two';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the Builder's response to invalid input for SELECT column names.
     *
     * This test verifies that the Builder class throws the appropriate
     * BuilderException with a specific error message and code when an attempt
     * is made to construct a SELECT statement with invalid or missing column names.
     * It checks for the exception type, error code, and message accuracy.
     *
     * @return void
     * @throws BuilderException
     */
    public function testSelectStatementNoColumnNameException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidColumnName->value);
        $this->expectExceptionMessage('Invalid SELECT statement columns.');
        $this->builder->select([]);
    }

    /**
     * Tests the functionality of grouping query results using the GROUP BY statement.
     *
     * This test ensures that the Builder class can correctly append a GROUP BY clause
     * to a SELECT statement. It verifies that the SQL query correctly groups the results
     * by the specified columns, and matches the expected query structure, ignoring whitespace differences.
     *
     * @return void
     * @throws BuilderException If an unexpected error occurs during the query construction.
     * @throws ExpectationFailedException If the generated SQL does not match the expected result.
     */
    public function testGroupBy(): void
    {
        $actual = $this->builder->select(
            ['COUNT(column_one)', 'co'],
            ['SUM(column_two)', 'ct'],
        )
            ->from('table_one', 'to')
            ->groupBy('column_one', 'column_two')
            ->getQuery();
        $expect = ' SELECT COUNT(column_one) AS co, ' .
            ' SUM(column_two) AS ct ' .
            ' FROM table_one AS to ' .
            ' GROUP BY column_one, column_two ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the Builder's handling of invalid GROUP BY columns.
     *
     * This test case assesses the Builder's robustness by ensuring it throws an exception
     * when provided with an invalid or empty list of columns for the GROUP BY clause. It verifies
     * that the exception thrown is a BuilderException with the correct error message and code,
     * aligning with defined standards for error handling in the EchoQuery library.
     *
     * @return void
     * @throws BuilderException If the GROUP BY clause is constructed with invalid columns, expected to
     *                          throw with a specific error code and message.
     */
    public function testGroupByInvalidColumnsException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidGroupByColumns->value);
        $this->expectExceptionMessage('Invalid GROUP BY columns.');
        $this->builder->select(
            ['COUNT(column_one)', 'co'],
            ['SUM(column_two)', 'ct'],
        )
            ->from('table_one', 'to')
            ->groupBy()
            ->getQuery();
    }

    /**
     * Tests the functionality of ordering query results using the ORDER BY statement.
     *
     * This test ensures that the Builder class can correctly append a ORDER BY clause
     * to a SELECT statement. It verifies that the SQL query correctly orders the results
     * by the specified columns, and matches the expected query structure, ignoring whitespace differences.
     *
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testOrderBy(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->orderBy(
                ['column_one'],
                ['column_two', 'desc'],
            )
            ->getQuery();
        $expect = 'SELECT column_one AS co, column_two AS ct' .
            'FROM table_one AS to ' .
            'ORDER BY column_one, column_two DESC';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the Builder's handling of invalid ORDER BY columns.
     *
     * This test case assesses the Builder's robustness by ensuring it throws an exception
     * when provided with an invalid or empty list of columns for the ORDER BY clause. It verifies
     * that the exception thrown is a BuilderException with the correct error message and code,
     * aligning with defined standards for error handling in the EchoQuery library.
     *
     * @return void
     * @throws BuilderException If the ORDER BY clause is constructed with invalid columns, expected to
     *                          throw with a specific error code and message.
     */
    public function testOrderByInvalidColumnsException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidOrderByColumns->value);
        $this->expectExceptionMessage('Invalid ORDER BY columns.');
        $this->builder->select(
            ['column_one'],
        )
            ->from('table_one')
            ->orderBy()
            ->getQuery();
    }

    /**
     * Tests the Builder's handling of invalid ORDER BY columns.
     *
     * This test case assesses the Builder's robustness by ensuring it throws an exception
     * when provided with an invalid column name  for the ORDER BY clause. It verifies
     * that the exception thrown is a BuilderException with the correct error message and code,
     * aligning with defined standards for error handling in the EchoQuery library.
     *
     * @return void
     * @throws BuilderException If the ORDER BY clause is constructed with invalid columns, expected to
     *                          throw with a specific error code and message.
     */
    public function testOrderByInvalidColumnNameException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidOrderColumnName->value);
        $this->expectExceptionMessage('Invalid ORDER BY column name.');
        $this->builder->select(
            ['column_one'],
        )
            ->from('table_one')
            ->orderBy(
                [],
            )
            ->getQuery();
    }

    /**
     * Tests pagination functionality without specifying an offset.
     *
     * This test verifies that the Builder class can correctly append a LIMIT clause to a SELECT statement
     * when only the limit is specified, without an offset. It checks whether the constructed SQL query
     * string correctly limits the number of results returned, conforming to the expected SQL format.
     *
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not match the expected format.
     */
    public function testPaginationWithoutOffset(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->pagination(10)
            ->getQuery();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' LIMIT 10 ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests pagination functionality with both limit and offset specified.
     *
     * This test ensures that the Builder class can accurately handle SQL queries that require both
     * a LIMIT and an OFFSET clause. It verifies that the SQL query string is constructed correctly,
     * limiting the results to a specified number and skipping a defined number of rows.
     *
     * @return void
     * @throws ExpectationFailedException If the actual SQL query does not conform to the expected output.
     */
    public function testPaginationWithOffset(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->pagination(10, 20)
            ->getQuery();
        $expect = ' SELECT column_one AS co, column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' LIMIT 10 OFFSET 20';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }
}
