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
}
