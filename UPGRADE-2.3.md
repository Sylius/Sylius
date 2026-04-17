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

## Grid providers are now configurable

`GridProviders` are now configurable, allowing users to choose between PHP and YAML for Sylius grid configurations. You can now change the definition format Sylius will use. Note that configurations in different formats cannot be merged, so if you switch to PHP for an existing grid, you will need to recreate the vendor's YAML definition.

Example configuration:

```yaml
sylius_core:
    grid:
        default_type: array # from the config directory (yaml or php)
        grids:
            sylius_admin_product_variant:
                use_legacy_config: true # from src directory, these are services tagged as "sylius.grid"
```
