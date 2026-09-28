# Doctrine Skeleton

Instructions for AI coding agents working in this repository.

## Overview

A Nette application skeleton that shows Doctrine ORM with two entity managers: `default` on PostgreSQL and `second`
on MariaDB, both mapping the same `User` entity. It has one page that lists, creates and deletes users, migrations
per database and a console. It is a starting point that users copy with `composer create-project`, not a library.
Every change must keep a fresh copy working.

- **PHP**: 8.4 and later (`>=8.4` in `composer.json`), CI runs 8.4 only
- **Package**: `contributte/doctrine-skeleton`, `"type": "project"`
- **Namespaces**: `App\` in `app/`, `Tests\` in `tests/`, `DB\Migrations\` in `db/postgres` and `db/mariadb`
  (loaded by Doctrine Migrations, not by Composer)

## Documentation

- `PRD.md` says what the skeleton demonstrates and what it leaves out. Read it before adding a feature or a package.
- `TECH.md` explains the boot through `contributte/nella`, the two connections, migrations and the known limits.
  Read it before changing `app/Bootstrap.php`, `config/` or `docker-compose.yml`.
- `DESIGN.md` holds the rules for the layout and the home page template in `app/UI/`.
- Organization rules are in [contributte/contributte specs](https://github.com/contributte/contributte/tree/master/specs).

## Stack

- **Framework**: Nette 3.2, Latte 3.1, Tracy 2.11, booted by `contributte/nella` 0.3
- **Database**: Doctrine ORM 3.6 and DBAL 4.4 via `nettrine/orm`, `nettrine/dbal`, `nettrine/migrations`,
  `nettrine/extra`; PostgreSQL 15 and MariaDB 10.10 in Docker Compose
- **QA**: `contributte/qa` (CodeSniffer), PHPStan level 9 with `phpstan-doctrine`, Nette Tester with
  `contributte/tester`

```
app/
├── Bootstrap.php     # Bootloader + NellaPreset; boot() and run()
├── Domain/Database/  # User entity and UserRepository, mapped by both managers
└── UI/               # @Templates/@layout.latte, BasePresenter, Home/
config/               # config.neon, doctrine.neon, local.neon.example
db/                   # postgres/ and mariadb/ migrations
www/                  # the only public directory: index.php, .htaccess
```

## Commands

```bash
# Install dependencies, create var/ folders, copy config/local.neon.example to config/local.neon
make project
make init

# PostgreSQL (5432) and MariaDB (3306) in Docker, in the foreground; there is no PHP container
make docker-up

# Built-in server on http://localhost:8080 with NETTE_DEBUG=1
make dev

# Migrate PostgreSQL (the default), clear the container cache, then migrate MariaDB
bin/console migrations:migrate
make clean
NETTE__MIGRATION__DB=mariadb NETTE__MIGRATION__MANAGER=second bin/console migrations:migrate

# Code style + PHPStan, fix code style, run tests, or one test file
make qa
make csf
make tests
vendor/bin/tester -s -p php --colors 1 -C tests/Cases/E2E/Database/MappingTest.php
```

CI runs `make init tests`, `make init phpstan`, `make init coverage` and CodeSniffer on PHP 8.4, without a database.

## Conventions

- A presenter lives in `app/UI/{Name}/{Name}Presenter.php` with templates in `app/UI/{Name}/Templates/`; the
  mapping `App\UI\*\*Presenter` and the route `<presenter>/<action>` come from `contributte/nella`, not from `config/`.
- Schema changes go through `migrations:diff` for each database into `db/postgres` or `db/mariadb`. Never edit a
  released migration.
- `composer.lock` is committed; update it together with `composer.json`.

## Traps

- **`config/local.neon.example` is fully commented out.** After `make init` the app uses `config/config.neon`:
  host `localhost`, database `contributte`. Docker creates `demopostgres` and `demomariadb`, so uncomment the example
  or the connection fails. `local.neon` is optional; `NellaPreset` loads it only when it exists.
- **The home page queries both databases.** `HomePresenter::actionDefault()` calls `findAll()` on `default` and
  `second`, so both must be running and migrated. Create and delete act on PostgreSQL only.
- **The migration target is a parameter baked into the container.** `migration.db` and `migration.manager` default
  to `postgres` and `default`; `NETTE__*` variables override them in an `onCompile` hook, which is not part of the
  container cache key. Run `make clean` between the PostgreSQL and MariaDB runs, or the cached container wins.
- **`NETTE_ENV=dev` in `make dev` does nothing.** Nella reads only `NETTE_DEBUG` and `NETTE__*` variables.
- **`EntrypointTest` writes `config/local.neon`** from the example when it is missing, so `make tests` leaves the file
  behind.
- **`make cs` and `make csf` call the `contributte/qa` wrappers `vendor/bin/codesniffer` and `codefixer`,** not
  `phpcs` directly, and `make build` only prints `OK`. `make tests` runs the whole `tests/` folder.
- Features for users and screenshots are described in `README.md`; product scope is in `PRD.md`, not here.

## Ground Rules

- **Only `www/` is public.** `app/`, `config/`, `db/` and `var/` must never be served.
- Secrets live in `config/local.neon` (git-ignored) or `NETTE__*` environment variables and are never committed.
- `make dev` binds `0.0.0.0:8080` with Tracy on. The Docker credentials `contributte` / `contributte` and the
  delete-all link on the home page are for local use only.
