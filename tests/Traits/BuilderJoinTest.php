<?php

declare(strict_types=1);

namespace tests\Traits;

use CastroItalo\EchoQuery\Builder;
use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;
use PHPUnit\Framework\Attributes\DataProvider;
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
     * Data provider for join operations without subqueries.
     *
     * Provides various join methods along with their corresponding SQL join type keywords,
     * used in testing the basic join functionality without involving subqueries.
     *
     * @return array An array of join types and their corresponding method names.
     */
    public static function joinWithoutSubQueryTestDataProvider(): array
    {
        return [
            'inner_join' => [
                'innerJoin', 'INNER',
            ],
            'left_join' => [
                'leftJoin', 'LEFT',
            ],
            'right_join' => [
                'rightJoin', 'RIGHT',
            ],
            'cross_join' => [
                'crossJoin', 'CROSS',
            ],
            'self_join' => [
                'selfJoin', 'SELF',
            ],
            'natural_join' => [
                'naturalJoin', 'NATURAL',
            ],
        ];
    }

    /**
     * Tests various JOIN operations without using subqueries.
     *
     * This test utilizes a data provider to verify the SQL syntax correctness for different types
     * of JOIN operations (like INNER, LEFT, etc.) as implemented in the Builder class.
     *
     * @param string $joinMethod The method name for the join operation.
     * @param string $joinType The SQL join type keyword.
     * @return void
     */
    #[DataProvider('joinWithoutSubQueryTestDataProvider')]
    public function testJoinWithoutSubQuery(string $joinMethod, string $joinType): void
    {
        $actual = $this->builder->select(
            ['a.column_one', 'co'],
            ['b.column_two', 'ct'],
        )
            ->from('table_one', 'to')
            ->where('a.column_one')
            ->equalsTo(2)
            ->$joinMethod(
                ['table_two', 'b'],
                ['a.column_one', 'b.column_one'],
            )
            ->getQuery();
        $expect = ' SELECT a.column_one AS co, b.column_two AS ct ' .
            ' FROM table_one AS to ' .
            ' WHERE a.column_one = 2 ' .
            ' ' . $joinType . ' JOIN table_two AS b ' .
            ' ON b.column_one = a.column_one ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Data provider for join operations with subqueries.
     *
     * Supplies test cases with various join methods designed to handle subqueries, paired with
     * their corresponding SQL join type keywords.
     *
     * @return array An array of join types and their corresponding method names for subqueries.
     */
    public static function joinWithSubQueryTestDataProvider(): array
    {
        return [
            'inner_join' => [
                'innerJoinSub', 'INNER',
            ],
            'left_join' => [
                'leftJoinSub', 'LEFT',
            ],
            'right_join' => [
                'rightJoinSub', 'RIGHT',
            ],
            'cross_join' => [
                'crossJoinSub', 'CROSS',
            ],
            'self_join' => [
                'selfJoinSub', 'SELF',
            ],
            'natural_join' => [
                'naturalJoinSub', 'NATURAL',
            ],
        ];
    }

    /**
     * Tests various JOIN operations involving subqueries.
     *
     * Each test case checks the correct syntax and functionality of JOIN operations when
     * a subquery is used as part of the JOIN condition. This method tests the ability of
     * the Builder to integrate complex SQL queries.
     *
     * @param string $joinMethod The method name for the join operation including subqueries.
     * @param string $joinType The SQL join type keyword.
     * @return void
     */
    #[DataProvider('joinWithSubQueryTestDataProvider')]
    public function testJoinWithSubQuery(string $joinMethod, string $joinType): void
    {
        $subQuery = (new Builder())->select(
            ['column_one'],
            ['column_two'],
        )
            ->from('table_two', 'tt')
            ->where('column_one')
            ->equalsTo(5)
            ->getQuery();
        $actual = $this->builder->select(
            ['a.column_one', 'co'],
            ['b.column_two', 'ct'],
        )
            ->from('table_one', 'a')
            ->where('a.column_one')
            ->notIn([1, 2, 3])
            ->$joinMethod(
                [$subQuery, 'b'],
                ['a.column_one', 'b.column_one'],
            )
            ->getQuery();
        $expect = ' SELECT a.column_one AS co, b.column_two AS ct ' .
            ' FROM table_one AS a ' .
            ' WHERE a.column_one NOT IN (1, 2, 3) ' .
            ' ' . $joinType . ' JOIN ( ' .
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

    /**
     * Tests the UNION SQL operation without subqueries.
     *
     * This method checks the correctness of the SQL UNION operation formulated by the Builder.
     * It ensures that two separate SELECT queries can be unified into a single result set, excluding duplicate records.
     *
     * @return void
     */
    public function testUnion(): void
    {
        $union_query = (new Builder())->select(
            ['column_four', 'cfr'],
            ['column_five', 'cf'],
            ['column_six', 'cs'],
        )
            ->from('table_two', 'tt')
            ->where('column_five')
            ->notIn([1, 3, 4, 6])
            ->getQuery();
        $actual = (new Builder())->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
            ['column_three', 'ctr'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->greaterThan(10)
            ->union($union_query)
            ->getQuery();
        $expect = ' SELECT column_one AS co, column_two AS ct, column_three AS ctr ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one > 10 ' .
            ' UNION ' .
            ' SELECT column_four AS cfr, column_five AS cf, column_six AS cs ' .
            ' FROM table_two AS tt ' .
            ' WHERE column_five NOT IN (1, 3, 4, 6) ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the UNION ALL SQL operation.
     *
     * This test confirms the functionality of the UNION ALL operation, which combines the results of two SELECT
     * statements including all duplicates. It verifies that the Builder accurately constructs the SQL for a UNION ALL
     * operation and that the resulting query string is as expected.
     *
     * @return void
     */
    public function testUnionAll(): void
    {
        $union_query = (new Builder())->select(
            ['column_four', 'cfr'],
            ['column_five', 'cf'],
            ['column_six', 'cs'],
        )
            ->from('table_two', 'tt')
            ->where('column_five')
            ->notIn([1, 3, 4, 6])
            ->getQuery();
        $actual = $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
            ['column_three', 'ctr'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->greaterThan(10)
            ->unionAll($union_query)
            ->getQuery();
        $expect = ' SELECT column_one AS co, column_two AS ct, column_three AS ctr ' .
            ' FROM table_one AS to ' .
            ' WHERE column_one > 10 ' .
            ' UNION ALL ' .
            ' SELECT column_four AS cfr, column_five AS cf, column_six AS cs ' .
            ' FROM table_two AS tt ' .
            ' WHERE column_five NOT IN (1, 3, 4, 6) ';

        $this->assertEquals(
            str_replace(' ', '', $expect),
            str_replace(' ', '', $actual),
        );
    }

    /**
     * Tests the handling of invalid UNION queries.
     *
     * This test ensures that the Builder class appropriately throws exceptions when an invalid or empty UNION query
     * is attempted. It checks for the correct exception type, code, and message, thereby validating the robustness of
     * error handling in SQL query construction.
     *
     * @return void
     */
    public function testUnionInvalidUnionQueryException(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionCode(BuilderExceptionsCode::InvalidUnionQuery->value);
        $this->expectExceptionMessage('Invalid UNION query.');
        $this->builder->select(
            ['column_one', 'co'],
            ['column_two', 'ct'],
            ['column_three', 'ctr'],
        )
            ->from('table_one', 'to')
            ->where('column_one')
            ->greaterThan(10)
            ->union('')
            ->getQuery();
    }
}
