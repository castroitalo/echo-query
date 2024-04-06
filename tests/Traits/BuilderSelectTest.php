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
            ->__toString();
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
            ->__toString();
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
}
