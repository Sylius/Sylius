# UPGRADE FROM `v1.5.X` TO `v1.6.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.6.0
```

### Deprecations

The `@SyliusShop/Product/Show/Tabs/_content.html.twig` template is deprecated, product tabs should be rendered with the `sylius.shop.product_tabs` menu instead
