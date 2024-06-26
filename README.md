# EchoQuery

EchoQuery offers a streamlined, intuitive interface for developers. It simplifies complex SQL scripting, enhances readability, and accelerates development, making database interactions effortless and efficient for projects of any scale.

## Basic Usage

To be able to use the EchoQuery package you need to install it with the following command:

```shell
composer require castroitalo/echoquery
```

To generate a string with your query using EchoQuery just use the methods available:

- Query example:

```sql
SELECT
    column_one AS co,
    column_two
FROM
    table_one AS to
WHERE
    column_one = 2
    AND column_two = 5;
```

- PHP code with EchoQuery example:

```php
// Importing library
use CastroItalo\EchoQuery\Builder;

// Instanciate class
$echo_query = new Builder();
$query = $echo_query->select(
    ['column_one', 'co'],
    ['column_two']
)
    ->from('table_one', 'to')
    ->where('column_one')
    ->equalsTo(2)
    ->and('column_two')
    ->equalsTo(5)
    ->getQuery();
```

## Features

### Select and From Statement

The base of any SQL query is the SELECT and FROM statement, you can easily do it with echo query:

Let's create this SQL code into PHP using EchoQuery:

```sql
SELECT column_one
FROM table_one
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['column_one']
)
    ->from('table_one')
    ->getQuery();
```

Let's make it more simple:

```sql
SELECT *
FROM table_one
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['*']
)
    ->from('table_one')
    ->getQuery();
```

In this case, every array passed in **select** method, is a column and its alias, you can pass as much columns as you want:

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['column_one', 'co'],
    ['column_two', 'ct'],
    ['column_three', 'ctr']
)
    ->from('table_one')
    ->getQuery();
```

### Where statement

You create each WHERE condition at time, if you want to take the `column_one` data only the data that is greater than 10 you just do:

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['column_one', 'co'],
    ['column_two', 'ct'],
    ['column_three', 'ctr']
)
    ->from('table_one')
    ->where('column_on')
    ->greaterThan(10)
    ->getQuery();
```

You can use `->where()` method with:

- Comparison operators:
  - `->equalsTo(mixed $value): Builder`
  - `->notEqualsTo(mixed $value, string $notEqualsToOperator = '!='): Builder`
  - `->lessThan(mixed $value): Builder`
  - `->lessThanEqualsTo(mixed $value): Builder`
  - `->greaterThan(mixed $value): Builder`
  - `->greaterThanEqualsTo(mixed $value): Builder`

- Logical operators:
  - `->and(string $columnName): Builder`
  - `->or(string $columnName): Builder`
  - `->not(string $columnName): Builder`

- Pattern matching:
  - `->like(string $pattern): Builder`
  - `->notLike(string $pattern): Builder`

- Range conditions:
  - `->between(mixed $start, mixed $end): Builder`
  - `->notBetween(mixed $start, mixed $end): Builder`

- List conditions:
  - `->in(array $list): Builder`
  - `->notIn(array $list): Builder`

- Null conditions:
  - `->isNull(): Builder`
  - `->isNotNull(): Builder`

### JOINS

To use joins you need to call the JOIN method you want, and specify the table and the JOIN columns like in:

```sql
SELECT a.column_one AS co,
    b.column_two AS ct
FROM table_one AS a
WHERE column_one > 10
    INNER JOIN table_two AS b
        ON a.column_one = b.column_one
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['a.column_one', 'co'],
    ['b.column_two', 'ct'],
)
    ->from('table_one', 'a')
    ->where('column_one')
    ->greaterThan(10)
    ->innerJoin(
        ['table_two', 'b'],
        ['a.column_one', 'b.column_one']
    )
    ->getQuery();
```

To use JOIN with sub query you just need to use the equivalent sub query JOIN method:

```sql
SELECT a.column_one AS co,
    b.column_two AS ct
FROM table_one AS a
WHERE a.column_one > 10
    INNER JOIN (
        SELECT column_one,
            column_two
        FROM table_two
    ) AS b
        ON a.column_one = b.column_one
```

```php
use CastroItalo\EchoQuery\Builder;

$sub_query = (new Builder())->select(
    ['a.column_one', 'co'],
    ['b.column_two', 'ct'],
)
    ->from('table_one', 'a')
    ->where('column_one')
    ->getQuery();
$query = (new Builder())->select(
    ['a.column_one', 'co'],
    ['b.column_two', 'ct'],
)
    ->from('table_one', 'a')
    ->where('column_one')
    ->greaterThan(10)
    ->innerJoin(
        [$sub_query, 'b'],
        ['a.column_one', 'b.column_one']
    )
    ->getQuery();
```

- INNER JOIN:
  - `->innerJoin(array ...$joinInfo): Builder`
  - `->innerJoinSub(array ...$joinInfo): Builder`

- LEFT JOIN:
  - `->leftJoin(array ...$joinInfo): Builder`
  - `->leftJoinSub(array ...$joinInfo): Builder`

- RIGHT JOIN:
  - `->rightJoin(array ...$joinInfo): Builder`
  - `->rightJoinSub(array ...$joinInfo): Builder`

- FULL JOIN:
  - `->fullJoin(array ...$joinInfo): Builder`
  - `->fullJoinSub(array ...$joinInfo): Builder`

- CROSS JOIN:
  - `->crossJoin(array ...$joinInfo): Builder`
  - `->crossJoinSub(array ...$joinInfo): Builder`

- SELF JOIN:
  - `->selfJoin(array ...$joinInfo): Builder`
  - `->selfJoinSub(array ...$joinInfo): Builder`

- NATURAL JOIN:
  - `->naturalJoin(array ...$joinInfo): Builder`
  - `->naturalJoinSub(array ...$joinInfo): Builder`

### UNIONS

To use use UNIONS just use the `->union(string $unionQuery): Builder` or `->unionAll(string $unionQuery): Builder` with the subsequent query as the parameter:

```sql
SELECT column_one AS co,
    column_two AS ct,
    column_three AS ctr
FROM table_one AS to
WHERE column_one > 10
UNION
SELECT column_four AS cfr,
    column_five AS cf,
    column_six AS cs
FROM table_two AS tt
WHERE column_five NOT IN (1, 3, 4, 6);
```

```php
use CastroItalo\EchoQuery\Builder;

$union_query = (new Builder())->select(
    ['column_four', 'cfr'],
    ['column_five', 'cf'],
    ['column_six', 'cs']
)
    ->from('table_two', 'tt')
    ->where('column_five')
    ->notIn([1, 3, 4, 6])
    ->getQuery();
$query = (new Builder())->select(
    ['column_one', 'co'],
    ['column_two', 'ct'],
    ['column_three', 'ctr']
)
    ->from('table_one', 'to')
    ->where('column_one')
    ->greaterThan(10)
    ->union($union_query)
    ->getQuery();
```

### GROUP BY

To use GROUP BY on aggregate functions simply use the `->groupBy(string ...$columns): Builder` method, passing one or more columns to group:

```sql
SELECT COUNT(column_one) AS co
    SUM(column_two) AS ct
FROM table_one AS to
GROUP BY column_one, column_two
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['COUNT(column_one)', 'co'],
    ['SUM(column_two)', 'ct']
)
    ->from('table_one', 'to')
    ->groupBy('column_one', 'column_two')
    ->getQuery();
```

### ORDER BY

To use ORDER BY on columns simply use the `->orderBy(array ...$columns): Builder` method, passing one or more columns with the respective ordering function.

```sql
SELECT column_one AS co,
    column_two AS ct
FROM table_one AS to
ORDER BY column_one,
    column_two DESC
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['column_one', 'co'],
    ['column_two', 'ct']
)
    ->from('table_one', 'to')
    ->orderBy(
        ['column_one'],
        ['column_two', 'desc']
    )
    ->getQuery();
```

### HAVING

For using HAVING just use the `->having(string $having): Builder` with one of the comparison operator:

```sql
SELECT COUNT(column_one) AS co,
    column_two AS ct,
    column_three AS ctr
FROM table_one AS to
WHERE column_one > 10
HAVING COUNT(column_one) > 10
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['COUNT(column_one)', 'co'],
    ['column_two', 'ct'],
    ['column_three', 'ctr']
)
    ->from('table_one', 'to')
    ->where('column_one')
    ->greaterThan(10)
    ->having('COUNT(column_one)')
    ->greaterThan(10)
    ->getQuery();
```

### Pagination

To create a pagination just use the `->pagination(int $limit, ?int $offset = null): Builder` method, passing the limit and an optional offset for it:

```sql
SELECT column_one AS co,
    column_two AS ct
FROM table_one AS to
LIMIT 10
```

```php
use CastroItalo\EchoQuery\Builder;

$query = (new Builder())->select(
    ['COUNT(column_one)', 'co'],
    ['column_two', 'ct'],
)
    ->from('table_one', 'to')
    ->where('column_one')
    ->pagination(10)
    ->getQuery();
```

## Contributing

To contribute to the project make sure you have read [CONTRIBUTING](https://github.com/castroitalo/echo-query/blob/main/CONTRIBUTING.md) section.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Credits

- [Italo Castro](https://github.com/castroitalo)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
