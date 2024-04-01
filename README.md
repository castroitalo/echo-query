# EchoQuery

> **Status: In Development**
> This library is currently in the development stage and may undergo significant changes. Feedback and contributions are welcome!

EchoQuery revolutionizes PHP-based SQL query generation, offering a streamlined, intuitive interface for developers. It simplifies complex SQL scripting, enhances readability, and accelerates development, making database interactions effortless and efficient for projects of any scale.

## Usage

To use EchoQuery you have to import into your code:

```php
// Importing library
use CastroItalo\EchoQuery\Builder;
```

And now is just to generate your queries string:

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
    ->__toString();
```

## Contributing

To contribute to the project make sure you have read [CONTRIBUTING](https://github.com/castroitalo/echo-query/blob/main/CONTRIBUTING.md) section.
