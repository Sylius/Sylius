# How to use B2B Suite without Elasticsearch?

In some projects, you may want to use the B2B Suite features without Elasticsearch. This guide walks you through the necessary steps to remove the dependency on Elasticsearch while retaining core B2B functionality.

## 1. Remove `bitbag/elasticsearch-plugin` dependency

In your `composer.json`, add the following to the `replace` section:

```json
"replace": {
    "bitbag/elasticsearch-plugin": "*"
}
```

Then run:

```bash
composer update
```

## 2. Remove Elasticsearch configuration

In `config/packages/_sylius.yaml`, remove the following line if it exists:

```diff
- { resource: "@BitBagSyliusElasticsearchPlugin/config/config.yml" }
```

## 3. Update B2B routing

Replace the entire content of `config/routes/sylius_b2b_suite.yaml` with the following:

```yaml
sylius_admin_order_creation_admin:
    resource: "@SyliusB2BKitPlugin/config/routes/admin_order_management/routes.yaml"
    prefix: '/%sylius_admin.path_name%'

sylius_quick_shopping:
    resource: "@SyliusB2BKitPlugin/config/routes/quick_shopping/routes.yaml"

sylius_organization_plugin_shop:
    resource: "@SyliusB2BKitPlugin/config/routes/organization/shop_routing.yaml"
    prefix: /{_locale}
    requirements:
        locale: ^[a-z]{2}(?:[A-Z]{2})?$

sylius_organization_plugin_admin:
    resource: "@SyliusB2BKitPlugin/config/routes/organization/admin_routing.yaml"
    prefix: '/%sylius_admin.path_name%'

sylius_pricing_lists_plugin_admin:
    resource: "@SyliusB2BKitPlugin/config/routes/pricing_lists/admin_routing.yaml"
    prefix: '/%sylius_admin.path_name%'
```

## 4. Update Shop asset entrypoint

In `assets/shop/entrypoint.js`, replace the default B2B import with the following SCSS imports:

```diff
- import '@vendor/sylius/b2b-kit/assets/shop/entrypoint';
+ import '@vendor/sylius/b2b-kit/assets/shop/scss/quick_shopping/main.scss';
+ import '@vendor/sylius/b2b-kit/assets/shop/scss/shop/main.scss';
```

## 5. Remove unused Elasticsearch services

Create a compiler pass in `src/DependencyInjection/Compiler/RemoveB2BElasticsearchServicesCompilerPass.php`:

```php
<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveB2BElasticsearchServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $queryBuilderServices = [
            'sylius_b2b.elastica.query_builder.site_wide_products',
            'sylius_b2b.elastica.query_builder.taxon_products',
            'sylius_b2b.elastica.query_builder.no_customer_group_or_organization',
            'sylius_b2b.elastica.query_builder.has_customer_group',
            'sylius_b2b.elastica.query_builder.has_organization',
            'sylius_b2b.elastica.query_builder.customer_group_and_organization',
        ];

        $propertyBuilderServices = [
            'sylius_b2b.elastica.property_builder.product_customer_groups',
            'sylius_b2b.elastica.property_builder.product_organizations',
        ];

        $allServicesToRemove = array_merge($queryBuilderServices, $propertyBuilderServices);

        foreach ($allServicesToRemove as $serviceId) {
            if ($container->hasDefinition($serviceId)) {
                $container->removeDefinition($serviceId);
            }

            if ($container->hasAlias($serviceId)) {
                $container->removeAlias($serviceId);
            }
        }
    }
}
```

## 6. Register the Compiler Pass

In your `src/Kernel.php`, register the compiler pass:

```php
protected function build(ContainerBuilder $container): void
{
    parent::build($container);

    $container->addCompilerPass(new \App\DependencyInjection\Compiler\RemoveB2BElasticsearchServicesCompilerPass());
}
```

## 7. Final Steps

Clear the cache and rebuild frontend assets:

```bash
php bin/console cache:clear
yarn build
```

### ✅ Done!

The B2B Suite will now function without Elasticsearch, retaining other core features and enabling you to implement an alternative search engine tool.
