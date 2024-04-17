<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Traits;

use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;

/**
 * Provides the capability to append JOIN clauses to SQL queries.
 *
 * This trait is utilized within the query builder system to facilitate the addition of various types of JOIN operations,
 * including handling subqueries as join tables. It defines methods that generate and append JOIN clauses to an existing
 * SQL query string based on provided join information and configuration.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 */
trait BuilderJoin
{
    /**
     * Accumulates JOIN clauses as they are constructed.
     *
     * This property holds the string representation of the latest JOIN clause generated by the methods within this trait.
     * It is used to append subsequent JOIN operations to the primary query string in the builder.
     *
     * @var string $join The current JOIN clause in construction.
     */
    private string $join = '';

    /**
     * The exception code for invalid join information.
     *
     * This code is used to throw a specific exception when the join information provided does not meet the expected format
     * or logical structure necessary for constructing a valid JOIN clause.
     *
     * @var int $invalidJoinInfoExceptionCode The error code corresponding to invalid join information.
     */
    private int $invalidJoinInfoExceptionCode = BuilderExceptionsCode::InvalidJoinInfo->value;

    /**
     * Constructs a JOIN clause and appends it to the provided SQL query string.
     *
     * This method takes essential parameters for constructing a JOIN clause, including the type of JOIN (e.g., INNER, LEFT),
     * information about the joining tables, and whether the join involves a subquery. It validates the structure of the join
     * information and constructs the JOIN clause accordingly. The resulting JOIN clause is then appended to the existing
     * SQL query string.
     *
     * @param string $query The existing SQL query to which the JOIN clause will be appended.
     * @param string $joinType The type of JOIN operation (e.g., 'INNER', 'LEFT').
     * @param array $joinInfo An array containing join parameters structured as [table info, column info].
     * @param bool $subQuery Specifies whether the join involves a subquery.
     * @return string The updated SQL query string with the new JOIN clause appended.
     * @throws BuilderException If the provided join information is invalid.
     */
    private function baseJoin(string $query, string $joinType, array $joinInfo, bool $subQuery): string
    {
        // Validate JOIN info
        if (sizeof($joinInfo) !== 2 || sizeof($joinInfo[0]) !== 2 || sizeof($joinInfo[1]) !== 2) {
            throw new BuilderException(
                'Invalid ' . $joinType . ' JOIN info.',
                $this->invalidJoinInfoExceptionCode,
            );
        }

        [$joinTable, $joinTableAlias] = $joinInfo[0];
        [$joinLeftTableColumnName, $joinRightTableColumnName] = $joinInfo[1];

        // Handling subqueries
        if ($subQuery) {
            $this->join = ' ' . $joinType . ' JOIN (' . $joinTable . ') AS ' . $joinTableAlias .
                ' ON ' . $joinRightTableColumnName . ' = ' . $joinLeftTableColumnName . ' ';
        } else {
            $this->join = ' ' . $joinType . ' JOIN ' . $joinTable . ' AS ' . $joinTableAlias .
                ' ON ' . $joinRightTableColumnName . ' = ' . $joinLeftTableColumnName . ' ';
        }

        $query .= $this->join;

        return $query;
    }
}
