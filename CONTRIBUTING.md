# Contributing to EchoQuery

First off, thank you for considering contributing to EchoQuery. It's people like you that make EchoQuery such a great tool.

## Development environment

### Pre-requisites

- [Docker](https://www.docker.com/) installed.

### Installing project locally

- Build docker image: `docker build . -t echoquery:v1.0.0-dev`.
- Up project with docker compose: `docker compose up -d`.

### Running project
- Enter inside docker container with `docker container exec -it echo-query-app-1 bash`.
- And run `composer run echo_query` to run **bin/index.php** file (used for testing).
