# Doctrine Skeleton PRD

Doctrine Skeleton is a Nette Framework starter project that shows Doctrine ORM with two databases, PostgreSQL and
MariaDB, wired through `nettrine/*` packages.

## Problem

Wiring Doctrine into Nette means choosing DBAL, ORM, migrations and console packages and configuring them together.
Running two connections and two entity managers side by side adds more configuration that is easy to get wrong:
mapping, migration folders and which manager a command uses. A new developer needs a small, working example to copy.

## Users

- Developers who know PHP and basic Nette and want Doctrine ORM in a new application.
- Developers who need a second database connection and want to see how two entity managers are configured.
- Maintainers of `nettrine/*` packages who need a real app to try a release against.

## Goals

- `composer create-project`, `make docker-up` and the values from `config/local.neon.example` give an app that
  connects to both databases.
- The home page lists users from both databases and creates and deletes users in PostgreSQL.
- Each database has its own migration folder, and one command migrates each of them.
- `make qa` and `make tests` pass on a fresh copy without a database.
- The code fits in a few files, so a developer reads it in one sitting.

## Non-goals

- Not a full web application - there is no sign-in, admin, forms or mailing; `webapp-skeleton` covers those.
- No replication, sharding or cross-database transactions - the two managers are independent.
- No PHP container - Docker Compose runs only the databases, so the app runs on the host PHP.
- No frontend build - the layout loads Tailwind from its CDN, so there is nothing to compile.

## Scope

Database:

- Two DBAL connections, `default` (PostgreSQL, `pdo_pgsql`) and `second` (MariaDB, `mysqli`), in
  `config/doctrine.neon`.
- Two entity managers with attribute mapping of `app/Domain/Database`.
- `User` entity with `id`, `username`, `createdAt`, `updatedAt` and a `UserRepository` based on `nettrine/extra`.
- One migration per database in `db/postgres` and `db/mariadb`, selected by `migration.db` and
  `migration.manager`.

Application:

- As a developer, I can open the home page and see users from both databases in one table.
- As a developer, I can create a random user and delete all users in PostgreSQL with one click.
- As a developer, I can see the SQL of both connections in the Tracy bar in debug mode.

Console:

- `bin/console` with the Doctrine, DBAL and migrations commands.

QA:

- CodeSniffer, PHPStan level 9 with `phpstan-doctrine`, and Nette Tester E2E tests that build the container, validate
  the mapping and compile every Latte template.

## Success Criteria

- `make init tests`, `make init phpstan` and `make init coverage` pass in CI on PHP 8.4.
- With both databases running and migrated, `http://localhost:8080` renders the users table.
- `bin/console migrations:migrate` creates the `user` table in PostgreSQL, and the MariaDB command in `README.md`
  creates it in MariaDB.
- The demo at `https://examples.contributte.org/doctrine-skeleton/` renders the home page.

## Out of Scope

- Per-package configuration - see the `.docs/README.md` of `nettrine/dbal`, `nettrine/orm` and
  `nettrine/migrations`.
- A complete application with UI modules, sign-in and fixtures - see `contributte/webapp-skeleton`.
- Doctrine behaviour extensions via `nettrine/extensions-atlantic18` - see `contributte/doctrine-extra-skeleton`.
- Messaging and queues - see `contributte/messenger-skeleton`.

## Open Questions

- 2026-09-28: Should `config/local.neon.example` ship uncommented values that match `docker-compose.yml`?
- 2026-09-28: Keep `contributte/event-dispatcher` in `composer.json` although no extension registers it?
- 2026-09-28: Keep the `nettrine.fixtures` extension although `db/Fixtures` does not exist?
