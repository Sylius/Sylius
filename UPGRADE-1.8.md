# UPGRADE FROM `v1.8.4` TO `v1.8.6`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.8.3` TO `v1.8.4`

1. The `sylius:cancel-unpaid-orders` command now continues to proceed even if an error occurred. The behavior here is now normal but it leads to a few issues for an upgrade:

    - Product variants with `on_hold = on_hand` will become available as soon as the uncancelled orders are now cancelled. This could lead to a lot of products on sale which were not previously on sale. This behavior is normal but the Merchant could be used to the previous behavior and could have acted around it.  
      **Before** you upgrade on production you may want to verify the stocks of these products variants:
    ```sql
    SELECT code FROM sylius_product_variant WHERE on_hand = on_hold; -- These products will go back on "available" and may be sold
    ```
    - If the merchant is used to this (now gone) bug, then they should be careful about this situation as well: if they worked with the issue and considered that increasing the `on_hand` value to sale more since some on hand stocks were locked by the `on_hold` items, they may be in the situation of having more items on hand than the reality.

# UPGRADE FROM `v1.7.X` TO `v1.8.0`

1. All consts classes has been changed from final classes to interfaces. As a result initialization of `\Sylius\Bundle\UserBundle\UserEvents` is not longer possible. The whole list of changed classes can be found [here](https://github.com/Sylius/Sylius/pull/11347).

1. Service alias `Sylius\Component\Channel\Context\ChannelContextInterface` was changed from `sylius.context.channel.composite` to `sylius.context.channel`.
   The later is being decorated by `sylius.context.channel.cached` which caches the channel per request and reduces the amount of database queries.

1. A serialization group has been added to the route `sylius_admin_ajax_product_index` to avoid an infinite loop, or a
   time out during this ajax request (previously no serialization group was defined on this route).

1. We now use the parameter `sylius_admin.path_name` to retrieve the admin routes prefix. If you used the `/admin` prefix
   in some admin URLs you can now replace `/admin` by `/%sylius_admin.path_name%`.  
   Also the route is now dynamic. You can change the `SYLIUS_ADMIN_ROUTING_PATH_NAME` environment variable to custom the admin's URL.

## Special attention

### Migrations

As we switched to the `3.0` version of Doctrine Migrations, there are some things that need to be changed in the application that is upgraded to Sylius 1.8:

1. Replace the DoctrineMigrationsBundle configuration in `config/packages/doctrine_migrations.yaml`:

   ```
   doctrine_migrations:
   -    dir_name: "%kernel.project_dir%/src/Migrations"
   -
   -    # Namespace is arbitrary but should be different from App\Migrations as migrations classes should NOT be autoloaded
   -    namespace: DoctrineMigrations
   +    storage:
   +        table_storage:
   +            table_name: sylius_migrations
   +    migrations_paths:
   +        'DoctrineMigrations': '%kernel.project_dir%/src/Migrations'
   ``` 

1. Remove all the legacy Sylius-Standard migrations (they're not needed anymore)

   ```bash
   rm "src/Migrations/Version20170912085504.php"
   #...
   ```

1. Mark all migrations from **@SyliusCoreBundle** and **@SyliusAdminApiBundle** as executed
   (they're the same as legacy ones, just not recognized by the doctrine _yet_)

   ```bash
   bin/console doctrine:migrations:version "Sylius\Bundle\CoreBundle\Migrations\Version20161202011555" --add --no-interaction
   #...
   ```

   > BEWARE!

   If you're using some Sylius plugins you'll probably need to handle their migrations as well.
   You can either leave them in your `src/Migrations/` catalog and treat as _your own_ or use the migrations from the vendors.
   However, it assumes that plugin provides a proper integration with Doctrine Migrations 3.0 - as, for example,
   [Invoicing Plugin](https://github.com/Sylius/InvoicingPlugin/blob/master/src/DependencyInjection/SyliusInvoicingExtension.php#L33).

   > TIP

   Take a look at [etc/migrations-1.8.sh](etc/migrations-1.8.sh) script - it would execute points 2) and 3) automatically.

1. Do the same for your own migrations. Assuming your migrations namespace is `DoctrineMigrations` and
   migrations catalog is `src/Migrations`, you can run such a script:

    ```bash
    #!/bin/bash
   
    for file in $(ls -1 src/Migrations/ | sed -e 's/\..*$//')
    do
        bin/console doctrine:migrations:version "DoctrineMigrations\\${file}" --add --no-interaction
    done
    ```

1. Run `bin/console doctrine:migrations:migrate` to apply migrations added in Sylius 1.8

#### Re-enable product variants and taxons

In the migrations of the 1.8 we add a flag `enabled` on the product variants and the taxons. This flag is on `0` by default, which leads to an empty catalog and some errors due to products without enabled variants.

To resolve this, you should run these two SQL requests on production:

```sql
UPDATE sylius_taxon SET enabled = 1;
UPDATE sylius_product_variant SET enabled = 1;
```

This could also be done in a Migration as well if you prefer. You can create an empty migration (then you can fill it!) like this:

```bash
./bin/console doctrine:migrations:generate
```


### Translations

Some translations have changed, you may want to search for them in your project:

- `sylius.email.shipment_confirmation.tracking_code` has been removed.
- `sylius.email.shipment_confirmation.you_can_check_its_location` has been removed.
- `sylius.email.shipment_confirmation.you_can_check_its_location_with_the_tracking_code` has been added instead of the two above.

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.8.md).
