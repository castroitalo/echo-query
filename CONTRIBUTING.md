# Contributing to EchoQuery

First off, thank you for considering contributing to EchoQuery. It's people like you that make EchoQuery such a great tool.
Before contributing make sure you have read [CODE_OF_CONDUCT](https://github.com/castroitalo/echo-query/blob/main/CODE_OF_CONDUCT.md)

## Development environment

### Pre-requisites

- [Docker](https://www.docker.com/) installed.

### Installing project locally

- Build docker image: `docker build . -t echoquery:v1.1.0`.
- Up project with docker compose: `docker compose up -d`.

### Running project
- Enter inside docker container with `docker container exec -it echo-query-app-1 bash`.
- And run `composer run echo_query` to run **bin/index.php** file (used for testing).

## Testing

Each EchoQuery functionality is separated in traits, for the SQL SELECT statement is used the **BuilderSelect.php** trait, so you can run tests for each individual trait like:

```shell
composer run builder_select_tests
```

To run all tests just type:

```shell
composer run tests
```

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Important

At the end of every contribution, run `composer run fix` to run **php-cs-fixer** on your code.
