# Doctrine Skeleton Tech

A Nette Framework application booted by `contributte/nella`, with Doctrine ORM on two connections, PostgreSQL and
MariaDB, served by the PHP built-in server.

## Architecture

```
browser -> php -S 0.0.0.0:8080 -t www -> www/index.php -> App\Bootstrap::run()
                                        -> Nette Application -> Nella RouterFactory -> HomePresenter -> Latte
bin/console -> App\Bootstrap::boot() -> Symfony Console -> Doctrine / migrations command
HomePresenter -> ManagerProvider -> default manager -> PostgreSQL (localhost:5432)
                                 -> second manager  -> MariaDB (localhost:3306)
```

- One DI container for web and CLI; `contributte/console` registers commands only in console mode.
- The two entity managers share one mapping of `app/Domain/Database` but have separate connections and migrations.
- Docker Compose runs only the databases. PHP runs on the host.

## Stack

- PHP `>=8.4`; Nette `application` 3.2, `di` 3.2, `bootstrap` 3.2, Latte 3.1, Tracy 2.11 (versions from
  `composer.lock`)
- `contributte/nella` `^0.3` for boot, routing, error presenters and template lookup; `contributte/bootstrap` for
  `ExtraConfigurator`
- Doctrine ORM 3.6, DBAL 4.4 and Migrations 3.9 via `nettrine/orm` `^0.10`, `nettrine/dbal` `^0.10`,
  `nettrine/migrations` `^0.10`, `nettrine/fixtures` `^0.9`, `nettrine/extra` `^0.2`
- Symfony Console 8.0 via `contributte/console` `^0.11`
- QA: `contributte/qa` `^0.4`, `contributte/phpstan` `^0.3` with `phpstan/phpstan-doctrine`, `contributte/tester` `^0.4`
- Docker images: `postgres:15-alpine`, `mariadb:10.10`

## Layout

```
app/Bootstrap.php     # Bootloader::create()->use(NellaPreset::create(__DIR__)); boot() and run()
app/Domain/Database/  # User entity, UserRepository
app/UI/               # @Templates/@layout.latte, BasePresenter, Home/HomePresenter.php, Home/Templates/
bin/console           # CLI entry point
config/               # config.neon (includes doctrine.neon), local.neon.example; local.neon is git-ignored
db/                   # postgres/ and mariadb/ migrations, namespace DB\Migrations
tests/                # Cases/E2E, Cases/Unit, Toolkit
var/                  # tmp/ and log/, created by make setup
www/                  # public web root: index.php, .htaccess
.build/               # phpstan-doctrine.php, boots the container for PHPStan
```

## Configuration

- Load order: the config that `NellaPreset` adds in code (application mapping, error presenter, session, HTTP
  headers, Tracy strict mode), then `config/config.neon`, then `config/local.neon` when the file exists.
- `config/config.neon` holds the `postgres`, `mariadb` and `migration` parameters and the `Europe/Prague` time zone.
  `config/doctrine.neon` registers the console, DBAL, ORM, fixtures and migrations extensions.
- `config/local.neon` is created by `make init` from `config/local.neon.example`, whose lines are all commented out.
- `NETTE_DEBUG=1` enables Tracy. Variables named `NETTE__A__B` become the parameter `a.b` and are added last, in an
  `onCompile` hook. `NETTE_ENV` is not read.

## Data Model

- `App\Domain\Database\User` (table `user`): `id`, `username`, `createdAt`, `updatedAt`, attribute mapping.
- `UserRepository` extends `Nettrine\Extra\Repository\AbstractRepository`.
- Migrations: `db/postgres/Version20241212173845.php` and `db/mariadb/Version20241212192036.php` create the table.
  `nettrine.migrations` reads `db/%migration.db%` with manager `%migration.manager%` (`postgres` / `default` by
  default).
- No fixtures; `nettrine.fixtures` points to `db/Fixtures`, which does not exist.

## Services

- `postgres` (5432): database `demopostgres`, user `contributte` / `contributte`.
- `mariadb` (3306): database `demomariadb`, user `contributte` / `contributte`, root password `contributte`.
- Data lives in `.data/` (git-ignored). Credentials are for local development only.

## Request Flow

- HTTP: `www/index.php` -> `Bootstrap::run()` -> Nella's `RouterFactory` (`<presenter>/<action>`, default
  `Home:default`) -> `App\UI\Home\HomePresenter` -> `app/UI/Home/Templates/default.latte` inside
  `app/UI/@Templates/@layout.latte`. The signals `createUser!` and `deleteUsers!` write to PostgreSQL and redirect.
- Errors: `Nella:Error` presenter from `contributte/nella` when `catchExceptions` is on (production mode).
- CLI: `bin/console` -> `Bootstrap::boot()->createContainer()` -> `Symfony\Component\Console\Application::run()`.

## Build and Deploy

- `make build` only prints `OK`. `make deploy` runs `clean`, `project`, `build`, `clean`.
- Migrations are not part of any target; run them by hand as in `README.md`.
- The demo at `https://examples.contributte.org/doctrine-skeleton/` is deployed outside this repository.

## Testing

- `tests/Cases/Unit/Domain/Database/UserTest.php` - the entity constructor.
- `tests/Cases/E2E/Container/EntrypointTest.php` - the container builds for web and CLI; copies
  `config/local.neon.example` to `config/local.neon` when it is missing.
- `tests/Cases/E2E/Database/MappingTest.php` - `SchemaValidator::validateMapping()` for the autowired manager.
- `tests/Cases/E2E/Latte/LatteTest.php` - every `*.latte` in `app/` compiles.
- CI workflows: `codesniffer`, `phpstan`, `tests`, `coverage`, all on PHP 8.4 and without a database.
- Not tested: queries, migrations, the `second` manager mapping and the rendered HTML.

## Decisions

### 2026-09-28 Boot through contributte/nella

- **Context:** The skeleton should stay small and focus on Doctrine, not on bootstrap code.
- **Decision:** `App\Bootstrap` uses `Bootloader` with `NellaPreset`; routing, error pages and template lookup come
  from `contributte/nella`.
- **Consequences:** (+) two short files in `app/UI`; (-) conventions such as `Templates/` folders and the
  `<presenter>/<action>` route live in a dependency, not in `config/`.

### 2026-09-28 Two entity managers on one mapping

- **Context:** The skeleton shows how to use more than one database.
- **Decision:** Connections `default` (PostgreSQL) and `second` (MariaDB) with managers of the same names, both
  mapping `app/Domain/Database`, each with its own migration folder.
- **Consequences:** (+) one entity class works on both platforms; (-) migrations run once per database, chosen by
  parameters.

## Known Limits

- `config/local.neon.example` is fully commented out, and `config/config.neon` points to database `contributte` on
  `localhost`, while Docker creates `demopostgres` and `demomariadb`. A fresh copy does not connect until the
  example lines are uncommented.
- `NETTE__MIGRATION__*` overrides are added in `onCompile`, outside the container cache key. After the first run the
  cached container in `var/tmp` keeps the old values, so `make clean` is needed between the two migration runs.
- `composer.json` requires `contributte/event-dispatcher`, `composer.lock` pins it to `dev-master`, and no extension
  registers it.
- The Makefile uses the `vendor/bin/codesniffer` and `codefixer` wrappers, `coverage` has no `GITHUB_ACTION`
  switch, and `tests` runs `tests/` instead of `tests/Cases`.
- `db/` is outside the CodeSniffer and PHPStan paths; the generated migrations use four spaces and empty
  descriptions.
- The layout loads Tailwind from `cdn.tailwindcss.com` at runtime, so the page needs internet access to be styled.
- `mariadb:10.10` is a short-term MariaDB release that is out of upstream support.
