# UPGRADE FROM `2.0` TO `2.1`

1. The `sylius_admin_customer_orders_statistics` route has been deprecated.

## Grid providers are now configurable
`GridProviders` are now configurable, allowing users to choose between PHP and YAML for Sylius grid configurations. You can now change the definition format Sylius will use. Note that configurations in different formats cannot be merged, so if you switch to PHP for an existing grid, you will need to recreate the vendor's YAML definition.

Example configuration:

```yaml
sylius_core:
    grid:
        default_type: array # from the config directory (yaml or php)
        grids:
            sylius_admin_product_variant:
                type: 'service' # from src directory, these are services tagged as "sylius.grid"
```
