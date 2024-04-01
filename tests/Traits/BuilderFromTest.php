<?php

declare(strict_types=1);

namespace tests\Traits;

use CastroItalo\EchoQuery\Builder;
use CastroItalo\EchoQuery\Exceptions\BuilderException;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\RequiresPhpunit;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the FROM statement functionality in the Builder class.
 *
 * This class contains tests to verify the correct construction of the FROM part of an SQL query
 * by the Builder class of the EchoQuery library. It includes tests for basic FROM statements,
 * usage of table aliases, and FROM statements derived from subqueries.
 *
 * @requires PHP 8.2
 * @requires PHPUnit 10.5
 */
#[RequiresPhp('8.2')]
#[RequiresPhpunit('^10.5')]
final class BuilderFromTest extends TestCase
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
     * Tests the construction of a basic FROM statement without using table aliases.
     *
     * This test verifies that the Builder class can generate a correct FROM statement as part of a
     * larger SQL query, without the use of table aliases. It constructs a SELECT query with a FROM
     * clause, comparing the resulting SQL string to the expected format, ignoring differences in whitespace.
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testFromStatementWithoutAlias(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one')
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct FROM table_one';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the construction of a FROM statement with a table alias.
     *
     * Ensures that the Builder class properly handles table aliases in the FROM statement of an SQL query.
     * The test constructs a SELECT query with a FROM clause that includes a table alias, and then compares
     * the output to the expected SQL string, with whitespace variations disregarded.
     * @return void
     * @throws BuilderException
     * @throws ExpectationFailedException
     */
    public function testFromStatementWithAlias(): void
    {
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->__toString();
        $expect = 'SELECT column_one AS co, column_two AS ct FROM table_one AS to';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }
}
