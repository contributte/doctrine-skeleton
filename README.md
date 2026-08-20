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
make setup
make init
```

`composer create-project` already installs the dependencies. `make setup` creates writable `var/tmp` and `var/log` directories without running Composer a second time, and `make init` copies the local configuration template.

## Database configuration

The application configures two DBAL connections and two entity managers: `default` uses PostgreSQL and `second` uses MariaDB. For native PHP clients connecting to the included Docker Compose databases, uncomment and use these local overrides in `config/local.neon`:

```neon
parameters:
	postgres:
		driver: pdo_pgsql
		host: localhost
		port: 5432
		user: contributte
		password: contributte
		dbname: demopostgres
	mariadb:
		driver: mysqli
		host: localhost
		port: 3306
		user: contributte
		password: contributte
		dbname: demomariadb
```

These database names and credentials match `docker-compose.yml`. The environment variables in the migration commands below select the matching migration directory and entity manager without changing the local file.

## Docker Compose

The tracked `docker-compose.yml` starts only the database services:

```bash
make docker-up
```

It exposes PostgreSQL on `localhost:5432` (`demopostgres`) and MariaDB on `localhost:3306` (`demomariadb`). Both use the `contributte` username and password.

## Migrations

Run migrations for PostgreSQL:

```bash
NETTE__MIGRATION__DB=postgres NETTE__MIGRATION__MANAGER=default bin/console migrations:migrate --no-interaction
```

Run migrations for MariaDB:

```bash
NETTE__MIGRATION__DB=mariadb NETTE__MIGRATION__MANAGER=second bin/console migrations:migrate --no-interaction
```

Generate a migration by replacing `migrations:migrate` with `migrations:diff` and keeping the matching database and manager variables.

## Development and quality checks

Start the application at <http://localhost:8080>:

```bash
make dev
```

Open the page and click **Create random user**. A successful PostgreSQL write displays **Saved** and adds a new row to the users table; the page reads and combines rows through both entity managers.

Run static analysis and coding-standard checks:

```bash
make qa
```

Run tests:

```bash
make tests
```
