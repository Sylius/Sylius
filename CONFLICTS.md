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

- `doctrine/orm:>=3.7`:

  3.7.0 extends `SchemaValidator` with a check verifying that the inverse side of an association points back at the
  owning side entity. `Sylius\Component\Shipping\Model\ShipmentUnit` is a mapped superclass with no table that
  nothing in the Core stack extends, and its `shipment` association names `Shipment#units` as the inverse side,
  while in that stack the inverse side belongs to `Sylius\Component\Core\Model\OrderItemUnit`. Since that release
  every project running `doctrine:schema:validate` on Sylius gets a mapping error, even though nothing is broken at
  runtime and no query touches that class.

  Unlike the other entries here this is not an upstream regression but an intended new check, so the conflict is
  ours to remove: it holds the integration on 3.6.x until the unused mapping stops being loaded.

  References: https://github.com/doctrine/orm/pull/12460
