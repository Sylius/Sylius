# UPGRADE FROM `2.2` TO `2.3`

## Configuration

1. The default value of `sylius_core.order_by_identifier` has been changed from `true` to `false`. ([#18956](https://github.com/Sylius/Sylius/pull/18956))

   The `OrderByIdentifierSqlWalker` is no longer enabled by default.
   If your application relies on ordering by identifier, enable it explicitly in your configuration:

   ```yaml
   sylius_core:
       order_by_identifier: true
   ```

## Dependencies

1. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.

   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.
