<?php

declare(strict_types=1);

namespace tests\Traits;

use CastroItalo\EchoQuery\Builder;
use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\RequiresPhpunit;
use PHPUnit\Framework\TestCase;

#[RequiresPhp('^8.2')]
#[RequiresPhpunit('^10.5')]
final class BuilderJoinTest extends TestCase
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
     * Tests the basic functionality of an INNER JOIN without a subquery.
     *
     * This test verifies that the Builder class can properly construct an SQL query using an INNER JOIN
     * between two tables without the use of subqueries. The test confirms that the SQL generated matches
     * the expected format.
     *
     * @return void
     */
    public function testInnerJoinWithoutSubQuery(): void
    {
        $actual = $this->builder->select(
            ['a.column_one', 'co'],
            ['b.column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('a.column_one')
            ->equalsTo(2)
            ->innerJoin(
                ['table_two', 'b'],
                ['a.column_one', 'b.column_one'],
            )
            ->__toString();
        $expect = ' SELECT a.column_one AS co, b.column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE a.column_one = 2 ' .
            ' INNER JOIN table_two AS b ' .
            ' ON b.column_one = a.column_one ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the ability of the Builder to handle INNER JOINs with a subquery.
     *
     * This test checks if the Builder class can correctly integrate a subquery into an INNER JOIN operation.
     * It constructs a subquery, uses it as a part of the join, and verifies that the resulting SQL query
     * is correctly formatted and logically correct.
     *
     * @return void
     */
    public function testInnerJoinWithSubQuery(): void
    {
        $subQuery = (new Builder())->select(
            ['column_one'],
            ['column_two'],
        )
            ->from('table_two', 'tt')
            ->where('column_one')
            ->equalsTo(5)
            ->__toString();
        $actual = $this->builder->select(
            ['a.column_one', 'co'],
            ['b.column_two', 'ct'],
        )
            ->from('table_one', 'a')
            ->where('a.column_one')
            ->notIn([1, 2, 3])
            ->innerJoinSub(
                [$subQuery, 'b'],
                ['a.column_one', 'b.column_one'],
            )
            ->__toString();
        $expect = ' SELECT a.column_one AS co, b.column_two AS ct ' .
            ' FROM table_one AS a ' .
            ' WHERE a.column_one NOT IN (1, 2, 3) ' .
            ' INNER JOIN ( ' .
            ' SELECT column_one, column_two ' .
            ' FROM table_two AS tt ' .
            ' WHERE column_one = 5 ' .
            ' ) AS b ' .
            ' ON b.column_one = a.column_one ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the exception handling for invalid JOIN information.
     *
     * This test ensures that the Builder class throws an appropriate exception when provided
     * with incomplete or incorrect join information. The test is designed to trigger an exception
     * by passing insufficient data to the innerJoin method, verifying that the error handling
     * conforms to expected behaviors.
     *
     * @return void
     */
    public function testInvalidJoinInfoException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidJoinInfo->value);
        $this->expectExceptionMessage('Invalid INNER JOIN info.');
        $this->builder->select(
            ['a.column_one', 'co'],
            ['b.column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('a.column_one')
            ->equalsTo(2)
            ->innerJoin(
                [''],
            );
    }
}
