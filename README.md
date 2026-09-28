![](https://heatbadger.now.sh/github/readme/contributte/doctrine-skeleton/)

<p align=center>
  <a href="https://github.com/contributte/doctrine-skeleton/actions"><img src="https://badgen.net/github/checks/contributte/doctrine-skeleton/master?cache=300"></a>
  <a href="https://codecov.io/gh/contributte/doctrine-skeleton"><img src="https://badgen.net/codecov/c/github/contributte/doctrine-skeleton"></a>
  <a href="https://packagist.org/packages/contributte/doctrine-skeleton"><img src="https://badgen.net/packagist/dm/contributte/doctrine-skeleton"></a>
  <a href="https://packagist.org/packages/contributte/doctrine-skeleton"><img src="https://badgen.net/packagist/v/contributte/doctrine-skeleton"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/doctrine-skeleton"><img src="https://badgen.net/packagist/php/contributte/doctrine-skeleton"></a>
  <a href="https://github.com/contributte/doctrine-skeleton"><img src="https://badgen.net/github/license/contributte/doctrine-skeleton"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

<p align=center>
  <img src="https://api.microlink.io?url=https%3A%2F%2Fexamples.contributte.org%2Fdoctrine-skeleton%2F&overlay.browser=light&screenshot=true&meta=false&embed=screenshot.url"/>
</p>

Doctrine Skeleton is a Nette Framework starter project with Doctrine ORM on two databases, PostgreSQL and MariaDB.

-----

## Goal

Doctrine Skeleton shows how [Doctrine](https://www.doctrine-project.org/) ORM, DBAL and migrations work in a
[Nette](https://nette.org/) application with two connections and two entity managers. One `User` entity is mapped
in both, and the home page lists users from both databases, so you see the whole setup working before you change it.

It is built on:

- PHP 8.4 or later and `nette/*` packages, booted by `contributte/nella`
- Doctrine ORM, DBAL and migrations via `nettrine/orm`, `nettrine/dbal`, `nettrine/migrations` and `nettrine/extra`
- Symfony Console via `contributte/console`
- PostgreSQL 15 and MariaDB 10.10 in Docker Compose
- Code style via CodeSniffer and `contributte/qa`, static analysis via PHPStan and `contributte/phpstan`
- Tests via Nette Tester and `contributte/tester`

## Demo

https://examples.contributte.org/doctrine-skeleton/

## Installation

Create a new project with [Composer](https://getcomposer.org):

```bash
composer create-project -s dev contributte/doctrine-skeleton acme
```

Or clone it with [Git](https://git-scm.com/) and install the dependencies:

```bash
git clone git@github.com:contributte/doctrine-skeleton.git acme
cd acme
composer install
```

Requires PHP 8.4 or later, Composer, and Docker for the two databases.

## Startup

Create your local config, `config/local.neon`, from the example:

```bash
make init
```

All lines in the example are commented out. Uncomment them, so the connections match the databases in
`docker-compose.yml`:

```neon
parameters:
	postgres:
		driver: pdo_pgsql
		host: 0.0.0.0
		port: 5432
		user: contributte
		password: contributte
		dbname: demopostgres
	mariadb:
		driver: mysqli
		host: 0.0.0.0
		port: 3306
		user: contributte
		password: contributte
		dbname: demomariadb
```

Start PostgreSQL on port 5432 and MariaDB on port 3306. The containers run in the foreground, so keep the terminal
open:

```bash
make docker-up
```

In a second terminal, run the migrations for PostgreSQL, clear the container cache, then run them for MariaDB:

```bash
bin/console migrations:migrate
make clean
NETTE__MIGRATION__DB=mariadb NETTE__MIGRATION__MANAGER=second bin/console migrations:migrate
```

> [!NOTE]
> The `NETTE__MIGRATION__*` variables are compiled into the cached container in `var/tmp`. Run `make clean` whenever
> you switch them.

To generate a new migration, run `migrations:diff` with the same variables.

Start the built-in server with Tracy enabled and open http://localhost:8080:

```bash
make dev
```

The known limits of the skeleton are listed in [TECH.md](TECH.md#known-limits), the scope in [PRD.md](PRD.md).

## Screenshots

![](.docs/screenshot.png)

## Development

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider [supporting](https://contributte.org/partners.html) the **contributte** development team.
Thank you for using this package.
