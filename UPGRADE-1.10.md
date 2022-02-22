# UPGRADE FROM `v1.10.8` TO `v1.10.10`

1. Field `byGuest` has been added to `Sylius\Component\Core\Model\Order`, this change will allow us to distinguish carts 
between guests and logged in customers.

2. Not passing `tokenStorage` through constructor in `Sylius\Component\Core\Cart\Context\ShopBasedCartContext` is deprecated
in Sylius 1.10.9 and it will be removed in Sylius 2.0.

# UPGRADE FROM `v1.10.x` TO `v1.10.8`

1. Update `payum/payum` to `^1.7` and execute Doctrine Migrations

If `payum/payum` is a root requirement (in the project's `composer.json`), then run:

```shell
composer require payum/payum:^1.7
```

otherwise, run:

```shell
composer update payum/payum
```

then execute the migrations:

```shell
bin/console doctrine:migrations:migrate
```

# UPGRADE FROM `v1.10.0` TO `v1.10.1`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.9.X` TO `v1.10.0`

### Admin API Bundle Removal

Sylius v1.10 extracts AdminApiBundle outside the core package. You might choose either to keep that bundle or remove it in case it's not used.

#### Keeping Admin API Bundle

1. Add Admin API Bundle to your application by running the following command:

```
composer require sylius/admin-api-bundle
```

#### Removing Admin API Bundle

1. **Before installing Sylius 1.10**, run the following command to adjust the database schema:

```
bin/console doctrine:migrations:execute Sylius\\Bundle\\AdminApiBundle\\Migrations\\Version20161202011556 Sylius\\Bundle\\AdminApiBundle\\Migrations\\Version20170313125424 Sylius\\Bundle\\AdminApiBundle\\Migrations\\Version20170711151342 --down
```

1. After installing Sylius v1.10, remove the remaining configuration by following the changes in [this PR](https://github.com/Sylius/Sylius-Standard/pull/543/files):

- remove `friendsofsymfony/oauth-server-bundle` from your `composer.json` and run `composer update`
- remove `FOS\OAuthServerBundle\FOSOAuthServerBundle` and `Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle` from `config/bundles.php`
- remove `@SyliusAdminApiBundle/Resources/config/app/config.yml` import from `config/packages/_sylius.yaml`
- remove `sylius_admin_api` package configuration from `config/packages/_sylius.yaml`
- remove `oauth_token` and `api` firewalls from `config/security.yaml`
- remove `sylius.security.api_regex` parameter and all its usage in access control from `config/security.yaml`
- remove `config/routes/sylius_admin_api.yaml` file
- remove all classes from `src/Entity/AdminApi` directory

### Buses

1. Message buses `sylius_default.bus` and `sylius_event.bus` has been deprecated. Use `sylius.command_bus` and `sylius.event_bus` instead.

### Shop & Core Decoupled

1. `Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener` has been moved from CoreBundle to ShopBundle, renamed to `Sylius\Bundle\ShopBundle\EventListener\ShopCartBlamerListener` and adjusted to work properly when decoupled.

1. `Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener` has been moved from CoreBundle to ShopBundle as `Sylius\Bundle\ShopBundle\EventListener\UserCartRecalculationListener` and adjusted to work properly when decoupled.

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.10.md).
