# EchoQuery

[![Software License][ico-license]](LICENSE.md)

> **Status: In Development**
> This library is currently in the development stage and may undergo significant changes. Feedback and contributions are welcome!

EchoQuery offers a streamlined, intuitive interface for developers. It simplifies complex SQL scripting, enhances readability, and accelerates development, making database interactions effortless and efficient for projects of any scale.

## Usage

To use EchoQuery you have to import into your code:

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

## Changelog (TODO)

## Testing

Each EchoQuery functionality is separated in traits, for the SQL SELECT statement is used the **BuilderSelect.php** trait, so you can run tests for each individual trait like:

```shell
composer run builder_select_tests
```

To run all tests just type:

```shell
composer run tests
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Credits

- [Italo Castro](https://github.com/castroitalo)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
