# Doctrine Skeleton

Example Nette application with Nettrine DBAL, ORM, fixtures, and migrations configured for PostgreSQL and MariaDB.

## Requirements

- PHP 8.4+
- [Composer](https://getcomposer.org/)
- PostgreSQL 15 and MariaDB 10.10, either installed locally or started with Docker Compose

## Create a project

```bash
composer create-project -s dev contributte/doctrine-skeleton acme
cd acme
make project
```

`make project` installs dependencies and creates writable `var/tmp` and `var/log` directories.

## Database configuration

Copy the local configuration template:

```bash
make init
```

For the included Docker Compose services, uncomment and use the following values in `config/local.neon`:

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

The base configuration selects the PostgreSQL `default` connection for migrations. Override `migration.db` and `migration.manager` locally when working with the MariaDB connection.

## Docker Compose

The tracked `docker-compose.yml` starts only the database services:

```bash
make docker-up
```

It exposes PostgreSQL on `localhost:5432` (`demopostgres`) and MariaDB on `localhost:3306` (`demomariadb`). Both use the `contributte` username and password.

## Migrations

Run migrations for PostgreSQL:

```bash
NETTE__MIGRATION__DB=postgres NETTE__MIGRATION__MANAGER=default bin/console migrations:migrate
```

Run migrations for MariaDB:

```bash
NETTE__MIGRATION__DB=mariadb NETTE__MIGRATION__MANAGER=second bin/console migrations:migrate
```

Generate a migration by replacing `migrations:migrate` with `migrations:diff` and keeping the matching database and manager variables.

## Development and quality checks

Start the application at <http://localhost:8080>:

```bash
make dev
```

Run static analysis and coding-standard checks:

```bash
make qa
```

Run tests:

```bash
make tests
```
