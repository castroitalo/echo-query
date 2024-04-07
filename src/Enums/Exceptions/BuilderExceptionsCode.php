<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Enums\Exceptions;

/**
 * Enum BuilderExceptionsCode
 *
 * Represents specific exception codes for query builder operations within the EchoQuery library.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery\enums\exceptions
 */
enum BuilderExceptionsCode: int
{
    /**
     * Invalid or unacceptable column names.
     *
     * This code is used when the provided column names are either syntactically
     * incorrect, empty, or otherwise invalid according to the query builder's validation logic.
     */
    case InvalidColumnName = 1000;

    /**
     * Attempt to modify or append to a SELECT statement without initializing one.
     *
     * This code is triggered when there is an attempt to add conditions or modifications
     * to a SELECT statement that has not been previously started or defined, indicating
     * a logical flow error in the query construction process.
     */
    case NoPreviousSelectStatement = 1001;

    /**
     * Specified table name for the query is empty or undefined.
     *
     * This exception code is used when a query operation is initiated without a valid
     * table name, which is essential for the execution of any database operation.
     */
    case InvalidTableName = 1002;

    /**
     * Invalid or missing alias.
     *
     * Triggered when alias name are either syntactically incorrect, empty or otherwise
     * invalid according to the query builder's validation logic
     */
    case InvalidAlias = 1003;

    /**
     * Attempt to add conditions to a WHERE clause without initializing it.
     *
     * Used when there's an attempt to specify conditions for a WHERE clause before
     * any WHERE statement has been defined in the query, indicating a misordered
     * or logically incorrect query construction.
     */
    case NoPreviousWhereStatement = 1004;

    /**
     * Indicates the inclusion of multiple FROM statements in a single query.
     *
     * This exception code is used when there is an attempt to add more than one FROM statement
     * to a single SQL query, which is not allowed. The presence of multiple FROM clauses in a query
     * can lead to ambiguity and is generally indicative of a logical error in query construction.
     * Proper query design should consolidate data sourcing to a single FROM clause, possibly
     * supplemented by JOINs or subqueries as needed.
     */
    case MultipleFromStatement = 1005;

    /**
     * Indicates an attempt to construct a WHERE clause without a preceding FROM statement.
     *
     * This exception code is used when a WHERE clause is being added to a query without a
     * prior FROM statement. SQL syntax requires that the FROM clause precedes the WHERE clause,
     * as the WHERE conditions apply to the dataset defined by the FROM clause. This error typically
     * signifies a logical error in the order of query construction, highlighting the absence of
     * the necessary FROM statement to define the data source for the query's filtering conditions.
     */
    case NoPreviousFromStatement = 1006;

    /**
     * Indicates the use of an unsupported or invalid comparison operator in a query condition.
     *
     * This exception code is triggered when a query attempts to use a comparison operator that is not
     * recognized or supported by the SQL standard or the query builder's logic. It underscores the importance
     * of adhering to standard SQL comparison operators and the query builder's specifications for ensuring
     * query validity and preventing errors during query execution.
     *
     * Supported operators typically include '=', '!=', '<>', '<', '>', '<=', and '>=', among others.
     * Using operators outside of these conventions or misusing them in the context of a query can lead
     * to this error, promoting the use of correct syntax and logical operator application in query conditions.
     */
    case InvalidComparisonOperator = 1007;
}
