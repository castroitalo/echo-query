<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Traits;

use CastroItalo\EchoQuery\Enums\Exceptions\BuilderExceptionsCode;
use CastroItalo\EchoQuery\Exceptions\BuilderException;

/**
 * Enhances query builders with WHERE clause construction capabilities.
 *
 * The BuilderWhere trait provides functionality to construct the WHERE part of an SQL query,
 * ensuring that the necessary conditions for a valid WHERE clause are met. This includes
 * verifying the presence of a FROM statement in the query and validating column names used in
 * the WHERE clause.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery\Traits
 */
trait BuilderWhere
{
    /**
     * Holds the current WHERE clause being constructed.
     *
     * @var string|null $where The partial or complete WHERE clause as it's built.
     */
    private ?string $where = null;

    /**
     * The error code for when a WHERE statement is attempted without a preceding FROM statement.
     *
     * @var int $noPreviousFromStatementExceptionCode Error code indicating the absence of a required FROM clause.
     */
    private int $noPreviousFromStatementExceptionCode = BuilderExceptionsCode::NoPreviousFromStatement->value;

    /**
     * The error code for when an invalid column name is provided in the WHERE clause.
     *
     * @var int $invalidColumnNameExceptionCode Error code used when the column name for a WHERE clause is invalid or empty.
     */
    private int $invalidColumnNameExceptionCode = BuilderExceptionsCode::InvalidColumnName->value;

    /**
     * Constructs a WHERE clause for an SQL query based on the provided column name.
     *
     * This method appends a WHERE clause to a given SQL query, using the specified column name for the condition.
     * Before appending, it validates the presence of a SELECT statement to ensure the logical structure of the SQL
     * query is maintained, and checks the validity of the column name to prevent SQL errors. If either condition
     * is not met, a BuilderException is thrown with an appropriate error code.
     *
     * @param string $query The initial or existing SQL query to which the WHERE clause will be appended.
     * @param string $columnName The column name to be used in the WHERE clause condition. The column name must
     *                           be valid and non-empty to ensure the generated SQL is syntactically correct.
     * @return string The modified query string including the newly constructed WHERE clause.
     * @throws BuilderException If there is no previous SELECT statement in the query or if the column name is invalid.
     */
    private function baseWhere(string $query, string $columnName): string
    {
        // Exitance conditions
        if (! str_contains($query, 'SELECT')) {
            throw new BuilderException(
                'No previous WHERE statement.',
                $this->noPreviousFromStatementExceptionCode,
            );
        }

        if (empty($columnName)) {
            throw new BuilderException(
                'Invalid WHERE statement column name.',
                $this->invalidColumnNameExceptionCode,
            );
        }

        $this->where = ' WHERE ' . $columnName;
        $query .= $this->where;

        return $query;
    }
}
