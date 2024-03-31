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
}
