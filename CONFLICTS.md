# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and references related issues.

- `doctrine/orm:2.20.7`:

  This version contains a regression that breaks queries with empty arrays, causing SQL syntax errors when methods like `EntityRepository::findById([])` are called with an empty array.
  This leads to invalid SQL queries like `WHERE t0.id IN ()`.
  The same regression affects 3.5.3, but that release is already excluded by the minimum requirement `^3.6`, so only 2.20.7 (reachable through `^2.20`) needs to be listed here.

  References: https://github.com/doctrine/orm/issues/12245

- `doctrine/orm:3.6.8`:

  This version adds `GenerateSchemaEventArgs::setSchema()`, which throws a `BadMethodCallException` unless
  `doctrine/dbal` provides the `Schema::edit()` API. That API requires `doctrine/dbal` ^4.5, which has not been
  released yet. The `SchemaListener` classes shipped with `symfony/doctrine-bridge` guard the call only with
  `method_exists($event, 'setSchema')`, so starting from this version they always hit the exception and every
  `doctrine:schema:create`, `doctrine:schema:update` and `doctrine:migrations:diff` call fails.

  References: https://github.com/doctrine/orm/issues/12547
